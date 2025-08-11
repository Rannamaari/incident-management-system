<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\OutageCategory;
use App\Models\FaultType;
use App\Models\ResolutionTeam;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, seed the lookup tables with existing data
        $this->seedLookupTables();
        
        // Add foreign key columns
        Schema::table('incidents', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')->constrained()->onDelete('set null');
            $table->foreignId('outage_category_id')->nullable()->after('outage_category')->constrained()->onDelete('set null');
            $table->foreignId('fault_type_id')->nullable()->after('fault_type')->constrained()->onDelete('set null');
            $table->foreignId('resolution_team_id')->nullable()->after('resolution_team')->constrained()->onDelete('set null');
        });
        
        // Update existing incidents with foreign keys
        $this->updateExistingIncidents();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['outage_category_id']);
            $table->dropForeign(['fault_type_id']);
            $table->dropForeign(['resolution_team_id']);
            
            $table->dropColumn(['category_id', 'outage_category_id', 'fault_type_id', 'resolution_team_id']);
        });
    }
    
    private function seedLookupTables(): void
    {
        // Seed categories
        $categories = ['FBB', 'RAN', 'ICT', 'International', 'Packet Core', 'Enterprise'];
        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
        
        // Seed outage categories  
        $outageCategories = ['Power', 'Core Network', 'Database', 'Partner End Issue Planned', 'RAN', 'Transmission', 'Unknown'];
        foreach ($outageCategories as $outageCategory) {
            OutageCategory::firstOrCreate(['name' => $outageCategory]);
        }
        
        // Seed fault types
        $faultTypes = ['Fiber Cut', 'Local Power', 'RRU Faulty'];
        foreach ($faultTypes as $faultType) {
            FaultType::firstOrCreate(['name' => $faultType]);
        }
    }
    
    private function updateExistingIncidents(): void
    {
        $incidents = DB::table('incidents')->get();
        
        foreach ($incidents as $incident) {
            $updates = [];
            
            // Map category
            if ($incident->category) {
                $category = Category::where('name', $incident->category)->first();
                if ($category) {
                    $updates['category_id'] = $category->id;
                }
            }
            
            // Map outage_category  
            if ($incident->outage_category) {
                $outageCategory = OutageCategory::where('name', $incident->outage_category)->first();
                if ($outageCategory) {
                    $updates['outage_category_id'] = $outageCategory->id;
                }
            }
            
            // Map fault_type
            if ($incident->fault_type) {
                $faultType = FaultType::where('name', $incident->fault_type)->first();
                if ($faultType) {
                    $updates['fault_type_id'] = $faultType->id;
                }
            }
            
            // Map resolution_team
            if ($incident->resolution_team) {
                $resolutionTeam = ResolutionTeam::firstOrCreate(['name' => $incident->resolution_team]);
                $updates['resolution_team_id'] = $resolutionTeam->id;
            }
            
            if (!empty($updates)) {
                DB::table('incidents')->where('id', $incident->id)->update($updates);
            }
        }
    }
};
