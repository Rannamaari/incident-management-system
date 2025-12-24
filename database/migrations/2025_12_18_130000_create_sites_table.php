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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->string('site_number'); // e.g., 1, 2, 3
            $table->string('site_code')->unique(); // e.g., AA-BODUFULHADHOO-1
            $table->string('display_name'); // e.g., AA-BODUFULHADHOO-1
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['region_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
