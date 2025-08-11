<?php

namespace App\Console\Commands;

use App\Models\Incident;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportIncidents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incidents:import {path : Path to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import incidents from CSV file with the exact 22-column format';

    /**
     * Expected CSV headers in exact order.
     */
    private const EXPECTED_HEADERS = [
        'Incident ID',
        'Outage Details (Incident Summary)',
        'Outage Category',
        'Category',
        'Affected Systems/Services',
        'Start Date and Time',
        'Date and Time Resolved',
        'Durations',
        'Fault/Issue Type',
        'Root Cause',
        'Reason for Delay',
        'Resolution Team',
        'Journey Start Time',
        'Island Arrival Time',
        'Work/Repair Start Time',
        'Repair Completion Time',
        'PIR/RCA No',
        'Incident Status',
        'Severity Level',
        'SLA',
        'Exceeded Beyond SLA',
        'SLA Status'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("CSV file not found: {$path}");
            return 1;
        }

        $this->info("Starting import from: {$path}");

        try {
            $handle = fopen($path, 'r');
            
            if (!$handle) {
                $this->error("Cannot open CSV file");
                return 1;
            }

            // Read and validate headers
            $headers = fgetcsv($handle);
            if (!$this->validateHeaders($headers)) {
                fclose($handle);
                return 1;
            }

            $imported = 0;
            $skipped = 0;
            $errors = 0;

            $this->output->progressStart();

            while (($row = fgetcsv($handle)) !== false) {
                $this->output->progressAdvance();
                
                try {
                    $result = $this->importRow($row);
                    if ($result === 'imported') {
                        $imported++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->warn("Error importing row: " . $e->getMessage());
                }
            }

            $this->output->progressFinish();
            fclose($handle);

            $this->info("\nImport completed:");
            $this->info("- Imported: {$imported}");
            $this->info("- Updated: {$skipped}");
            $this->info("- Errors: {$errors}");

        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Validate CSV headers.
     */
    private function validateHeaders(array $headers): bool
    {
        if (count($headers) !== count(self::EXPECTED_HEADERS)) {
            $this->error("Expected " . count(self::EXPECTED_HEADERS) . " columns, got " . count($headers));
            return false;
        }

        foreach (self::EXPECTED_HEADERS as $index => $expected) {
            if (trim($headers[$index]) !== $expected) {
                $this->error("Header mismatch at column " . ($index + 1) . ": expected '{$expected}', got '{$headers[$index]}'");
                return false;
            }
        }

        return true;
    }

    /**
     * Import a single row.
     */
    private function importRow(array $row): string
    {
        // Skip empty rows
        if (empty(trim($row[0]))) {
            return 'skipped';
        }

        $data = [
            'incident_id' => trim($row[0]),
            'summary' => trim($row[1]),
            'outage_category' => $this->validateConstant(trim($row[2]), Incident::OUTAGE_CATEGORIES, 'Unknown'),
            'category' => $this->validateConstant(trim($row[3]), Incident::CATEGORIES, 'ICT'),
            'affected_services' => trim($row[4]),
            'started_at' => $this->parseDate($row[5]),
            'resolved_at' => $this->parseDate($row[6]),
            'duration_minutes' => $this->parseDuration($row[7]),
            'fault_type' => $this->validateConstant(trim($row[8]), Incident::FAULT_TYPES, null, true),
            'root_cause' => !empty(trim($row[9])) ? trim($row[9]) : null,
            'delay_reason' => !empty(trim($row[10])) ? trim($row[10]) : null,
            'resolution_team' => !empty(trim($row[11])) ? trim($row[11]) : null,
            'journey_started_at' => $this->parseDate($row[12]),
            'island_arrival_at' => $this->parseDate($row[13]),
            'work_started_at' => $this->parseDate($row[14]),
            'work_completed_at' => $this->parseDate($row[15]),
            'pir_rca_no' => !empty(trim($row[16])) ? trim($row[16]) : null,
            'status' => $this->validateConstant(trim($row[17]), Incident::STATUSES, 'Open'),
            'severity' => $this->validateConstant(trim($row[18]), Incident::SEVERITIES, 'Low'),
        ];

        // Use updateOrCreate to handle duplicates by incident_id
        $incident = Incident::updateOrCreate(
            ['incident_id' => $data['incident_id']],
            $data
        );

        return $incident->wasRecentlyCreated ? 'imported' : 'updated';
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate(?string $date): ?Carbon
    {
        if (empty(trim($date))) {
            return null;
        }

        $date = trim($date);
        
        // Try common formats
        $formats = [
            'd/m/y H:i',    // 1/1/25 9:17
            'd/m/Y H:i',    // 1/1/2025 9:17
            'm/d/y H:i',    // 1/1/25 9:17 (US format)
            'm/d/Y H:i',    // 1/1/2025 9:17 (US format)
            'Y-m-d H:i:s',  // MySQL format
            'Y-m-d H:i',    // MySQL format without seconds
            'd-m-Y H:i',    // European format
            'd-m-y H:i',    // European format short year
        ];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $date);
                if ($parsed) {
                    // Set timezone to Indian/Maldives
                    return $parsed->setTimezone('Indian/Maldives');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Try Carbon's flexible parser as fallback
        try {
            return Carbon::parse($date)->setTimezone('Indian/Maldives');
        } catch (\Exception $e) {
            $this->warn("Could not parse date: {$date}");
            return null;
        }
    }

    /**
     * Parse duration from various formats.
     */
    private function parseDuration(?string $duration): ?int
    {
        if (empty(trim($duration))) {
            return null;
        }

        $duration = trim($duration);
        
        // If it's already a number (minutes)
        if (is_numeric($duration)) {
            return (int)$duration;
        }

        // Try to parse HH:MM:SS or HH:MM format
        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $duration, $matches)) {
            $hours = (int)$matches[1];
            $minutes = (int)$matches[2];
            return ($hours * 60) + $minutes;
        }

        // Try to parse "X hours Y minutes" format
        if (preg_match('/(\d+)\s*hours?\s*(\d+)\s*minutes?/i', $duration, $matches)) {
            return ((int)$matches[1] * 60) + (int)$matches[2];
        }

        // Try to parse "X hours" format
        if (preg_match('/(\d+)\s*hours?/i', $duration, $matches)) {
            return (int)$matches[1] * 60;
        }

        // Try to parse "X minutes" format
        if (preg_match('/(\d+)\s*minutes?/i', $duration, $matches)) {
            return (int)$matches[1];
        }

        $this->warn("Could not parse duration: {$duration}");
        return null;
    }

    /**
     * Validate a value against constants, with fallback.
     */
    private function validateConstant(?string $value, array $constants, ?string $default = null, bool $nullable = false): ?string
    {
        if (empty(trim($value))) {
            return $nullable ? null : $default;
        }

        $value = trim($value);
        
        // Exact match
        if (in_array($value, $constants)) {
            return $value;
        }

        // Case insensitive match
        foreach ($constants as $constant) {
            if (strcasecmp($value, $constant) === 0) {
                return $constant;
            }
        }

        // Partial match (for fuzzy matching)
        foreach ($constants as $constant) {
            if (stripos($constant, $value) !== false || stripos($value, $constant) !== false) {
                $this->warn("Fuzzy matched '{$value}' to '{$constant}'");
                return $constant;
            }
        }

        if ($default !== null) {
            $this->warn("Unknown value '{$value}', using default '{$default}'");
            return $default;
        }

        return $nullable ? null : $constants[0];
    }
}