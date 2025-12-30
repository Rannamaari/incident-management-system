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
            $table->decimal('isp_capacity_lost_gbps', 10, 2)->nullable()->after('isp_link_id');
            $table->text('isp_services_impacted')->nullable()->after('isp_capacity_lost_gbps');
            $table->boolean('isp_traffic_rerouted')->default(false)->after('isp_services_impacted');
            $table->text('isp_reroute_details')->nullable()->after('isp_traffic_rerouted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['isp_capacity_lost_gbps', 'isp_services_impacted', 'isp_traffic_rerouted', 'isp_reroute_details']);
        });
    }
};
