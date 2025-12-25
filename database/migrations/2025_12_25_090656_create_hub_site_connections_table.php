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
        Schema::create('hub_site_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('connected_site_id')->constrained('sites')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate connections
            $table->unique(['hub_site_id', 'connected_site_id']);

            // Indexes for performance
            $table->index('hub_site_id');
            $table->index('connected_site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_site_connections');
    }
};
