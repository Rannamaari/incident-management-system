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
        Schema::create('notification_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Level 1", "Level 2", "Level 3"
            $table->string('description')->nullable(); // e.g., "Critical Alerts Only"
            $table->json('severities'); // ['High'] or ['Medium', 'High'] or ['Low', 'Medium', 'High']
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // For ordering levels
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_levels');
    }
};
