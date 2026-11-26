<?php

namespace Tests\Feature;

use App\Exports\QueueHistoryWorkbook;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class QueueRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['username' => 'operator']));
    }

    public function test_kiosk_saves_group_category_and_institution(): void
    {
        $response = $this->post(route('queue.store'), [
            'visitor_type' => 'group',
            'visitor_name' => 'Rina',
            'institution_name' => 'SMP Negeri 1',
            'movie_title' => 'Jejak Sejarah Nusantara',
            'session_time' => '10:00 WIB',
        ]);

        $response->assertRedirect(route('queue.kiosk'))
            ->assertSessionHas('success', 'A-001');
        $this->assertDatabaseHas('queues', [
            'queue_number' => 'A-001',
            'visitor_type' => 'group',
            'institution_name' => 'SMP Negeri 1',
        ]);
    }

    public function test_same_institution_cannot_register_for_the_same_session_twice(): void
    {
        Queue::create([
            'queue_number' => 'A-001',
            'visitor_name' => 'Rina',
            'visitor_type' => 'group',
            'institution_name' => 'SMP Negeri 1',
            'movie_title' => 'Jejak Sejarah Nusantara',
            'session_time' => '10:00 WIB',
            'status' => 'waiting',
        ]);

        $response = $this->from(route('queue.kiosk'))->post(route('queue.store'), [
            'visitor_type' => 'group',
            'visitor_name' => 'Dewi',
            'institution_name' => 'smp negeri 1',
            'movie_title' => 'Eksplorasi Gunung Merapi',
            'session_time' => '10:00 WIB',
        ]);

        $response->assertRedirect(route('queue.kiosk'))
            ->assertSessionHas('error');
        $this->assertDatabaseCount('queues', 1);
    }

    public function test_different_institutions_can_register_for_the_same_session(): void
    {
        Queue::create([
            'queue_number' => 'A-001',
            'visitor_name' => 'Rina',
            'visitor_type' => 'group',
            'institution_name' => 'SMP Negeri 1',
            'movie_title' => 'Jejak Sejarah Nusantara',
            'session_time' => '10:00 WIB',
            'status' => 'waiting',
        ]);

        $this->post(route('queue.store'), [
            'visitor_type' => 'group',
            'visitor_name' => 'Dewi',
            'institution_name' => 'SMP Negeri 2',
            'movie_title' => 'Jejak Sejarah Nusantara',
            'session_time' => '10:00 WIB',
        ])->assertRedirect(route('queue.kiosk'))
            ->assertSessionHas('success', 'A-002');

        $this->assertDatabaseCount('queues', 2);
    }

    public function test_statistics_count_individual_and_group_registrations(): void
    {
        Queue::create([
            'queue_number' => 'A-001',
            'visitor_name' => 'Ayu',
            'visitor_type' => 'individual',
        ]);
        Queue::create([
            'queue_number' => 'A-002',
            'visitor_name' => 'Rina',
            'visitor_type' => 'group',
            'institution_name' => 'SMP Negeri 1',
        ]);

        $this->getJson(route('queue.admin.data'))
            ->assertOk()
            ->assertJsonPath('stats.today.total', 2)
            ->assertJsonPath('stats.today.individual', 1)
            ->assertJsonPath('stats.today.group', 1);
    }

    public function test_today_statistics_use_the_jakarta_calendar_day(): void
    {
        $localMidnightUtc = Carbon::now('Asia/Jakarta')->startOfDay()->utc();

        $previousDayQueue = Queue::create(['queue_number' => 'A-001', 'visitor_name' => 'Hari lalu']);
        $previousDayQueue->created_at = $localMidnightUtc->copy()->subSecond();
        $previousDayQueue->save();

        $todayQueue = Queue::create(['queue_number' => 'A-002', 'visitor_name' => 'Hari ini']);
        $todayQueue->created_at = $localMidnightUtc->copy()->addSecond();
        $todayQueue->save();

        $this->getJson(route('queue.admin.data'))
            ->assertOk()
            ->assertJsonCount(1, 'queues')
            ->assertJsonPath('queues.0.queue_number', 'A-002')
            ->assertJsonPath('stats.today.total', 1);
    }

    public function test_history_export_returns_an_xlsx_download(): void
    {
        $this->get(route('queue.history.export'))
            ->assertDownload('riwayat-antrean-'.Carbon::now('Asia/Jakarta')->format('Y-m-d').'.xlsx');
    }

    public function test_export_workbook_is_a_valid_xlsx_archive(): void
    {
        Queue::create([
            'queue_number' => 'A-001',
            'visitor_name' => 'Rina',
            'visitor_type' => 'group',
            'institution_name' => 'Museum & Sejarah',
        ]);

        $path = QueueHistoryWorkbook::create(Queue::all());
        $archive = new \ZipArchive;

        try {
            $this->assertTrue($archive->open($path) === true);
            $worksheet = $archive->getFromName('xl/worksheets/sheet1.xml');
            $this->assertNotFalse($worksheet);
            $this->assertNotFalse(simplexml_load_string($worksheet));
            $this->assertStringContainsString('Museum &amp; Sejarah', $worksheet);
        } finally {
            $archive->close();
            unlink($path);
        }
    }

    public function test_monitor_admin_and_kiosk_pages_render(): void
    {
        $this->get(route('queue.index'))->assertOk();
        $this->get(route('queue.admin'))->assertOk();
        $this->get(route('queue.kiosk'))->assertOk();
        $this->get(route('queue.history'))->assertOk();
    }

    public function test_history_only_includes_the_last_thirty_calendar_days(): void
    {
        $oldQueue = Queue::create([
            'queue_number' => 'A-001',
            'visitor_name' => 'Pengunjung Lama',
        ]);
        $oldQueue->created_at = today()->subDays(31);
        $oldQueue->save();
        Queue::create([
            'queue_number' => 'A-002',
            'visitor_name' => 'Pengunjung Baru',
        ]);

        $this->get(route('queue.history'))
            ->assertOk()
            ->assertSee('A-002')
            ->assertDontSee('A-001');
    }
}
