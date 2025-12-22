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
        Schema::table('temporary_sites', function (Blueprint $table) {
            $table->boolean('is_2g_online')->default(true)->after('coverage');
            $table->boolean('is_3g_online')->default(true)->after('is_2g_online');
            $table->boolean('is_4g_online')->default(true)->after('is_3g_online');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_sites', function (Blueprint $table) {
            $table->dropColumn(['is_2g_online', 'is_3g_online', 'is_4g_online']);
        });
    }
};
