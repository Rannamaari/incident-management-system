<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FbbIsland;
use App\Models\Region;

class FbbIslandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = public_path('fbb_islands.txt');

        if (!file_exists($filePath)) {
            $this->command->error('fbb_islands.txt file not found in public directory!');
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $imported = 0;
        $skipped = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Parse the line: Region-IslandName-Technology
            $parts = explode('-', $line);

            if (count($parts) < 3) {
                $this->command->warn("Skipping invalid line: {$line}");
                $skipped++;
                continue;
            }

            $regionCode = array_shift($parts); // First part is region code
            $technology = array_pop($parts); // Last part is technology
            $islandName = implode('-', $parts); // Middle parts are island name (in case island has dash)

            // Find region by code
            $region = Region::where('code', $regionCode)->first();

            if (!$region) {
                $this->command->warn("Region not found for code: {$regionCode}");
                $skipped++;
                continue;
            }

            // Create or update FBB island
            FbbIsland::updateOrCreate(
                [
                    'region_id' => $region->id,
                    'island_name' => $islandName,
                ],
                [
                    'technology' => $technology,
                    'is_active' => true,
                ]
            );

            $imported++;
        }

        $this->command->info("FBB Islands import completed!");
        $this->command->info("Imported: {$imported}");
        $this->command->info("Skipped: {$skipped}");
    }
}
