<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'email' => 'admin@staff.local',
                'role' => 'admin',
                'password' => 'admin123',
            ],
        );

        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@staff.local',
                'role' => 'super_admin',
                'password' => 'superadmin123',
            ],
        );
    }
}
