<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResolutionTeam;

class ResolutionTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            'GMR',
            'Kulhuduffushi Team',
            'EydhaFushi',
            'Mulah',
            'Central Team',
            'South Team',
        ];

        foreach ($teams as $team) {
            ResolutionTeam::firstOrCreate(['name' => $team]);
        }

        $this->command->info('Resolution teams seeded successfully.');
    }
}
