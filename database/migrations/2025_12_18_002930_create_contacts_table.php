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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('role')->nullable();
            $table->string('category')->nullable();
            $table->string('region')->nullable();
            $table->string('atoll')->nullable();
            $table->string('island')->nullable();
            $table->string('site')->nullable();
            $table->text('notes')->nullable();
            $table->string('source_sheet')->nullable();
            $table->string('raw')->nullable();
            $table->timestamps();

            // Indexes for fast searching
            $table->index('name');
            $table->index('phone');
            $table->index('company');
            $table->index('category');
            $table->index('atoll');
            $table->index('island');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
