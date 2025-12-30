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
        Schema::create('incident_isp_link', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->foreignId('isp_link_id')->constrained()->onDelete('cascade');
            $table->decimal('capacity_lost_gbps', 10, 2)->nullable();
            $table->text('services_impacted')->nullable();
            $table->boolean('traffic_rerouted')->default(false);
            $table->text('reroute_details')->nullable();
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['incident_id', 'isp_link_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_isp_link');
    }
};
