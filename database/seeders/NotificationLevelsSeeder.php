<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationLevel;

class NotificationLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Level 1 - High severity only
        NotificationLevel::create([
            'name' => 'Level 1',
            'description' => 'Critical Alerts - High severity incidents only',
            'severities' => ['High'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Create Level 2 - Medium and High severity
        NotificationLevel::create([
            'name' => 'Level 2',
            'description' => 'Standard Alerts - Medium and High severity incidents',
            'severities' => ['Medium', 'High'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Create Level 3 - All severity levels
        NotificationLevel::create([
            'name' => 'Level 3',
            'description' => 'All Alerts - Low, Medium, and High severity incidents',
            'severities' => ['Low', 'Medium', 'High'],
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
