<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    /**
     * Display the log viewer page
     */
    public function index(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');

        // Check if log file exists
        if (!File::exists($logFile)) {
            return view('logs.viewer', [
                'logs' => [],
                'totalLines' => 0,
                'fileSize' => 0,
                'lastModified' => null,
                'noFile' => true,
            ]);
        }

        // Get file info
        $fileSize = File::size($logFile);
        $lastModified = File::lastModified($logFile);

        // Get filter parameters
        $lines = $request->input('lines', 100); // Default: last 100 lines
        $search = $request->input('search', '');
        $level = $request->input('level', ''); // error, warning, info, etc.

        // Read log file
        $logContent = File::get($logFile);
        $logLines = explode("\n", $logContent);
        $totalLines = count($logLines);

        // Filter logs
        $filteredLogs = $logLines;

        // Filter by log level
        if ($level) {
            $filteredLogs = array_filter($filteredLogs, function($line) use ($level) {
                return stripos($line, strtoupper($level)) !== false;
            });
        }

        // Filter by search term
        if ($search) {
            $filteredLogs = array_filter($filteredLogs, function($line) use ($search) {
                return stripos($line, $search) !== false;
            });
        }

        // Get last N lines
        $filteredLogs = array_slice($filteredLogs, -$lines);

        // Reverse to show newest first
        $filteredLogs = array_reverse($filteredLogs);

        // Parse logs into structured format
        $parsedLogs = $this->parseLogs($filteredLogs);

        return view('logs.viewer', [
            'logs' => $parsedLogs,
            'totalLines' => $totalLines,
            'fileSize' => $this->formatBytes($fileSize),
            'lastModified' => date('Y-m-d H:i:s', $lastModified),
            'noFile' => false,
            'currentLines' => $lines,
            'currentSearch' => $search,
            'currentLevel' => $level,
        ]);
    }

    /**
     * Download the log file
     */
    public function download()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            abort(404, 'Log file not found');
        }

        return response()->download($logFile, 'laravel-' . date('Y-m-d-His') . '.log');
    }

    /**
     * Clear the log file
     */
    public function clear()
    {
        $logFile = storage_path('logs/laravel.log');

        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return redirect()->route('logs.viewer')->with('success', 'Log file cleared successfully');
    }

    /**
     * Parse log lines into structured format
     */
    private function parseLogs($lines)
    {
        $logs = [];
        $currentLog = null;

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            // Check if this is a new log entry (starts with timestamp pattern like [2024-01-01 12:00:00])
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                // Save previous log entry
                if ($currentLog !== null) {
                    $logs[] = $currentLog;
                }

                // Parse log level
                $level = 'info';
                if (preg_match('/\.(ERROR|WARNING|INFO|DEBUG|CRITICAL|ALERT|EMERGENCY):/', $line, $levelMatches)) {
                    $level = strtolower($levelMatches[1]);
                }

                // Start new log entry
                $currentLog = [
                    'timestamp' => $matches[1],
                    'level' => $level,
                    'message' => $line,
                    'stack' => [],
                ];
            } else {
                // This is a continuation of the previous log (stack trace, etc.)
                if ($currentLog !== null) {
                    $currentLog['stack'][] = $line;
                }
            }
        }

        // Add the last log entry
        if ($currentLog !== null) {
            $logs[] = $currentLog;
        }

        return $logs;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
