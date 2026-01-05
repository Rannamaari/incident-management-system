<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    /**
     * Display all incidents in logs view with pagination.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $query = Incident::query()
            ->search($request->search)
            ->status($request->status)
            ->severity($request->severity);

        // Add date range filtering
        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        // Add advanced filters
        if ($request->has('rca_required') && $request->rca_required === '1') {
            $query->where('rca_required', true);
        }
        if ($request->has('sla_breached') && $request->sla_breached === '1') {
            $query->where('exceeded_sla', true);
        }
        // Handle unread timeline filter separately
        if ($request->has('has_timeline') && $request->has_timeline === '1') {
            // Get all incidents matching current filters
            $allIncidents = $query->get();
            // Filter for unread timeline updates using model method
            $filteredIncidentIds = $allIncidents->filter(function($incident) {
                return $incident->hasUnreadTimelineUpdates();
            })->pluck('id');

            // Rebuild query with filtered IDs
            $query = Incident::query()->whereIn('id', $filteredIncidentIds);
        }

        // Filter for ISP outages/incidents
        if ($request->has('isp_outages') && $request->isp_outages === '1') {
            $query->where(function($q) {
                // Old system: has isp_link_id
                $q->whereNotNull('isp_link_id')
                  // OR new system: has many-to-many ISP links
                  ->orWhereHas('ispLinks');
            });
        }

        $incidents = $query
            ->orderByDesc('started_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('logs.index', compact('incidents'));
    }

    /**
     * Export incidents to CSV with applied filters.
     */
    public function export(Request $request)
    {
        // Validate date parameters
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        // Build query with same filters as index method
        $query = Incident::query()
            ->search($request->search)
            ->status($request->status)
            ->severity($request->severity);

        // Add date range filtering
        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        // Add advanced filters
        if ($request->has('rca_required') && $request->rca_required === '1') {
            $query->where('rca_required', true);
        }
        if ($request->has('sla_breached') && $request->sla_breached === '1') {
            $query->where('exceeded_sla', true);
        }
        // Handle unread timeline filter separately
        if ($request->has('has_timeline') && $request->has_timeline === '1') {
            // Get all incidents matching current filters
            $allIncidents = $query->get();
            // Filter for unread timeline updates using model method
            $filteredIncidentIds = $allIncidents->filter(function($incident) {
                return $incident->hasUnreadTimelineUpdates();
            })->pluck('id');

            // Rebuild query with filtered IDs
            $query = Incident::query()->whereIn('id', $filteredIncidentIds);
        }

        $query->orderBy('started_at', 'desc');

        // Generate filename based on filters
        $filename = 'incident_logs_export_';
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filename .= $request->date_from . '_to_' . $request->date_to . '_';
        } elseif ($request->filled('date_from')) {
            $filename .= 'from_' . $request->date_from . '_';
        } elseif ($request->filled('date_to')) {
            $filename .= 'until_' . $request->date_to . '_';
        }
        $filename .= now('Indian/Maldives')->format('Y-m-d_H-i-s') . '.csv';

        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // Stream the CSV response
        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Add BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers - all available fields
            $csvHeaders = [
                'Incident Code',
                'Summary',
                'Category',
                'Outage Category',
                'Fault Type',
                'Severity',
                'Status',
                'Affected Services',
                'Started At',
                'Resolved At',
                'Duration (Minutes)',
                'Duration (HMS)',
                'Root Cause',
                'Reason for Delay',
                'Resolution Team',
                'Journey Started At',
                'Island Arrival At',
                'Work Started At',
                'Work Completed At',
                'Travel Time (Minutes)',
                'Work Time (Minutes)',
                'PIR/RCA No',
                'SLA Minutes',
                'SLA Status',
                'RCA Required',
                'RCA Received At',
                'Created At',
                'Updated At'
            ];

            fputcsv($handle, $csvHeaders);

            // Stream data in chunks to handle large datasets
            $query->chunk(1000, function ($incidents) use ($handle) {
                foreach ($incidents as $incident) {
                    // Format dates to Maldives timezone
                    $maldivesTimezone = 'Indian/Maldives';
                    
                    $csvRow = [
                        $incident->incident_code,
                        $incident->summary,
                        $incident->category ?? '',
                        $incident->outage_category ?? '',
                        $incident->fault_type ?? '',
                        $incident->severity,
                        $incident->status,
                        $incident->affected_services,
                        $incident->started_at ? $incident->started_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                        $incident->resolved_at ? $incident->resolved_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                        $incident->duration_minutes ?? '',
                        $incident->duration_hms ?? '',
                        $incident->root_cause ?? '',
                        $incident->delay_reason ?? '',
                        $incident->resolution_team ?? '',
                        $incident->journey_started_at ? $incident->journey_started_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                        $incident->island_arrival_at ? $incident->island_arrival_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                        $incident->work_started_at ? $incident->work_started_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                        $incident->work_completed_at ? $incident->work_completed_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                        $incident->travel_time ?? '',
                        $incident->work_time ?? '',
                        $incident->pir_rca_no ?? '',
                        $incident->sla_minutes ?? '',
                        $incident->getCurrentSlaStatus(),
                        $incident->rca_required ? 'Yes' : 'No',
                        $incident->rca_received_at ? $incident->rca_received_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                        $incident->created_at ? $incident->created_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                        $incident->updated_at ? $incident->updated_at->setTimezone($maldivesTimezone)->format('Y-m-d H:i:s') : '',
                    ];

                    fputcsv($handle, $csvRow);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Display recurring incidents analysis - incidents grouped by summary.
     */
    public function recurringIncidents(Request $request)
    {
        // Get date range for filtering (default to last 3 months)
        $startDate = $request->input('start_date', now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Parse dates
        $dateStart = \Carbon\Carbon::parse($startDate)->startOfDay();
        $dateEnd = \Carbon\Carbon::parse($endDate)->endOfDay();

        // Get minimum occurrence count filter (default to 2 - incidents that occurred at least twice)
        $minOccurrences = $request->input('min_occurrences', 2);

        // Query to get recurring incidents grouped by summary
        // Use string_agg for PostgreSQL (compatible with PostgreSQL)
        $recurringIncidents = Incident::query()
            ->selectRaw("
                summary,
                COUNT(*) as occurrence_count,
                MAX(started_at) as last_occurrence,
                MIN(started_at) as first_occurrence,
                string_agg(DISTINCT COALESCE(severity, 'N/A'), ', ') as severities,
                string_agg(DISTINCT COALESCE(category, 'N/A'), ', ') as categories,
                string_agg(DISTINCT COALESCE(outage_category, 'N/A'), ', ') as outage_categories
            ")
            ->whereBetween('started_at', [$dateStart, $dateEnd])
            ->groupBy('summary')
            ->havingRaw('COUNT(*) >= ?', [$minOccurrences])
            ->orderByDesc('occurrence_count')
            ->get();

        // Get monthly trend data (last 6 months) for recurring incidents
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $trendMonth = now()->subMonths($i)->format('Y-m');
            $trendMonthStart = \Carbon\Carbon::createFromFormat('Y-m', $trendMonth)->startOfMonth();
            $trendMonthEnd = \Carbon\Carbon::createFromFormat('Y-m', $trendMonth)->endOfMonth();

            $monthlyTrends[$trendMonth] = Incident::query()
                ->selectRaw('summary, COUNT(*) as count')
                ->whereBetween('started_at', [$trendMonthStart, $trendMonthEnd])
                ->groupBy('summary')
                ->havingRaw('COUNT(*) >= 2')
                ->get()
                ->pluck('count', 'summary')
                ->toArray();
        }

        return view('logs.recurring-incidents', compact('recurringIncidents', 'startDate', 'endDate', 'minOccurrences', 'monthlyTrends'));
    }

    /**
     * Display all incidents with the same summary (drill-down from recurring incidents).
     */
    public function incidentsBySummary(Request $request)
    {
        $summary = $request->input('summary');
        $perPage = $request->input('per_page', 15);

        if (empty($summary)) {
            return redirect()->route('logs.recurring-incidents')
                ->with('error', 'Summary parameter is required.');
        }

        $incidents = Incident::query()
            ->where('summary', $summary)
            ->orderByDesc('started_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('logs.incidents-by-summary', compact('incidents', 'summary'));
    }
}
