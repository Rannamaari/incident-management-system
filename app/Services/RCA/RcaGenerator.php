<?php

namespace App\Services\RCA;

use App\Models\Incident;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class RcaGenerator
{
    /**
     * Generate RCA document from template for the given incident.
     */
    public function generateFromTemplate(Incident $incident): string
    {
        $templatePath = storage_path('app/templates/RCA_Report_Template.docx');
        
        if (!file_exists($templatePath)) {
            throw new \Exception('RCA template file not found at: ' . $templatePath);
        }

        // Create template processor
        $templateProcessor = new TemplateProcessor($templatePath);

        // Replace placeholders with incident data
        $this->fillPlaceholders($templateProcessor, $incident);
        
        // Handle logs table insertion
        $this->insertLogsTable($templateProcessor, $incident);

        // Generate filename
        $filename = "INC-{$incident->incident_code}-" . now()->format('YmdHis') . ".docx";
        $outputPath = storage_path("app/rca/{$filename}");

        // Ensure directory exists
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        // Save the processed document
        $templateProcessor->saveAs($outputPath);

        return $filename;
    }

    /**
     * Fill template placeholders with incident data.
     */
    private function fillPlaceholders(TemplateProcessor $templateProcessor, Incident $incident): void
    {
        $templateProcessor->setValue('INCIDENT_CODE', $incident->incident_code ?? 'N/A');
        $templateProcessor->setValue('SUMMARY', $incident->summary ?? 'N/A');
        $templateProcessor->setValue('SEVERITY', $incident->severity ?? 'N/A');
        $templateProcessor->setValue('STATUS', $incident->status ?? 'N/A');
        $templateProcessor->setValue('AFFECTED_SERVICES', $incident->affected_services ?? 'N/A');
        
        $templateProcessor->setValue('STARTED_AT', $incident->started_at ? $incident->started_at->format('M j, Y g:i A') : 'N/A');
        $templateProcessor->setValue('RESOLVED_AT', $incident->resolved_at ? $incident->resolved_at->format('M j, Y g:i A') : 'N/A');
        
        // Duration
        $duration = 'N/A';
        if ($incident->resolved_at && $incident->started_at) {
            $duration = $incident->duration_hms ?? $incident->started_at->diffForHumans($incident->resolved_at, true);
        }
        $templateProcessor->setValue('DURATION', $duration);

        // Incident details
        $templateProcessor->setValue('CATEGORY', $incident->category ?? 'Not specified');
        $templateProcessor->setValue('OUTAGE_CATEGORY', $incident->outage_category ?? 'Not specified');
        $templateProcessor->setValue('FAULT_TYPE', $incident->fault_type ?? 'Not specified');
        $templateProcessor->setValue('RESOLUTION_TEAM', $incident->resolution_team ?? 'Not assigned');

        // Root cause and delay reason
        $templateProcessor->setValue('ROOT_CAUSE', $incident->root_cause ?? 'To be determined');
        $templateProcessor->setValue('DELAY_REASON', $incident->delay_reason ?? 'N/A');

        // SLA information
        $slaTarget = $incident->sla_minutes ? ($incident->sla_minutes / 60) . ' hours' : 'N/A';
        $templateProcessor->setValue('SLA_TARGET', $slaTarget);
        $templateProcessor->setValue('SLA_STATUS', $incident->sla_status ?? 'N/A');

        // Resolution metrics
        $travelTime = $incident->travel_time ? $incident->travel_time . ' minutes' : 'N/A';
        $workTime = $incident->work_time ? $incident->work_time . ' minutes' : 'N/A';
        $templateProcessor->setValue('TRAVEL_TIME', $travelTime);
        $templateProcessor->setValue('WORK_TIME', $workTime);

        // Generation info
        $templateProcessor->setValue('GENERATED_AT', now()->format('M j, Y g:i A'));
    }

    /**
     * Insert logs table into the document.
     */
    private function insertLogsTable(TemplateProcessor $templateProcessor, Incident $incident): void
    {
        $logs = $incident->logs()->orderBy('occurred_at', 'asc')->get();
        
        if ($logs->isEmpty()) {
            $templateProcessor->setValue('LOGS_TABLE', 'No log entries recorded.');
            return;
        }

        // Create logs table content as text
        $logsContent = '';
        foreach ($logs as $index => $log) {
            $timestamp = $log->occurred_at->format('M j, Y g:i A');
            $logsContent .= ($index + 1) . ". [{$timestamp}] {$log->note}\n";
        }

        $templateProcessor->setValue('LOGS_TABLE', rtrim($logsContent));
    }
}