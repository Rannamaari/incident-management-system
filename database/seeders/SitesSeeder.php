<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Location;
use App\Models\Site;
use App\Models\SiteTechnology;
use Illuminate\Support\Facades\DB;

class SitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET CONSTRAINTS ALL DEFERRED');
        SiteTechnology::truncate();
        Site::truncate();
        Location::truncate();
        Region::truncate();

        $this->command->info('Importing regions...');
        $this->importRegions();

        $this->command->info('Importing locations...');
        $this->importLocations();

        $this->command->info('Importing sites...');
        $this->importSites();

        $this->command->info('Creating site technologies (2G, 3G, 4G, 5G, ILL, SIP, IPTV)...');
        $this->createSiteTechnologies();

        $this->command->info('Sites import completed successfully!');
    }

    private function importRegions(): void
    {
        $csvFile = public_path('regions.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("regions.csv not found in public folder!");
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file); // Skip header row

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 2) {
                Region::create([
                    'code' => $row[0],
                    'name' => $row[1],
                ]);
            }
        }

        fclose($file);
        $this->command->info('Regions imported: ' . Region::count());
    }

    private function importLocations(): void
    {
        $csvFile = public_path('locations.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("locations.csv not found in public folder!");
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file); // Skip header row

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 3) {
                $region = Region::where('code', $row[0])->first();

                if ($region) {
                    Location::create([
                        'region_id' => $region->id,
                        'location_key' => $row[1],
                        'location_name' => $row[2],
                    ]);
                }
            }
        }

        fclose($file);
        $this->command->info('Locations imported: ' . Location::count());
    }

    private function importSites(): void
    {
        $csvFile = public_path('sites.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("sites.csv not found in public folder!");
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file); // Skip header row

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 5) {
                $region = Region::where('code', $row[0])->first();

                if ($region) {
                    $location = Location::where('region_id', $region->id)
                        ->where('location_key', $row[1])
                        ->first();

                    if ($location) {
                        Site::create([
                            'region_id' => $region->id,
                            'location_id' => $location->id,
                            'site_number' => $row[2],
                            'site_code' => $row[3],
                            'display_name' => $row[4],
                            'is_active' => true,
                            // has_fbb can be specified in column 6, or defaults to false
                            'has_fbb' => isset($row[5]) ? filter_var($row[5], FILTER_VALIDATE_BOOLEAN) : false,
                        ]);
                    }
                }
            }
        }

        fclose($file);
        $this->command->info('Sites imported: ' . Site::count());
    }

    private function createSiteTechnologies(): void
    {
        // All available technologies
        $cellularTechs = ['2G', '3G', '4G', '5G'];
        $otherServices = ['ILL', 'SIP', 'IPTV'];
        $sites = Site::all();

        foreach ($sites as $site) {
            // Create cellular technologies (all active by default)
            foreach ($cellularTechs as $technology) {
                SiteTechnology::create([
                    'site_id' => $site->id,
                    'technology' => $technology,
                    'is_active' => true,
                ]);
            }

            // Create other services (inactive by default)
            foreach ($otherServices as $technology) {
                SiteTechnology::create([
                    'site_id' => $site->id,
                    'technology' => $technology,
                    'is_active' => false,
                ]);
            }
        }

        $this->command->info('Site technologies created: ' . SiteTechnology::count());
    }
}
