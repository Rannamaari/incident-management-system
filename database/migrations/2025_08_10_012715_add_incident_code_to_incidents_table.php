<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('incident_code')->nullable()->after('id');
        });
        
        // Generate incident codes for existing records
        $this->generateIncidentCodesForExistingRecords();
        
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('incident_code')->nullable(false)->change();
            $table->unique('incident_code');
            $table->index('incident_code');
        });
    }
    
    /**
     * Generate incident codes for existing records.
     */
    private function generateIncidentCodesForExistingRecords(): void
    {
        $incidents = DB::table('incidents')->orderBy('started_at')->get();
        $dailyCounts = [];
        
        foreach ($incidents as $incident) {
            $date = \Carbon\Carbon::parse($incident->started_at)->format('Ymd');
            
            if (!isset($dailyCounts[$date])) {
                $dailyCounts[$date] = 0;
            }
            
            $dailyCounts[$date]++;
            $sequentialNumber = str_pad($dailyCounts[$date], 4, '0', STR_PAD_LEFT);
            $incidentCode = "INC-{$date}-{$sequentialNumber}";
            
            DB::table('incidents')
                ->where('id', $incident->id)
                ->update(['incident_code' => $incidentCode]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropUnique(['incident_code']);
            $table->dropIndex(['incident_code']);
            $table->dropColumn('incident_code');
        });
    }
};
