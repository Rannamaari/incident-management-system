<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the check constraint first
        DB::statement("ALTER TABLE site_technologies DROP CONSTRAINT IF EXISTS site_technologies_technology_check");

        // Change technology column from enum to varchar to support additional technologies
        DB::statement("ALTER TABLE site_technologies ALTER COLUMN technology TYPE VARCHAR(50)");

        // Add ILL, SIP, IPTV technologies to all existing sites (disabled by default)
        $sites = DB::table('sites')->pluck('id');
        $newTechnologies = ['ILL', 'SIP', 'IPTV'];

        foreach ($sites as $siteId) {
            foreach ($newTechnologies as $tech) {
                // Check if it doesn't already exist
                $exists = DB::table('site_technologies')
                    ->where('site_id', $siteId)
                    ->where('technology', $tech)
                    ->exists();

                if (!$exists) {
                    DB::table('site_technologies')->insert([
                        'site_id' => $siteId,
                        'technology' => $tech,
                        'is_active' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the new technologies
        DB::table('site_technologies')
            ->whereIn('technology', ['ILL', 'SIP', 'IPTV'])
            ->delete();

        // Revert to enum (this is complex in PostgreSQL, so we'll just leave as varchar)
        // In a real rollback scenario, you'd need to handle this more carefully
    }
};
