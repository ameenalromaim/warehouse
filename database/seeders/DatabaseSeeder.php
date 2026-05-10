<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['phone' => '771738225'],
            [
                'name' => 'مستخدم تجريبي',
                'role' => User::ROLE_SUPER_ADMIN,
                'type_location' => null,
                'email' => '771738225@dashboard.local',
                'password' => Hash::make('password'),
            ]
        );
    }
}
