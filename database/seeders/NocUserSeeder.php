<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class NocUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample NOC user
        User::firstOrCreate(
            ['email' => 'noc@example.com'],
            [
                'name' => 'NOC User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_NOC,
            ]
        );

        $this->command->info('NOC user created successfully!');
        $this->command->info('Email: noc@example.com');
        $this->command->info('Password: password');
        $this->command->info('Role: NOC');
    }
}
