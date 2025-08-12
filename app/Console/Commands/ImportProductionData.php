<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\ActionPoint;
use App\Models\User;
use App\Models\Category;
use App\Models\OutageCategory;
use App\Models\FaultType;
use App\Models\ResolutionTeam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportProductionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incidents:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import incidents and related data from JSON export file';

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

        $this->info("Starting import from: {$filePath}");
        $this->info("File size: " . round(filesize($filePath) / 1024, 2) . " KB");

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);
        
        if ($data === null) {
            $this->error('Invalid JSON file: ' . json_last_error_msg());
            $this->error('First 200 chars: ' . substr($jsonContent, 0, 200));
            return 1;
        }

        $this->info('JSON parsed successfully');
        $this->info('Data keys: ' . implode(', ', array_keys($data)));
        $this->info('Incidents to import: ' . count($data['incidents'] ?? []));

        DB::beginTransaction();
        
        try {
            // Import lookup tables first
            $this->importUsers($data['users'] ?? []);
            $this->importCategories($data['categories'] ?? []);
            $this->importOutageCategories($data['outage_categories'] ?? []);
            $this->importFaultTypes($data['fault_types'] ?? []);
            $this->importResolutionTeams($data['resolution_teams'] ?? []);
            
            // Import incidents and related data
            $this->importIncidents($data['incidents'] ?? []);
            
            DB::commit();
            $this->info('Import completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Import failed: ' . $e->getMessage());
            $this->error('Error on line: ' . $e->getLine());
            $this->error('Error in file: ' . $e->getFile());
            if ($this->option('verbose')) {
                $this->error('Stack trace: ' . $e->getTraceAsString());
            }
            return 1;
        }

        return 0;
    }

    private function importUsers(array $users)
    {
        $this->info('Importing users...');
        
        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'], // Already hashed
                    'role' => $userData['role'],
                    'email_verified_at' => $userData['email_verified_at'],
                ]
            );
        }
        
        $this->info('Users imported: ' . count($users));
    }

    private function importCategories(array $categories)
    {
        $this->info('Importing categories...');
        
        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }
        
        $this->info('Categories imported: ' . count($categories));
    }

    private function importOutageCategories(array $outageCategories)
    {
        $this->info('Importing outage categories...');
        
        foreach ($outageCategories as $categoryData) {
            OutageCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }
        
        $this->info('Outage categories imported: ' . count($outageCategories));
    }

    private function importFaultTypes(array $faultTypes)
    {
        $this->info('Importing fault types...');
        
        foreach ($faultTypes as $faultTypeData) {
            FaultType::updateOrCreate(
                ['name' => $faultTypeData['name']],
                $faultTypeData
            );
        }
        
        $this->info('Fault types imported: ' . count($faultTypes));
    }

    private function importResolutionTeams(array $resolutionTeams)
    {
        $this->info('Importing resolution teams...');
        
        foreach ($resolutionTeams as $teamData) {
            ResolutionTeam::updateOrCreate(
                ['name' => $teamData['name']],
                $teamData
            );
        }
        
        $this->info('Resolution teams imported: ' . count($resolutionTeams));
    }

    private function importIncidents(array $incidents)
    {
        $this->info('Importing incidents...');
        
        foreach ($incidents as $incidentData) {
            // Extract related data
            $logs = $incidentData['logs'] ?? [];
            $actionPoints = $incidentData['action_points'] ?? [];
            
            // Remove related data from incident data
            unset($incidentData['logs'], $incidentData['action_points']);
            
            // Create or update incident
            $incident = Incident::updateOrCreate(
                ['incident_code' => $incidentData['incident_code']],
                $incidentData
            );
            
            // Import logs
            foreach ($logs as $logData) {
                unset($logData['id']); // Remove old ID
                $logData['incident_id'] = $incident->id;
                IncidentLog::create($logData);
            }
            
            // Import action points
            foreach ($actionPoints as $actionPointData) {
                unset($actionPointData['id']); // Remove old ID
                $actionPointData['incident_id'] = $incident->id;
                ActionPoint::create($actionPointData);
            }
        }
        
        $this->info('Incidents imported: ' . count($incidents));
    }
}
