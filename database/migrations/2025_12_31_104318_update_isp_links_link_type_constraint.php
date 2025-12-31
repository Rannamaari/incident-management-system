<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE isp_links DROP CONSTRAINT IF EXISTS isp_links_link_type_check");
        DB::statement("ALTER TABLE isp_links ADD CONSTRAINT isp_links_link_type_check CHECK (link_type IN ('Backhaul', 'Peering', 'Backup'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE isp_links DROP CONSTRAINT IF EXISTS isp_links_link_type_check");
        DB::statement("ALTER TABLE isp_links ADD CONSTRAINT isp_links_link_type_check CHECK (link_type IN ('Backhaul', 'Peering'))");
    }
};
