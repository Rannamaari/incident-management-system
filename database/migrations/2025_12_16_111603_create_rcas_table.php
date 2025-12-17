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
        Schema::create('rcas', function (Blueprint $table) {
            $table->id();

            // Link to incident
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');

            // RCA Basic Info
            $table->string('title');
            $table->string('rca_number')->unique()->nullable();

            // 1. Problem Description
            $table->text('problem_description')->nullable();

            // 2. Problem Analysis
            $table->text('problem_analysis')->nullable();

            // 3. Root Cause
            $table->text('root_cause')->nullable();

            // 4. Corrective Action
            // 4.1 Workaround
            $table->text('workaround')->nullable();

            // 4.2 Solution
            $table->text('solution')->nullable();

            // 4.3 Recommendation
            $table->text('recommendation')->nullable();

            // Status
            $table->enum('status', ['Draft', 'In Review', 'Approved', 'Closed'])->default('Draft');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });

        // Time logs for RCA
        Schema::create('rca_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rca_id')->constrained()->onDelete('cascade');
            $table->datetime('occurred_at');
            $table->text('event_description');
            $table->timestamps();
        });

        // Action points for RCA
        Schema::create('rca_action_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rca_id')->constrained()->onDelete('cascade');
            $table->text('action_item');
            $table->string('responsible_person');
            $table->date('due_date')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rca_action_points');
        Schema::dropIfExists('rca_time_logs');
        Schema::dropIfExists('rcas');
    }
};
