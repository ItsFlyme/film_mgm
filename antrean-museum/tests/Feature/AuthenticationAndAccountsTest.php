<?php

namespace Tests\Feature;

use App\Models\Queue;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_has_no_registration_option(): void
    {
        $this->withoutVite();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk Sistem Teater')
            ->assertDontSee('Daftar akun');
    }

    public function test_login_accepts_username_remembers_session_and_redirects_to_kiosk(): void
    {
        $user = User::factory()->create([
            'username' => 'operator',
            'role' => 'admin',
            'password' => 'staff-password',
        ]);

        $this->post(route('login.store'), [
            'username' => 'operator',
            'password' => 'staff-password',
            'remember' => '1',
        ])->assertRedirect(route('queue.kiosk'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_invalid_credentials_do_not_authenticate(): void
    {
        User::factory()->create(['username' => 'operator']);

        $this->from(route('login'))->post(route('login.store'), [
            'username' => 'operator',
            'password' => 'wrong-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_logout_ends_the_authenticated_session(): void
    {
        $this->actingAs(User::factory()->create(['username' => 'operator']))
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_tv_and_its_display_feed_remain_public_but_kiosk_and_admin_require_authentication(): void
    {
        Queue::create([
            'queue_number' => 'A-001',
            'visitor_name' => 'Pengunjung',
            'institution_name' => 'Sekolah',
            'status' => 'waiting',
        ]);

        $this->get(route('queue.index'))->assertOk();
        $this->getJson(route('queue.data'))
            ->assertOk()
            ->assertJsonPath('waitingQueues.0.queue_number', 'A-001')
            ->assertJsonMissingPath('stats');
        $this->get(route('queue.kiosk'))->assertRedirect(route('login'));
        $this->get(route('queue.admin'))->assertRedirect(route('login'));
        $this->post(route('queue.store'), [])->assertRedirect(route('login'));
    }

    public function test_admin_user_can_use_queue_pages_but_cannot_manage_accounts(): void
    {
        $admin = User::factory()->create(['username' => 'operator', 'role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('queue.kiosk'))->assertOk();
        $this->get(route('queue.admin'))
            ->assertOk()
            ->assertSee('Logout')
            ->assertDontSee('Manajemen akun');
        $this->getJson(route('queue.admin.data'))->assertOk();
        $this->get(route('queue.accounts.index'))->assertForbidden();
    }

    public function test_super_admin_can_create_update_and_delete_staff_accounts(): void
    {
        $superAdmin = User::factory()->create(['username' => 'root', 'role' => 'super_admin']);
        $this->actingAs($superAdmin);

        $this->get(route('queue.admin'))
            ->assertOk()
            ->assertSee('root')
            ->assertSee('Super Admin')
            ->assertSee('Manajemen akun');
        $this->get(route('queue.accounts.index'))->assertOk();
        $this->post(route('queue.accounts.store'), [
            'username' => 'new-operator',
            'role' => 'admin',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('queue.accounts.index'));

        $staff = User::where('username', 'new-operator')->firstOrFail();
        $this->assertTrue(Hash::check('new-password', $staff->password));

        $this->patch(route('queue.accounts.update', $staff), [
            'username' => 'updated-operator',
            'role' => 'admin',
            'password' => 'updated-password',
            'password_confirmation' => 'updated-password',
        ])->assertRedirect(route('queue.accounts.index'));

        $this->assertDatabaseHas('users', ['id' => $staff->id, 'username' => 'updated-operator']);
        $this->assertTrue(Hash::check('updated-password', $staff->fresh()->password));

        $this->delete(route('queue.accounts.destroy', $staff))
            ->assertRedirect(route('queue.accounts.index'));
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    public function test_super_admin_cannot_delete_own_account_or_remove_last_super_admin(): void
    {
        $superAdmin = User::factory()->create(['username' => 'root', 'role' => 'super_admin']);
        $this->actingAs($superAdmin);

        $this->delete(route('queue.accounts.destroy', $superAdmin))
            ->assertSessionHasErrors('account');
        $this->patch(route('queue.accounts.update', $superAdmin), [
            'username' => 'root',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id, 'role' => 'super_admin']);
    }

    public function test_seeder_creates_the_two_requested_roles_with_hashed_passwords(): void
    {
        $this->seed(UserSeeder::class);

        $admin = User::where('username', 'admin')->firstOrFail();
        $superAdmin = User::where('username', 'superadmin')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('admin123', $admin->password));
        $this->assertSame('super_admin', $superAdmin->role);
        $this->assertTrue(Hash::check('superadmin123', $superAdmin->password));
    }
}
