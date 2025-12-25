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
            $table->boolean('is_link_site')->default(false)->after('is_temp_site');
            $table->enum('site_type', ['End Site', 'Hub Site'])->default('End Site')->after('is_link_site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['is_link_site', 'site_type']);
        });
    }
};
