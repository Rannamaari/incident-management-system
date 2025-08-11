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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            
            // 1. incident_id (string, unique, indexed)
            $table->string('incident_id')->unique()->index();
            
            // 2. summary (string, "Outage Details (Incident Summary)")
            $table->string('summary');
            
            // 3. outage_category (enum/string)
            $table->string('outage_category');
            
            // 4. category (enum/string)
            $table->string('category');
            
            // 5. affected_services (string)
            $table->string('affected_services');
            
            // 6. started_at (datetime)
            $table->datetime('started_at')->index();
            
            // 7. resolved_at (datetime, nullable)
            $table->datetime('resolved_at')->nullable();
            
            // 8. duration_minutes (unsignedInteger, nullable) - manual override
            $table->unsignedInteger('duration_minutes')->nullable();
            
            // 9. fault_type (enum/string, nullable)
            $table->string('fault_type')->nullable();
            
            // 10. root_cause (text, nullable)
            $table->text('root_cause')->nullable();
            
            // 11. delay_reason (text, nullable) - notes like weather
            $table->text('delay_reason')->nullable();
            
            // 12. resolution_team (string, nullable)
            $table->string('resolution_team')->nullable();
            
            // 13. journey_started_at (datetime, nullable)
            $table->datetime('journey_started_at')->nullable();
            
            // 14. island_arrival_at (datetime, nullable)
            $table->datetime('island_arrival_at')->nullable();
            
            // 15. work_started_at (datetime, nullable)
            $table->datetime('work_started_at')->nullable();
            
            // 16. work_completed_at (datetime, nullable)
            $table->datetime('work_completed_at')->nullable();
            
            // 17. pir_rca_no (string, nullable)
            $table->string('pir_rca_no')->nullable();
            
            // 18. status (enum: Open, In Progress, Monitoring, Closed; default Open)
            $table->enum('status', ['Open', 'In Progress', 'Monitoring', 'Closed'])->default('Open')->index();
            
            // 19. severity (enum: Critical, High, Medium, Low; default Low)
            $table->enum('severity', ['Critical', 'High', 'Medium', 'Low'])->default('Low')->index();
            
            // 20. sla_minutes (unsignedInteger, default 720)
            $table->unsignedInteger('sla_minutes')->default(720);
            
            // 21. exceeded_sla (boolean, default false)
            $table->boolean('exceeded_sla')->default(false);
            
            // 22. sla_status (string, default 'SLA Achieved')
            $table->string('sla_status')->default('SLA Achieved');
            
            // RCA-related fields
            $table->boolean('rca_required')->default(false)->index();
            $table->string('rca_file_path')->nullable();
            $table->datetime('rca_received_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};