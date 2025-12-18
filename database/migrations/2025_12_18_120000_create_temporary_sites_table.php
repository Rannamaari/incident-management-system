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
        Schema::create('temporary_sites', function (Blueprint $table) {
            $table->id();
            $table->string('temp_site_id')->unique(); // e.g., TS001
            $table->string('atoll_code')->index(); // e.g., AA, ADh, B, etc.
            $table->string('site_name')->index(); // e.g., AA_Veligandu_Resort
            $table->string('coverage'); // e.g., 2G/3G/4G, 3G/4G
            $table->date('added_date')->index(); // Date the temporary site was added
            $table->string('transmission_or_backhaul'); // e.g., Rasdhoo-Veligandu link
            $table->text('remarks')->nullable(); // Long notes
            $table->enum('status', ['Temporary', 'Resolved', 'Remove from list', 'Monitoring'])->default('Temporary')->index();
            $table->date('review_date')->nullable(); // Optional follow-up date
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
        Schema::dropIfExists('temporary_sites');
    }
};
