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
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedInteger('sites_2g_impacted')->nullable()->default(0)->after('affected_services');
            $table->unsignedInteger('sites_3g_impacted')->nullable()->default(0)->after('sites_2g_impacted');
            $table->unsignedInteger('sites_4g_impacted')->nullable()->default(0)->after('sites_3g_impacted');
            $table->unsignedInteger('sites_5g_impacted')->nullable()->default(0)->after('sites_4g_impacted');
            $table->unsignedInteger('fbb_impacted')->nullable()->default(0)->after('sites_5g_impacted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['sites_2g_impacted', 'sites_3g_impacted', 'sites_4g_impacted', 'sites_5g_impacted', 'fbb_impacted']);
        });
    }
};
