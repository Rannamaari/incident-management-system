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
        Schema::create('fbb_islands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->string('island_name');
            $table->string('technology'); // FTTH, FTTx, IPOE, etc.
            $table->boolean('is_active')->default(true);
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Index for faster lookups
            $table->index(['region_id', 'island_name']);
            $table->index('is_active');
        });

        // Create pivot table for incident-fbb_island relationship
        Schema::create('incident_fbb_island', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->onDelete('cascade');
            $table->foreignId('fbb_island_id')->constrained('fbb_islands')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint to prevent duplicate relationships
            $table->unique(['incident_id', 'fbb_island_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_fbb_island');
        Schema::dropIfExists('fbb_islands');
    }
};
