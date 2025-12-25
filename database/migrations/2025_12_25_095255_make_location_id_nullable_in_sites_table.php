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
        Schema::table('sites', function (Blueprint $table) {
            // For PostgreSQL, we need to drop and recreate the column
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('region_id')->constrained('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable(false)->change();
        });
    }
};
