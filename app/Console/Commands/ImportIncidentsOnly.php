<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\ActionPoint;
use App\Models\Category;
use App\Models\OutageCategory;
use App\Models\FaultType;
use App\Models\ResolutionTeam;
use Illuminate\Support\Facades\DB;

class ImportIncidentsOnly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incidents:import-simple {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simple import of incidents data only (no user accounts)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting simple incidents import from: {$filePath}");

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);
        
        if ($data === null) {
            $this->error('Invalid JSON file: ' . json_last_error_msg());
            return 1;
        }

        $incidents = $data['incidents'] ?? [];
        $this->info('Found ' . count($incidents) . ' incidents to import');

        if (count($incidents) === 0) {
            $this->warn('No incidents found in export file');
            return 0;
        }

        DB::beginTransaction();
        
        try {
            // Create lookup data if needed (safe way)
            $this->createLookupData($data);
            
            // Import incidents
            $this->importIncidents($incidents);
            
            DB::commit();
            $this->info('✅ Import completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Import failed: ' . $e->getMessage());
            $this->error('Line: ' . $e->getLine());
            return 1;
        }

        return 0;
    }

    private function createLookupData(array $data)
    {
        $this->info('Creating lookup data...');

        // Categories
        if (isset($data['categories'])) {
            foreach ($data['categories'] as $cat) {
                Category::firstOrCreate(['name' => $cat['name']]);
            }
        }

        // Outage Categories
        if (isset($data['outage_categories'])) {
            foreach ($data['outage_categories'] as $cat) {
                OutageCategory::firstOrCreate(['name' => $cat['name']]);
            }
        }

        // Fault Types
        if (isset($data['fault_types'])) {
            foreach ($data['fault_types'] as $type) {
                FaultType::firstOrCreate(['name' => $type['name']]);
            }
        }

        // Resolution Teams
        if (isset($data['resolution_teams'])) {
            foreach ($data['resolution_teams'] as $team) {
                ResolutionTeam::firstOrCreate(['name' => $team['name']]);
            }
        }

        $this->info('Lookup data created');
    }

    private function importIncidents(array $incidents)
    {
        $this->info('Importing incidents...');
        $imported = 0;
        $skipped = 0;

        foreach ($incidents as $incidentData) {
            try {
                // Check if incident already exists
                if (Incident::where('incident_code', $incidentData['incident_code'])->exists()) {
                    $this->warn("Skipping existing incident: {$incidentData['incident_code']}");
                    $skipped++;
                    continue;
                }

                // Extract related data
                $logs = $incidentData['logs'] ?? [];
                $actionPoints = $incidentData['action_points'] ?? [];
                
                // Remove related data and IDs from incident data
                unset($incidentData['id'], $incidentData['logs'], $incidentData['action_points']);
                
                // Create incident
                $incident = Incident::create($incidentData);
                
                // Import logs
                foreach ($logs as $logData) {
                    unset($logData['id']);
                    $logData['incident_id'] = $incident->id;
                    IncidentLog::create($logData);
                }
                
                // Import action points
                foreach ($actionPoints as $actionPointData) {
                    unset($actionPointData['id']);
                    $actionPointData['incident_id'] = $incident->id;
                    ActionPoint::create($actionPointData);
                }
                
                $imported++;
                $this->info("✅ Imported: {$incident->incident_code}");
                
            } catch (\Exception $e) {
                $this->error("❌ Failed to import incident: {$incidentData['incident_code']} - " . $e->getMessage());
                throw $e; // Re-throw to trigger rollback
            }
        }
        
        $this->info("📊 Summary: {$imported} imported, {$skipped} skipped");
    }
}
