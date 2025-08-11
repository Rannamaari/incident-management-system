<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incident;
use Carbon\Carbon;

class IdentifyMissingDelayReasons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incidents:check-delay-reasons {--fix : Fix incidents by prompting for missing delay reasons} {--export= : Export results to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Identify closed incidents with duration > 5 hours that are missing delay reasons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for incidents missing delay reasons...');
        $this->newLine();

        // Get all closed incidents
        $incidents = Incident::where('status', 'Closed')
            ->whereNotNull('started_at')
            ->whereNotNull('resolved_at')
            ->get();

        $problematicIncidents = [];
        $totalChecked = 0;

        foreach ($incidents as $incident) {
            $totalChecked++;
            
            $start = Carbon::parse($incident->started_at);
            $end = Carbon::parse($incident->resolved_at);
            $durationHours = $start->diffInHours($end);

            // Check if duration > 5 hours and delay_reason is missing
            if ($durationHours > 5 && empty($incident->delay_reason)) {
                $problematicIncidents[] = [
                    'incident' => $incident,
                    'duration_hours' => $durationHours,
                    'duration_minutes' => $start->diffInMinutes($end)
                ];
            }
        }

        $this->info("Checked {$totalChecked} closed incidents");
        $this->info("Found " . count($problematicIncidents) . " incidents missing delay reasons");
        $this->newLine();

        if (empty($problematicIncidents)) {
            $this->success('✅ All incidents with duration > 5 hours have delay reasons!');
            return 0;
        }

        // Display results in a table
        $tableData = [];
        foreach ($problematicIncidents as $item) {
            $incident = $item['incident'];
            $tableData[] = [
                $incident->incident_code,
                $incident->severity,
                $incident->started_at->format('Y-m-d H:i'),
                $incident->resolved_at->format('Y-m-d H:i'),
                number_format($item['duration_hours'], 1) . 'h',
                $incident->category ?? 'N/A',
                $incident->affected_services
            ];
        }

        $this->table([
            'Incident Code',
            'Severity',
            'Started At',
            'Resolved At', 
            'Duration',
            'Category',
            'Affected Services'
        ], $tableData);

        // Export option
        if ($this->option('export')) {
            $this->exportToCsv($problematicIncidents, $this->option('export'));
        }

        // Fix option
        if ($this->option('fix')) {
            $this->fixIncidents($problematicIncidents);
        } else {
            $this->newLine();
            $this->warn('💡 Run with --fix option to interactively add delay reasons');
            $this->warn('💡 Run with --export=filename.csv to export results');
        }

        return 0;
    }

    private function exportToCsv(array $incidents, string $filename)
    {
        $this->info("Exporting results to {$filename}...");
        
        $handle = fopen($filename, 'w');
        
        // Add CSV headers
        fputcsv($handle, [
            'Incident Code',
            'Severity',
            'Started At',
            'Resolved At',
            'Duration Hours',
            'Duration Minutes',
            'Category',
            'Affected Services',
            'Summary'
        ]);

        foreach ($incidents as $item) {
            $incident = $item['incident'];
            fputcsv($handle, [
                $incident->incident_code,
                $incident->severity,
                $incident->started_at->format('Y-m-d H:i:s'),
                $incident->resolved_at->format('Y-m-d H:i:s'),
                number_format($item['duration_hours'], 2),
                $item['duration_minutes'],
                $incident->category ?? '',
                $incident->affected_services,
                $incident->summary
            ]);
        }

        fclose($handle);
        $this->success("✅ Exported " . count($incidents) . " incidents to {$filename}");
    }

    private function fixIncidents(array $incidents)
    {
        $this->info('Interactive mode: Add delay reasons for incidents');
        $this->newLine();

        foreach ($incidents as $item) {
            $incident = $item['incident'];
            
            $this->line("📋 <comment>{$incident->incident_code}</comment>");
            $this->line("   Duration: <info>" . number_format($item['duration_hours'], 1) . " hours</info>");
            $this->line("   Summary: {$incident->summary}");
            $this->newLine();

            if ($this->confirm("Add delay reason for {$incident->incident_code}?", true)) {
                $delayReason = $this->ask('Enter delay reason');
                
                if (!empty($delayReason)) {
                    $incident->delay_reason = $delayReason;
                    $incident->save();
                    $this->success("✅ Added delay reason for {$incident->incident_code}");
                } else {
                    $this->warn("⏭️  Skipped {$incident->incident_code} (empty reason)");
                }
            } else {
                $this->warn("⏭️  Skipped {$incident->incident_code}");
            }
            
            $this->newLine();
        }
    }
}
