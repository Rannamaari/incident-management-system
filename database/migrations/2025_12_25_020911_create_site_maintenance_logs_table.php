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
        Schema::create('site_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->date('maintenance_date');
            $table->string('maintenance_type')->default('PM'); // PM, Corrective, etc.
            $table->text('description')->nullable();
            $table->string('performed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'maintenance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_maintenance_logs');
    }
};
