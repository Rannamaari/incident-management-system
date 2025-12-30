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
        Schema::create('isp_links', function (Blueprint $table) {
            $table->id();
            $table->string('isp_name', 100);
            $table->string('circuit_id', 100)->unique()->index();
            $table->enum('link_type', ['Backhaul', 'Peering']);
            $table->decimal('total_capacity_gbps', 10, 2);
            $table->decimal('current_capacity_gbps', 10, 2);
            $table->enum('status', ['Up', 'Down', 'Degraded'])->default('Up');
            $table->string('location_a', 255);
            $table->string('location_b', 255);
            $table->string('prtg_sensor_id', 100)->nullable();
            $table->string('prtg_api_endpoint', 255)->nullable();
            $table->timestamp('last_prtg_sync')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('isp_links');
    }
};
