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
            $table->string('site_id')->unique(); // e.g., SITE001
            $table->string('atoll_code')->index();
            $table->string('site_name')->index();
            $table->string('coverage'); // e.g., 2G/3G/4G, 3G/4G
            $table->date('operational_date')->index(); // Date the site became operational
            $table->string('transmission_or_backhaul');
            $table->text('remarks')->nullable();
            $table->enum('status', ['Active', 'Monitoring', 'Maintenance', 'Inactive'])->default('Active')->index();
            $table->date('review_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
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
