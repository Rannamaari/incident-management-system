<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@incident.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin2025#Secure'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Create Editor User
        User::firstOrCreate(
            ['email' => 'editor@incident.com'],
            [
                'name' => 'Incident Editor',
                'password' => Hash::make('Editor2025#System'),
                'role' => User::ROLE_EDITOR,
            ]
        );

        // Create Viewer User
        User::firstOrCreate(
            ['email' => 'viewer@incident.com'],
            [
                'name' => 'Incident Viewer',
                'password' => Hash::make('Viewer2025#Access'),
                'role' => User::ROLE_VIEWER,
            ]
        );
    }
}
