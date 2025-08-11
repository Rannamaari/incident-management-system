<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\ActionPoint;
use App\Models\Category;
use App\Models\OutageCategory;
use App\Models\FaultType;
use App\Models\ResolutionTeam;
use App\Http\Requests\CloseIncidentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IncidentController extends Controller
{
    /**
     * Display a listing of incidents with filtering and pagination.
     */
    public function index(Request $request)
    {
        // Get selected month (default to current month)
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $monthEnd = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

        // Get incidents for display (with pagination and filters)
        $incidents = Incident::query()
            ->search($request->search)
            ->status($request->status)
            ->severity($request->severity)
            ->orderByDesc('started_at')
            ->paginate(15)
            ->withQueryString();

        // Get monthly KPI data (for the selected month only)
        $monthlyIncidents = Incident::query()
            ->whereBetween('started_at', [$monthStart, $monthEnd])
            ->get();

        return view('incidents.index', compact('incidents', 'monthlyIncidents', 'selectedMonth'));
    }

    /**
     * Show the form for creating a new incident.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $outageCategories = OutageCategory::orderBy('name')->get();
        $faultTypes = FaultType::orderBy('name')->get();
        $resolutionTeams = ResolutionTeam::orderBy('name')->get();

        return view('incidents.create', compact('categories', 'outageCategories', 'faultTypes', 'resolutionTeams'));
    }

    /**
     * Store a newly created incident in storage.
     */
    public function store(Request $request)
    {
        // If creating a closed incident, use the CloseIncidentRequest for validation
        if ($request->input('status') === 'Closed') {
            $closeRequest = CloseIncidentRequest::createFrom($request);
            $closeRequest->setContainer(app())->setRedirector(app('redirect'));
            $closeRequest->validateResolved();
        }

        $validated = $this->validateIncident($request);

        // Handle new category creation
        $validated = $this->handleNewValues($request, $validated);

        $incident = new Incident();
        $this->fillIncidentData($incident, $validated, $request);
        $incident->save();

        // Handle log entries and action points
        $this->handleIncidentLogs($incident, $validated);
        $this->handleIncidentActionPoints($incident, $validated);

        return redirect()->route('incidents.index')
            ->with('success', 'Incident created successfully.');
    }

    /**
     * Display the specified incident.
     */
    public function show(Incident $incident)
    {
        $incident->load(['logs', 'actionPoints']); // Eager load logs and action points
        
        return view('incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the specified incident.
     */
    public function edit(Incident $incident)
    {
        $incident->load(['logs', 'actionPoints']); // Eager load logs and action points
        
        $categories = Category::orderBy('name')->get();
        $outageCategories = OutageCategory::orderBy('name')->get();
        $faultTypes = FaultType::orderBy('name')->get();
        $resolutionTeams = ResolutionTeam::orderBy('name')->get();

        return view('incidents.edit', compact('incident', 'categories', 'outageCategories', 'faultTypes', 'resolutionTeams'));
    }

    /**
     * Update the specified incident in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        // If closing the incident, use the CloseIncidentRequest for additional validation
        if ($request->input('status') === 'Closed') {
            $closeRequest = CloseIncidentRequest::createFrom($request);
            $closeRequest->setContainer(app())->setRedirector(app('redirect'));
            $closeRequest->validateResolved();
        }

        $validated = $this->validateIncident($request, $incident);

        // Handle new category creation
        $validated = $this->handleNewValues($request, $validated);


        // Business rule: Cannot close without resolved_at
        if ($validated['status'] === 'Closed' && empty($validated['resolved_at'])) {
            return back()->withErrors([
                'resolved_at' => 'Resolved date is required when closing an incident.'
            ])->withInput();
        }

        $this->fillIncidentData($incident, $validated, $request);
        $incident->save();

        // Handle log entries and action points
        $this->handleIncidentLogs($incident, $validated);
        $this->handleIncidentActionPoints($incident, $validated);

        return redirect()->route('incidents.index')
            ->with('success', 'Incident updated successfully.');
    }

    /**
     * Remove the specified incident from storage.
     */
    public function destroy(Incident $incident)
    {
        // Delete RCA file if exists
        if ($incident->rca_file_path && Storage::disk('public')->exists($incident->rca_file_path)) {
            Storage::disk('public')->delete($incident->rca_file_path);
        }

        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incident deleted successfully.');
    }

    /**
     * Get filtered incidents preview for export modal.
     */
    public function exportPreview(Request $request)
    {
        // Validate export parameters
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'resolution_team' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        // Build query with filters (same logic as export)
        $query = $this->buildExportQuery($request);
        
        // Get count and sample records
        $total = $query->count();
        $preview = $query->limit(10)->get(['incident_code', 'summary', 'category', 'status', 'severity', 'started_at', 'resolved_at']);

        return response()->json([
            'total' => $total,
            'preview' => $preview->map(function ($incident) {
                return [
                    'incident_code' => $incident->incident_code,
                    'summary' => substr($incident->summary, 0, 50) . (strlen($incident->summary) > 50 ? '...' : ''),
                    'category' => $incident->category ?? '',
                    'status' => $incident->status,
                    'severity' => $incident->severity,
                    'started_at' => $incident->started_at ? $incident->started_at->format('Y-m-d H:i') : '',
                    'resolved_at' => $incident->resolved_at ? $incident->resolved_at->format('Y-m-d H:i') : '',
                ];
            })
        ]);
    }

    /**
     * Export incidents to CSV with filters.
     */
    public function export(Request $request)
    {
        // Validate export parameters
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'resolution_team' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        // Build query with filters
        $query = $this->buildExportQuery($request);

        // Generate filename with current timestamp
        $filename = 'incidents_export_' . now('Indian/Maldives')->format('Y-m-d_H-i-s') . '.csv';

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
     * Validate incident data.
     */
    private function validateIncident(Request $request, ?Incident $incident = null): array
    {
        // Pre-process logs and action points to remove empty/template entries before validation
        $requestData = $request->all();
        
        // Filter logs - remove empty entries and template entries with "INDEX"
        if (isset($requestData['logs']) && is_array($requestData['logs'])) {
            $requestData['logs'] = array_filter($requestData['logs'], function ($log) {
                return !empty($log['occurred_at']) && !empty($log['note']) 
                    && !str_contains($log['occurred_at'], 'INDEX') 
                    && !str_contains($log['note'], 'INDEX');
            });
            $requestData['logs'] = array_values($requestData['logs']); // Re-index array
        }
        
        // Filter action points - remove empty entries and template entries with "INDEX"
        if (isset($requestData['action_points']) && is_array($requestData['action_points'])) {
            $requestData['action_points'] = array_filter($requestData['action_points'], function ($actionPoint) {
                return !empty($actionPoint['description']) && !empty($actionPoint['due_date'])
                    && !str_contains($actionPoint['description'], 'INDEX')
                    && !str_contains($actionPoint['due_date'], 'INDEX');
            });
            $requestData['action_points'] = array_values($requestData['action_points']); // Re-index array
        }

        // Create a new request instance with filtered data
        $filteredRequest = new Request($requestData);
        $filteredRequest->setUserResolver($request->getUserResolver());
        $filteredRequest->setRouteResolver($request->getRouteResolver());

        return $filteredRequest->validate([
            'summary' => ['required', 'string', 'max:1000'],
            'outage_category_id' => ['nullable', 'exists:outage_categories,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'fault_type_id' => ['nullable', 'exists:fault_types,id'],
            'resolution_team_id' => ['nullable', 'exists:resolution_teams,id'],
            'new_outage_category_name' => ['nullable', 'string', 'max:255'],
            'new_category_name' => ['nullable', 'string', 'max:255'],
            'new_fault_type_name' => ['nullable', 'string', 'max:255'],
            'new_resolution_team_name' => ['nullable', 'string', 'max:255'],
            'affected_services' => ['required', 'string', 'max:255'],
            'started_at' => ['required', 'date'],
            'resolved_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'fault_type' => ['nullable', 'string', 'max:255'],
            'outage_category' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'root_cause' => ['nullable', 'string'],
            'delay_reason' => ['nullable', 'string'],
            'resolution_team' => ['nullable', 'string', 'max:255'],
            'journey_started_at' => ['nullable', 'date'],
            'island_arrival_at' => ['nullable', 'date'],
            'work_started_at' => ['nullable', 'date'],
            'work_completed_at' => ['nullable', 'date'],
            'travel_time' => ['nullable', 'integer', 'min:0'],
            'work_time' => ['nullable', 'integer', 'min:0'],
            'pir_rca_no' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(Incident::STATUSES)],
            'severity' => ['required', 'string', Rule::in(Incident::SEVERITIES)],
            'corrective_actions' => ['nullable', 'string'],
            'workaround' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
            'logs' => ['nullable', 'array'],
            'logs.*.occurred_at' => ['required', 'date'],
            'logs.*.note' => ['required', 'string', 'max:1000'],
            'action_points' => ['nullable', 'array'],
            'action_points.*.description' => ['required', 'string'],
            'action_points.*.due_date' => ['required', 'date'],
            'action_points.*.completed' => ['boolean']
        ]);
    }

    /**
     * Fill incident with validated data and handle file upload.
     */
    private function fillIncidentData(Incident $incident, array $validated, Request $request): void
    {
        // Remove logs and action_points from validated data before filling
        unset($validated['logs'], $validated['action_points']);
        
        $incident->fill($validated);

        // Only set resolved_at if incident status is "Closed" and no resolved_at is provided
        if ($incident->status === 'Closed' && empty($incident->resolved_at) && !empty($incident->started_at)) {
            $incident->resolved_at = $incident->started_at->addHour();
        }
        
        // If incident is not closed, ensure resolved_at is null
        if ($incident->status !== 'Closed') {
            $incident->resolved_at = null;
        }
    }

    /**
     * Handle creation of new values for select fields.
     */
    private function handleNewValues(Request $request, array $validated): array
    {
        // Handle category (from datalist input)
        if (isset($validated['category']) && $validated['category']) {
            $category = Category::firstOrCreate(['name' => $validated['category']]);
            $validated['category_id'] = $category->id;
            $validated['category'] = $category->name; // Normalize the name
        }

        // Handle outage category (from datalist input)
        if (isset($validated['outage_category']) && $validated['outage_category']) {
            $outageCategory = OutageCategory::firstOrCreate(['name' => $validated['outage_category']]);
            $validated['outage_category_id'] = $outageCategory->id;
            $validated['outage_category'] = $outageCategory->name; // Normalize the name
        }

        // Handle fault type (from datalist input)
        if (isset($validated['fault_type']) && $validated['fault_type']) {
            $faultType = FaultType::firstOrCreate(['name' => $validated['fault_type']]);
            $validated['fault_type_id'] = $faultType->id;
            $validated['fault_type'] = $faultType->name; // Normalize the name
        }

        // Handle resolution team (from datalist input)
        if (isset($validated['resolution_team']) && $validated['resolution_team']) {
            $resolutionTeam = ResolutionTeam::firstOrCreate(['name' => $validated['resolution_team']]);
            $validated['resolution_team_id'] = $resolutionTeam->id;
            $validated['resolution_team'] = $resolutionTeam->name; // Normalize the name
        }

        // Handle new category from create form (for backward compatibility)
        if ($request->filled('new_category_name')) {
            $category = Category::firstOrCreate(['name' => $request->new_category_name]);
            $validated['category_id'] = $category->id;
            $validated['category'] = $category->name;
        }

        // Handle new outage category from create form (for backward compatibility)
        if ($request->filled('new_outage_category_name')) {
            $outageCategory = OutageCategory::firstOrCreate(['name' => $request->new_outage_category_name]);
            $validated['outage_category_id'] = $outageCategory->id;
            $validated['outage_category'] = $outageCategory->name;
        }

        // Handle new fault type from create form (for backward compatibility)
        if ($request->filled('new_fault_type_name')) {
            $faultType = FaultType::firstOrCreate(['name' => $request->new_fault_type_name]);
            $validated['fault_type_id'] = $faultType->id;
            $validated['fault_type'] = $faultType->name;
        }

        // Handle new resolution team from create form (for backward compatibility)
        if ($request->filled('new_resolution_team_name')) {
            $resolutionTeam = ResolutionTeam::firstOrCreate(['name' => $request->new_resolution_team_name]);
            $validated['resolution_team_id'] = $resolutionTeam->id;
            $validated['resolution_team'] = $resolutionTeam->name;
        }

        // Set text fields from existing ID selections (fallback)
        if (isset($validated['category_id']) && $validated['category_id'] && !isset($validated['category'])) {
            $category = Category::find($validated['category_id']);
            $validated['category'] = $category->name ?? null;
        }

        if (isset($validated['outage_category_id']) && $validated['outage_category_id'] && !isset($validated['outage_category'])) {
            $outageCategory = OutageCategory::find($validated['outage_category_id']);
            $validated['outage_category'] = $outageCategory->name ?? null;
        }

        if (isset($validated['fault_type_id']) && $validated['fault_type_id'] && !isset($validated['fault_type'])) {
            $faultType = FaultType::find($validated['fault_type_id']);
            $validated['fault_type'] = $faultType->name ?? null;
        }

        if (isset($validated['resolution_team_id']) && $validated['resolution_team_id'] && !isset($validated['resolution_team'])) {
            $resolutionTeam = ResolutionTeam::find($validated['resolution_team_id']);
            $validated['resolution_team'] = $resolutionTeam->name ?? null;
        }

        return $validated;
    }

    /**
     * Handle incident logs creation/update.
     */
    private function handleIncidentLogs(Incident $incident, array $validated): void
    {
        if (!isset($validated['logs']) || !is_array($validated['logs'])) {
            return;
        }

        // For updates, we'll delete existing logs and recreate them
        // This is simpler than trying to diff and update individual logs
        $incident->logs()->delete();

        // Create new logs
        foreach ($validated['logs'] as $logData) {
            if (empty($logData['occurred_at']) || empty($logData['note'])) {
                continue; // Skip empty logs
            }

            IncidentLog::create([
                'incident_id' => $incident->id,
                'occurred_at' => $logData['occurred_at'],
                'note' => $logData['note'],
            ]);
        }
    }

    /**
     * Handle incident action points creation/update.
     */
    private function handleIncidentActionPoints(Incident $incident, array $validated): void
    {
        if (!isset($validated['action_points']) || !is_array($validated['action_points'])) {
            return;
        }

        // For updates, we'll delete existing action points and recreate them
        // This is simpler than trying to diff and update individual action points
        $incident->actionPoints()->delete();

        // Create new action points
        foreach ($validated['action_points'] as $actionPointData) {
            if (empty($actionPointData['description']) || empty($actionPointData['due_date'])) {
                continue; // Skip empty action points
            }

            ActionPoint::create([
                'incident_id' => $incident->id,
                'description' => $actionPointData['description'],
                'due_date' => $actionPointData['due_date'],
                'completed' => isset($actionPointData['completed']) && $actionPointData['completed'] == '1',
                'completed_at' => (isset($actionPointData['completed']) && $actionPointData['completed'] == '1') ? now() : null,
            ]);
        }
    }

    /**
     * Build export query with filters.
     */
    private function buildExportQuery(Request $request)
    {
        $query = Incident::query();

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        // Resolution team filter
        if ($request->filled('resolution_team')) {
            $query->where('resolution_team', 'LIKE', '%' . $request->resolution_team . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by most recent first
        $query->orderBy('started_at', 'desc');

        return $query;
    }

}