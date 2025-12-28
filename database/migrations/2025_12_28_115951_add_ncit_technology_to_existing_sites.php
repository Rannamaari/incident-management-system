<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Site;
use App\Models\SiteTechnology;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all existing sites
        $sites = Site::all();

        foreach ($sites as $site) {
            // Check if NCIT already exists for this site
            $existingNcit = SiteTechnology::where('site_id', $site->id)
                ->where('technology', 'NCIT')
                ->first();

            // Only add if it doesn't exist
            if (!$existingNcit) {
                SiteTechnology::create([
                    'site_id' => $site->id,
                    'technology' => 'NCIT',
                    'is_active' => false, // Default to inactive
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove NCIT technology from all sites
        SiteTechnology::where('technology', 'NCIT')->delete();
    }
};
