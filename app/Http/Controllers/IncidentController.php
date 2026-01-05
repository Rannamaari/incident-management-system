<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\ActionPoint;
use App\Models\Category;
use App\Models\OutageCategory;
use App\Models\FaultType;
use App\Models\ResolutionTeam;
use App\Models\Rca;
use App\Http\Requests\CloseIncidentRequest;
use App\Services\IncidentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
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

        // Return JSON for AJAX requests (for RCA incident search)
        if ($request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $incidents = Incident::query()
                ->search($request->search)
                ->orderByDesc('started_at')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $incidents
            ]);
        }

        // Get per_page value from request, default to 15
        $perPage = $request->input('per_page', 15);
        // Validate per_page is one of the allowed values
        $perPage = in_array($perPage, [15, 25, 50, 100]) ? $perPage : 15;

        // Default to showing only Open incidents if no status filter is applied
        $defaultStatus = $request->has('status') ? $request->status : 'Open';

        // Get incidents for display (with pagination and filters)
        $incidents = Incident::query()
            ->search($request->search)
            ->status($defaultStatus)
            ->severity($request->severity)
            ->when($request->has('rca_required') && $request->rca_required === '1', function ($query) {
                return $query->where('rca_required', true);
            })
            ->when($request->has('sla_breached') && $request->sla_breached === '1', function ($query) {
                return $query->where('exceeded_sla', true);
            })
            ->orderByDesc('started_at')
            ->paginate($perPage)
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

        // Load sites with region and location for site selection (only active sites)
        $regions = \App\Models\Region::with(['sites.technologies'])->orderBy('name')->get();
        $sites = \App\Models\Site::with(['region', 'location', 'technologies'])
            ->active()
            ->orderBy('site_code')
            ->get();

        // Load FBB islands for FBB service selection (only active islands)
        $fbbIslands = \App\Models\FbbIsland::with('region')
            ->where('is_active', true)
            ->orderBy('island_name')
            ->get();

        // Load ISP links for ISP outage reporting
        $ispLinks = \App\Models\IspLink::orderBy('circuit_id')->get();

        return view('incidents.create', compact('categories', 'outageCategories', 'faultTypes', 'resolutionTeams', 'regions', 'sites', 'fbbIslands', 'ispLinks'));
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

        // Custom validation: site counts required when site services selected
        $affectedServices = $request->input('affected_services', []);
        // Ensure affected_services is always an array
        if (!is_array($affectedServices)) {
            $affectedServices = [];
        }
        if (in_array('Single Site', $affectedServices) || in_array('Multiple Site', $affectedServices)) {
            $totalSites = ($validated['sites_2g_impacted'] ?? 0)
                        + ($validated['sites_3g_impacted'] ?? 0)
                        + ($validated['sites_4g_impacted'] ?? 0)
                        + ($validated['sites_5g_impacted'] ?? 0);

            if ($totalSites === 0) {
                return back()->withErrors([
                    'sites_2g_impacted' => 'Please specify the number of impacted sites when Single Site or Multiple Site is selected.'
                ])->withInput();
            }
        }

        // Custom validation: FBB count required when FBB service selected
        if (in_array('Single FBB', $affectedServices)) {
            $fbbCount = $validated['fbb_impacted'] ?? 0;

            if ($fbbCount === 0) {
                return back()->withErrors([
                    'fbb_impacted' => 'Please specify the number of impacted FBB when Single FBB is selected.'
                ])->withInput();
            }
        }

        // Custom validation: ISP outage fields required when ISP service selected
        if (in_array('ISP', $affectedServices)) {
            $errors = [];

            if (empty($validated['isp_links']) || count($validated['isp_links']) === 0) {
                $errors['isp_links'] = 'Please select at least one ISP link when ISP is selected as affected service.';
            } else {
                // Validate each ISP link
                foreach ($validated['isp_links'] as $linkId => $linkData) {
                    $ispLink = \App\Models\IspLink::find($linkId);

                    if (!$ispLink) {
                        $errors["isp_links.{$linkId}"] = "Invalid ISP link selected.";
                        continue;
                    }

                    // Validate capacity lost doesn't exceed ISP link's total capacity
                    if (isset($linkData['capacity_lost']) && $linkData['capacity_lost'] > $ispLink->total_capacity_gbps) {
                        $errors["isp_links.{$linkId}.capacity_lost"] = "Capacity lost ({$linkData['capacity_lost']} Gbps) cannot exceed {$ispLink->circuit_id}'s total capacity ({$ispLink->total_capacity_gbps} Gbps).";
                    }
                }
            }

            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }
        }

        // Handle new category creation
        $validated = $this->handleNewValues($request, $validated);

        // Check for duplicate incident (same summary at any time)
        // Allow user to bypass this check by setting 'confirm_duplicate' field
        if (!$request->input('confirm_duplicate')) {
            $duplicate = Incident::where('summary', $validated['summary'])
                ->with('creator')
                ->first();

            if ($duplicate) {
                return back()->withErrors([
                    'duplicate' => 'A similar incident already exists: "' . $duplicate->summary . '" created by ' .
                        ($duplicate->creator ? $duplicate->creator->name : 'Unknown') . ' on ' .
                        $duplicate->created_at->format('M d, Y H:i') . ' (started at: ' . $duplicate->started_at->format('M d, Y H:i') . '). If you are sure you want to create this duplicate incident, please click "Create Anyway" below.'
                ])->withInput();
            }
        }

        $incident = new Incident();
        $this->fillIncidentData($incident, $validated, $request);
        $incident->created_by = auth()->id();
        $incident->updated_by = auth()->id();

        // Try to save with retry logic for concurrent incident_code generation
        $maxRetries = 3;
        $attempt = 0;
        $incidentSaved = false;

        while ($attempt < $maxRetries) {
            try {
                // Wrap in transaction to ensure atomicity with lockForUpdate
                \DB::transaction(function () use ($incident) {
                    $incident->save();
                });

                // Success - break out of retry loop
                $incidentSaved = true;
                break;

            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a unique constraint violation on incident_code
                if ($e->getCode() === '23505' && str_contains($e->getMessage(), 'incident_code')) {
                    $attempt++;

                    if ($attempt >= $maxRetries) {
                        // All retries exhausted - log and return error
                        \Log::error('Failed to generate unique incident code after ' . $maxRetries . ' attempts', [
                            'user_id' => auth()->id(),
                            'started_at' => $incident->started_at,
                            'error' => $e->getMessage()
                        ]);

                        return back()->withErrors([
                            'incident_code' => 'Unable to generate unique incident code. Please try again in a moment.'
                        ])->withInput();
                    }

                    // Retry: Clear the incident_code to force regeneration
                    $incident->incident_code = null;

                    // Small delay to reduce contention (10-50ms)
                    usleep(random_int(10000, 50000));

                    continue; // Retry
                }

                // Not a duplicate incident_code error - rethrow
                throw $e;

            } catch (\InvalidArgumentException $e) {
                // Convert model validation exception to user-friendly error
                return back()->withErrors([
                    'delay_reason' => $e->getMessage()
                ])->withInput();
            }
        }

        // Handle RCA file upload
        $this->handleRcaFileUpload($incident, $request);

        // Handle log entries and action points
        $this->handleIncidentLogs($incident, $validated);
        $this->handleIncidentActionPoints($incident, $validated);

        // Handle site attachments
        $this->handleSiteAttachments($incident, $request);

        // Handle FBB island attachments
        $this->handleFbbIslandAttachments($incident, $request);

        // Handle ISP links attachments with metrics
        if (isset($validated['isp_links']) && is_array($validated['isp_links'])) {
            $ispLinksData = [];
            foreach ($validated['isp_links'] as $linkId => $linkData) {
                $ispLinksData[$linkId] = [
                    'capacity_lost_gbps' => $linkData['capacity_lost'] ?? 0,
                    'services_impacted' => $linkData['services_impacted'] ?? '',
                    'traffic_rerouted' => filter_var($linkData['traffic_rerouted'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'reroute_details' => $linkData['reroute_details'] ?? null,
                ];
            }
            $incident->ispLinks()->sync($ispLinksData);
        }

        // Send email notification for incident creation
        if ($incidentSaved) {
            try {
                $notificationService = new IncidentNotificationService();
                $notificationService->sendCreatedNotification($incident);
            } catch (\Exception $e) {
                \Log::error('Failed to send incident created notification', [
                    'incident_id' => $incident->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the request if notification fails
            }
        }

        return redirect()->route('incidents.index')
            ->with('success', 'Incident created successfully.');
    }

    /**
     * Display the specified incident.
     */
    public function show(Incident $incident)
    {
        $incident->load([
            'logs',
            'actionPoints',
            'creator',
            'updater',
            'activityLogs',
            'activityLogs.user',
            'sites.region',
            'sites.location',
            'sites.technologies',
            'fbbIslands.region',
            'ispLinks',
            'category',
            'outageCategory',
            'faultType',
            'resolutionTeam'
        ]); // Eager load all relationships needed for display and copy function

        // Mark incident as viewed by current user
        $incident->markAsViewed();

        return view('incidents.show', compact('incident'));
    }

    /**
     * Show the form for editing the specified incident.
     */
    public function edit(Incident $incident)
    {
        $incident->load(['logs', 'actionPoints', 'ispLinks']); // Eager load logs, action points, and ISP links with pivot data

        $categories = Category::orderBy('name')->get();
        $outageCategories = OutageCategory::orderBy('name')->get();
        $faultTypes = FaultType::orderBy('name')->get();
        $resolutionTeams = ResolutionTeam::orderBy('name')->get();

        // Load ISP links for ISP outage reporting
        $ispLinks = \App\Models\IspLink::orderBy('circuit_id')->get();

        // Prepare selected ISP links data structure for the view
        // Use (object) to ensure empty arrays become {} instead of []
        $selectedIspLinksData = $incident->ispLinks->mapWithKeys(function($link) {
            return [$link->id => [
                'capacity_lost' => $link->pivot->capacity_lost_gbps ?? 0,
                'services_impacted' => $link->pivot->services_impacted ?? '',
                'traffic_rerouted' => (bool)($link->pivot->traffic_rerouted ?? false),
                'reroute_details' => $link->pivot->reroute_details ?? ''
            ]];
        })->toArray();

        // If empty, convert to object for proper JSON encoding
        if (empty($selectedIspLinksData)) {
            $selectedIspLinksData = new \stdClass();
        }

        // Prepare expanded links (all selected links should be expanded by default)
        $expandedLinksData = $incident->ispLinks->pluck('id')->mapWithKeys(function($id) {
            return [$id => true];
        })->toArray();

        // If empty, convert to object for proper JSON encoding
        if (empty($expandedLinksData)) {
            $expandedLinksData = new \stdClass();
        }

        return view('incidents.edit', compact('incident', 'categories', 'outageCategories', 'faultTypes', 'resolutionTeams', 'ispLinks', 'selectedIspLinksData', 'expandedLinksData'));
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

        // Custom validation: site counts required when site services selected
        $affectedServices = $request->input('affected_services', []);
        // Ensure affected_services is always an array
        if (!is_array($affectedServices)) {
            $affectedServices = [];
        }
        if (in_array('Single Site', $affectedServices) || in_array('Multiple Site', $affectedServices)) {
            $totalSites = ($validated['sites_2g_impacted'] ?? 0)
                        + ($validated['sites_3g_impacted'] ?? 0)
                        + ($validated['sites_4g_impacted'] ?? 0)
                        + ($validated['sites_5g_impacted'] ?? 0);

            if ($totalSites === 0) {
                return back()->withErrors([
                    'sites_2g_impacted' => 'Please specify the number of impacted sites when Single Site or Multiple Site is selected.'
                ])->withInput();
            }
        }

        // Custom validation: FBB count required when FBB service selected
        if (in_array('Single FBB', $affectedServices)) {
            $fbbCount = $validated['fbb_impacted'] ?? 0;

            if ($fbbCount === 0) {
                return back()->withErrors([
                    'fbb_impacted' => 'Please specify the number of impacted FBB when Single FBB is selected.'
                ])->withInput();
            }
        }

        // Custom validation: ISP links required when ISP service selected
        if (in_array('ISP', $affectedServices)) {
            $errors = [];

            // Check if at least one ISP link is selected
            if (empty($validated['isp_links']) || count($validated['isp_links']) === 0) {
                $errors['isp_links'] = 'Please select at least one ISP link when ISP is selected as affected service.';
            } else {
                // Validate each selected ISP link
                foreach ($validated['isp_links'] as $linkId => $linkData) {
                    $ispLink = \App\Models\IspLink::find($linkId);

                    if (!$ispLink) {
                        $errors["isp_links.{$linkId}"] = "Invalid ISP link selected.";
                        continue;
                    }

                    // Validate capacity lost doesn't exceed ISP link's total capacity
                    if (isset($linkData['capacity_lost']) && $linkData['capacity_lost'] > $ispLink->total_capacity_gbps) {
                        $errors["isp_links.{$linkId}.capacity_lost"] = "Capacity lost ({$linkData['capacity_lost']} Gbps) cannot exceed {$ispLink->circuit_id}'s total capacity ({$ispLink->total_capacity_gbps} Gbps).";
                    }
                }
            }

            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }
        }

        // Handle new category creation
        $validated = $this->handleNewValues($request, $validated);


        // Business rule: Cannot close without resolved_at
        if ($validated['status'] === 'Closed' && empty($validated['resolved_at'])) {
            return back()->withErrors([
                'resolved_at' => 'Resolved date is required when closing an incident.'
            ])->withInput();
        }

        // Track if incident status is changing to Closed (for notification)
        $wasOpen = $incident->status !== 'Closed';
        $isNowClosed = $validated['status'] === 'Closed';

        $this->fillIncidentData($incident, $validated, $request);
        $incident->updated_by = auth()->id();

        // Try to save and catch model-level validation errors
        try {
            $incident->save();
        } catch (\InvalidArgumentException $e) {
            // Convert model validation exception to user-friendly error
            return back()->withErrors([
                'delay_reason' => $e->getMessage()
            ])->withInput();
        }

        // Send email notification if incident was just closed
        if ($wasOpen && $isNowClosed) {
            try {
                $notificationService = new IncidentNotificationService();
                $notificationService->sendClosedNotification($incident);
            } catch (\Exception $e) {
                \Log::error('Failed to send incident closed notification', [
                    'incident_id' => $incident->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the request if notification fails
            }
        }

        // Auto-create RCA for Critical or High severity incidents when closed
        if ($validated['status'] === 'Closed' && in_array($incident->severity, ['Critical', 'High'])) {
            // Check if RCA doesn't already exist for this incident
            if (!$incident->rca) {
                // Generate RCA number
                $latestRca = Rca::latest('id')->first();
                $rcaNumber = 'RCA-' . date('Y') . '-' . str_pad(($latestRca ? $latestRca->id + 1 : 1), 4, '0', STR_PAD_LEFT);

                // Create RCA
                Rca::create([
                    'incident_id' => $incident->id,
                    'title' => 'RCA for ' . $incident->title,
                    'rca_number' => $rcaNumber,
                    'problem_description' => $incident->description ?? '',
                    'status' => 'Draft',
                    'created_by' => auth()->id(),
                ]);
            }
        }

        // Handle RCA file upload
        $this->handleRcaFileUpload($incident, $request);

        // Handle log entries and action points
        $this->handleIncidentLogs($incident, $validated);
        $this->handleIncidentActionPoints($incident, $validated);

        // Handle ISP links attachments with metrics
        if (isset($validated['isp_links']) && is_array($validated['isp_links'])) {
            $ispLinksData = [];
            foreach ($validated['isp_links'] as $linkId => $linkData) {
                $ispLinksData[$linkId] = [
                    'capacity_lost_gbps' => $linkData['capacity_lost'] ?? 0,
                    'services_impacted' => $linkData['services_impacted'] ?? '',
                    'traffic_rerouted' => filter_var($linkData['traffic_rerouted'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'reroute_details' => $linkData['reroute_details'] ?? null,
                ];
            }
            $incident->ispLinks()->sync($ispLinksData);
        } else {
            // If ISP is not in affected services, detach all ISP links
            $affectedServices = $request->input('affected_services', []);
            // Ensure affected_services is always an array
            if (!is_array($affectedServices)) {
                $affectedServices = [];
            }
            if (!in_array('ISP', $affectedServices)) {
                $incident->ispLinks()->detach();
            }
        }

        return redirect()->route('incidents.index')
            ->with('success', 'Incident updated successfully.');
    }

    /**
     * Close an incident with root cause.
     */
    public function close(Request $request, Incident $incident)
    {
        // Validate input
        $validated = $request->validate([
            'resolved_at' => ['required', 'date'],
            'root_cause' => ['required', 'string', 'min:10'],
            'delay_reason' => ['nullable', 'string', 'min:10'],
            'travel_time' => ['nullable', 'integer', 'min:0'],
            'work_time' => ['nullable', 'integer', 'min:0'],
        ]);

        // Update incident
        $incident->status = 'Closed';
        $incident->resolved_at = $validated['resolved_at'];
        $incident->root_cause = $validated['root_cause'];
        $incident->delay_reason = $validated['delay_reason'] ?? null;
        $incident->travel_time = $validated['travel_time'] ?? null;
        $incident->work_time = $validated['work_time'] ?? null;
        $incident->updated_by = auth()->id();

        // Try to save and catch model-level validation errors
        try {
            $incident->save();
        } catch (\InvalidArgumentException $e) {
            // Convert model validation exception to user-friendly error
            return back()->withErrors([
                'delay_reason' => $e->getMessage()
            ])->withInput();
        }

        // Send email notification for incident closure
        try {
            $notificationService = new IncidentNotificationService();
            $notificationService->sendClosedNotification($incident);
        } catch (\Exception $e) {
            \Log::error('Failed to send incident closed notification', [
                'incident_id' => $incident->id,
                'error' => $e->getMessage(),
            ]);
            // Don't fail the request if notification fails
        }

        return redirect()->route('incidents.index')
            ->with('success', 'Incident closed successfully.');
    }

    /**
     * Remove the specified incident from storage.
     */
    public function destroy(Incident $incident)
    {
        // Delete RCA file if exists
        if ($incident->rca_file_path) {
            $filePath = storage_path('app/' . $incident->rca_file_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $incident->delete();

        // Redirect back to the page where delete was initiated
        // Check if referer contains 'logs' to redirect to logs page
        $referer = request()->headers->get('referer');
        if ($referer && str_contains($referer, '/logs')) {
            return redirect()->route('logs.index')
                ->with('success', 'Incident deleted successfully.');
        }

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
        
        // Handle affected_services - convert string to array if needed (for close modal compatibility)
        if (isset($requestData['affected_services']) && is_string($requestData['affected_services'])) {
            // If it's a string (from close modal), convert to array for validation
            $requestData['affected_services'] = array_filter(array_map('trim', explode(',', $requestData['affected_services'])));
        }
        
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
            'isp_links' => ['nullable', 'array'],
            'isp_links.*.capacity_lost' => ['required', 'numeric', 'min:0'],
            'isp_links.*.services_impacted' => ['required', 'string'],
            'isp_links.*.traffic_rerouted' => ['required', 'in:true,false,1,0'],
            'isp_links.*.reroute_details' => ['nullable', 'string'],
            'new_outage_category_name' => ['nullable', 'string', 'max:255'],
            'new_category_name' => ['nullable', 'string', 'max:255'],
            'new_fault_type_name' => ['nullable', 'string', 'max:255'],
            'new_resolution_team_name' => ['nullable', 'string', 'max:255'],
            'affected_services' => ['required', 'array', 'min:1'],
            'affected_services.*' => ['required', 'string', 'in:Cell,Single FBB,Single Site,Multiple Site,P2P,ILL,SIP,IPTV,Peering,Mobile Data,ISP,Others'],
            'sites_2g_impacted' => ['nullable', 'integer', 'min:0'],
            'sites_3g_impacted' => ['nullable', 'integer', 'min:0'],
            'sites_4g_impacted' => ['nullable', 'integer', 'min:0'],
            'sites_5g_impacted' => ['nullable', 'integer', 'min:0'],
            'fbb_impacted' => ['nullable', 'integer', 'min:0'],
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
            'action_points.*.completed' => ['boolean'],
            'rca_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB max
        ]);
    }

    /**
     * Fill incident with validated data and handle file upload.
     */
    private function fillIncidentData(Incident $incident, array $validated, Request $request): void
    {
        // Remove logs and action_points from validated data before filling
        unset($validated['logs'], $validated['action_points']);
        
        // Convert affected_services array to comma-separated string
        if (isset($validated['affected_services']) && is_array($validated['affected_services'])) {
            $validated['affected_services'] = implode(', ', $validated['affected_services']);
        }
        
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
     * Handle site attachments for the incident.
     */
    private function handleSiteAttachments(Incident $incident, Request $request): void
    {
        $selectedSites = $request->input('selected_sites', []);

        if (empty($selectedSites)) {
            return;
        }

        // Prepare data for pivot table attachment
        $siteData = [];
        foreach ($selectedSites as $siteId => $technologiesJson) {
            $technologies = json_decode($technologiesJson, true);

            if (!empty($technologies) && is_array($technologies)) {
                $siteData[$siteId] = [
                    'affected_technologies' => json_encode($technologies)
                ];
            }
        }

        // Attach sites to incident with affected technologies
        if (!empty($siteData)) {
            $incident->sites()->attach($siteData);
        }
    }

    /**
     * Handle FBB island attachments for the incident.
     */
    private function handleFbbIslandAttachments(Incident $incident, Request $request): void
    {
        $selectedFbbIslands = $request->input('selected_fbb_islands', []);

        if (empty($selectedFbbIslands)) {
            return;
        }

        // Attach FBB islands to incident
        $incident->fbbIslands()->attach($selectedFbbIslands);
    }

    /**
     * Handle RCA file upload.
     */
    private function handleRcaFileUpload(Incident $incident, Request $request): void
    {
        if ($request->hasFile('rca_file')) {
            $file = $request->file('rca_file');
            
            // Validate file type (PDF, DOC, DOCX)
            $allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $allowedExtensions = ['pdf', 'doc', 'docx'];
            
            $fileExtension = strtolower($file->getClientOriginalExtension());
            $fileMimeType = $file->getMimeType();
            
            // Validate extension
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['rca_file' => ['The RCA file must be a PDF or Word document (PDF, DOC, or DOCX).']]
                );
            }
            
            // Validate MIME type
            if (!in_array($fileMimeType, $allowedMimes)) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['rca_file' => ['The RCA file must be a valid PDF or Word document.']]
                );
            }
            
            // Validate file size (10MB max)
            if ($file->getSize() > 10 * 1024 * 1024) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['rca_file' => ['The RCA file size must not exceed 10MB.']]
                );
            }
            
            // Delete old RCA file if exists
            if ($incident->rca_file_path) {
                $oldPath = storage_path('app/' . $incident->rca_file_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Generate unique filename
            $filename = $incident->incident_code . '-' . time() . '.' . $fileExtension;
            
            // Store file in storage/app/rca directory (not public)
            $path = $file->storeAs('rca', $filename);
            
            // Update incident with RCA file info
            $incident->update([
                'rca_file_path' => $path,
                'rca_received_at' => now(),
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

    /**
     * Show the Excel import form.
     */
    public function showImport()
    {
        return view('incidents.import');
    }

    /**
     * Handle Excel file import.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return back()->withErrors(['excel_file' => 'Excel file is empty or has no data rows.']);
            }

            // First row is headers - find column indices
            $headers = array_map('strtolower', array_map('trim', $rows[0]));
            $headerMap = [];
            
            // Map common column name variations
            foreach ($headers as $index => $header) {
                $header = strtolower(trim($header));
                if (stripos($header, 'summary') !== false || stripos($header, 'incident summary') !== false || stripos($header, 'description') !== false) {
                    $headerMap['summary'] = $index;
                } elseif (stripos($header, 'start') !== false && (stripos($header, 'date') !== false || stripos($header, 'time') !== false)) {
                    $headerMap['started_at'] = $index;
                } elseif (stripos($header, 'resolution') !== false && (stripos($header, 'date') !== false || stripos($header, 'time') !== false)) {
                    $headerMap['resolved_at'] = $index;
                } elseif (stripos($header, 'resolved') !== false && (stripos($header, 'date') !== false || stripos($header, 'time') !== false)) {
                    $headerMap['resolved_at'] = $index;
                } elseif (stripos($header, 'severity') !== false) {
                    $headerMap['severity'] = $index;
                }
            }

            // Validate required columns
            $required = ['summary', 'started_at', 'severity'];
            $missing = [];
            foreach ($required as $field) {
                if (!isset($headerMap[$field])) {
                    $missing[] = $field;
                }
            }

            if (!empty($missing)) {
                return back()->withErrors([
                    'excel_file' => 'Missing required columns: ' . implode(', ', $missing) . '. Please ensure your Excel file has columns for: Incident Summary, Start Date/Time, and Severity.'
                ])->withInput();
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            // Process data rows (skip header row)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Extract data based on header map
                    $summary = isset($headerMap['summary']) && isset($row[$headerMap['summary']]) 
                        ? trim($row[$headerMap['summary']]) : '';
                    
                    if (empty($summary)) {
                        $skipped++;
                        continue;
                    }

                    $startedAt = null;
                    if (isset($headerMap['started_at']) && isset($row[$headerMap['started_at']])) {
                        $startedAt = $this->parseExcelDate($row[$headerMap['started_at']]);
                    }

                    $resolvedAt = null;
                    if (isset($headerMap['resolved_at']) && isset($row[$headerMap['resolved_at']])) {
                        $resolvedAt = $this->parseExcelDate($row[$headerMap['resolved_at']]);
                    }

                    $severity = isset($headerMap['severity']) && isset($row[$headerMap['severity']]) 
                        ? trim($row[$headerMap['severity']]) : 'Low';
                    
                    // Validate severity
                    if (!in_array($severity, Incident::SEVERITIES)) {
                        $severity = 'Low'; // Default to Low if invalid
                    }

                    // Determine status based on resolved_at
                    $status = $resolvedAt ? 'Closed' : 'Open';

                    // Check if delay_reason is needed (duration > 5 hours for closed incidents)
                    $delayReason = null;
                    if ($status === 'Closed' && $startedAt && $resolvedAt) {
                        $durationHours = $startedAt->diffInHours($resolvedAt);
                        if ($durationHours > 5) {
                            // Set a default delay reason for imports
                            $delayReason = 'Imported from Excel - duration exceeded 5 hours';
                        }
                    }

                    // Create incident
                    $incident = Incident::create([
                        'summary' => $summary,
                        'started_at' => $startedAt ?: now(),
                        'resolved_at' => $resolvedAt,
                        'severity' => $severity,
                        'status' => $status,
                        'affected_services' => 'Cell', // Default value (will be stored as string)
                        'category' => 'ICT', // Default value
                        'outage_category' => 'Unknown', // Default value (required field)
                        'delay_reason' => $delayReason,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    // Get more detailed error info
                    if ($e instanceof \Illuminate\Database\QueryException) {
                        $errorMsg = 'Database error: ' . $e->getMessage();
                    } elseif ($e instanceof \InvalidArgumentException) {
                        $errorMsg = 'Validation error: ' . $e->getMessage();
                    }
                    $errors[] = "Row " . ($i + 1) . ": " . $errorMsg;
                    $skipped++;
                    
                    // Log first few errors for debugging
                    if (count($errors) <= 5) {
                        \Log::error("Import error on row " . ($i + 1), [
                            'error' => $errorMsg,
                            'summary' => $summary ?? 'N/A',
                            'started_at' => $startedAt ?? 'N/A',
                            'resolved_at' => $resolvedAt ?? 'N/A',
                            'severity' => $severity ?? 'N/A',
                        ]);
                    }
                }
            }

            $message = "Import completed: {$imported} incidents imported";
            if ($skipped > 0) {
                $message .= ", {$skipped} rows skipped";
            }
            if (!empty($errors)) {
                $message .= ". Errors: " . count($errors);
                // Show first 10 errors in the message
                $errorPreview = array_slice($errors, 0, 10);
                $message .= "\n\nFirst errors:\n" . implode("\n", $errorPreview);
                if (count($errors) > 10) {
                    $message .= "\n... and " . (count($errors) - 10) . " more errors";
                }
            }

            $redirect = redirect()->route('incidents.index');
            
            if ($imported > 0) {
                $redirect->with('success', $message);
            } else {
                $redirect->with('error', $message);
            }
            
            if (!empty($errors)) {
                $redirect->with('import_errors', $errors);
            }
            
            return $redirect;

        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Error processing Excel file: ' . $e->getMessage()]);
        }
    }

    /**
     * Parse Excel date value.
     */
    private function parseExcelDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // If it's already a DateTime object
        if ($value instanceof \DateTime) {
            return \Carbon\Carbon::instance($value)->setTimezone('Indian/Maldives');
        }

        // If it's a numeric value (Excel date serial number)
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return \Carbon\Carbon::instance($date)->setTimezone('Indian/Maldives');
            } catch (\Exception $e) {
                // Try parsing as string
            }
        }

        // Try parsing as string date
        try {
            return \Carbon\Carbon::parse($value)->setTimezone('Indian/Maldives');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Add a timeline update to an incident
     */
    public function addTimelineUpdate(Request $request, Incident $incident)
    {
        // Prevent adding updates to closed incidents
        if ($incident->status === 'Closed') {
            return back()->with('error', 'Cannot add updates to closed incidents.');
        }

        $validated = $request->validate([
            'timeline_note' => 'required|string|min:5',
        ]);

        // Get existing timeline or initialize empty array
        $timeline = $incident->timeline ?? [];

        // Add new timeline entry
        $timeline[] = [
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'note' => $validated['timeline_note'],
        ];

        // Update incident
        $incident->timeline = $timeline;
        $incident->updated_by = auth()->id();
        $incident->save();

        // Send email notification for incident update
        try {
            $notificationService = new IncidentNotificationService();
            $notificationService->sendUpdatedNotification($incident, $validated['timeline_note'], auth()->user()->name);
        } catch (\Exception $e) {
            \Log::error('Failed to send incident updated notification', [
                'incident_id' => $incident->id,
                'error' => $e->getMessage(),
            ]);
            // Don't fail the request if notification fails
        }

        return back()->with('success', 'Timeline update added successfully.');
    }

    /**
     * Manually send notification for an incident
     */
    public function sendNotification(Incident $incident)
    {
        // Check if notifications are enabled
        if (!Config::get('incident-notifications.enabled', false)) {
            return back()->with('error', 'Email notifications are disabled. Please enable INCIDENT_NOTIFICATIONS_ENABLED in your .env file.');
        }

        // Initialize notification service
        $notificationService = new IncidentNotificationService();

        // Check if there are any recipients configured for this severity
        $recipients = $notificationService->getConfiguredLevels()
            ->filter(function ($level) use ($incident) {
                return $level->shouldReceiveForSeverity($incident->severity);
            })
            ->flatMap(function ($level) {
                return $level->activeRecipients;
            })
            ->pluck('email')
            ->unique()
            ->values()
            ->toArray();

        if (empty($recipients)) {
            return back()->with('warning', 'No email recipients configured for ' . $incident->severity . ' severity incidents. Please add recipients in Notification Settings.');
        }

        try {
            // Send appropriate notification based on incident status
            if ($incident->status === 'Closed') {
                $notificationService->sendClosedNotification($incident);
                $message = 'Incident closure notification sent successfully to ' . count($recipients) . ' recipient(s).';
            } else {
                $notificationService->sendCreatedNotification($incident);
                $message = 'Incident notification sent successfully to ' . count($recipients) . ' recipient(s).';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Failed to manually send incident notification', [
                'incident_id' => $incident->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send notification: ' . $e->getMessage());
        }
    }

    /**
     * Get formatted incident text for copying to clipboard (Server-Side Option B)
     */
    public function getCopyText(Incident $incident)
    {
        $incident->load(['sites.technologies', 'sites.region', 'sites.location', 'fbbIslands.region', 'category', 'outageCategory', 'faultType', 'resolutionTeam', 'creator']);

        // Format affected services
        $services = is_array($incident->affected_services)
            ? $incident->affected_services
            : explode(',', $incident->affected_services ?? '');
        $services = array_filter($services); // Remove empty values

        // Format services as numbered list (1. 2. 3.)
        $servicesText = null;
        if (!empty($services)) {
            $serviceLines = [];
            $counter = 1;
            foreach ($services as $service) {
                $serviceLines[] = "{$counter}. " . trim($service);
                $counter++;
            }
            $servicesText = implode("\n", $serviceLines);
        }

        // Format impacted sites
        $sitesText = null;
        if ($incident->sites && $incident->sites->isNotEmpty()) {
            $siteLines = [];
            foreach ($incident->sites as $site) {
                $techs = $site->pivot->affected_technologies ?? [];
                if (is_string($techs)) {
                    $techs = json_decode($techs, true) ?? [];
                }
                $techStr = !empty($techs) && is_array($techs) ? ' (' . implode(', ', $techs) . ')' : '';
                $siteLines[] = "• {$site->site_code}{$techStr}";
            }
            $sitesText = implode("\n", $siteLines);
        }

        // Format FBB islands
        $fbbText = null;
        if ($incident->fbbIslands && $incident->fbbIslands->isNotEmpty()) {
            $fbbLines = [];
            foreach ($incident->fbbIslands as $island) {
                $fbbLines[] = "• {$island->full_name}";
            }
            $fbbText = implode("\n", $fbbLines);
        }

        // Calculate duration
        $start = \Carbon\Carbon::parse($incident->started_at);

        if ($incident->resolved_at) {
            // Incident is closed - show actual duration
            $end = \Carbon\Carbon::parse($incident->resolved_at);
            $diff = $start->diff($end);

            $parts = [];
            if ($diff->d > 0) $parts[] = $diff->d . 'd';
            if ($diff->h > 0) $parts[] = $diff->h . 'h';
            if ($diff->i > 0) $parts[] = $diff->i . 'm';

            $duration = !empty($parts) ? implode(' ', $parts) : 'Less than 1 minute';
        } else {
            // Incident is ongoing - show how long it's been ongoing
            $now = \Carbon\Carbon::now('Indian/Maldives');
            $diff = $start->diff($now);

            $parts = [];
            if ($diff->d > 0) $parts[] = $diff->d . 'd';
            if ($diff->h > 0) $parts[] = $diff->h . 'h';
            if ($diff->i > 0) $parts[] = $diff->i . 'm';

            $ongoingTime = !empty($parts) ? implode(' ', $parts) : 'Less than 1 minute';
            $duration = "Ongoing ({$ongoingTime})";
        }

        // Format started/resolved times in Maldives time
        $startedAt = \Carbon\Carbon::parse($incident->started_at)->setTimezone('Indian/Maldives')->format('d M Y, H:i');
        $resolvedAt = $incident->resolved_at
            ? \Carbon\Carbon::parse($incident->resolved_at)->setTimezone('Indian/Maldives')->format('d M Y, H:i')
            : 'Ongoing';

        // Helper function to clean and escape user content for WhatsApp
        $cleanForWhatsApp = function($str) {
            if (!$str) return '';
            // Strip all HTML and PHP tags first
            $str = strip_tags($str);
            // Decode HTML entities
            $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
            // Escape WhatsApp markdown characters that users might have entered
            $str = str_replace(['*', '_', '~', '`'], ['\*', '\_', '\~', '\`'], $str);
            // Clean up extra whitespace
            $str = preg_replace('/\s+/', ' ', $str);
            $str = trim($str);
            return $str;
        };

        // Build the formatted text (Professional)
        $text = "*INCIDENT {$incident->incident_code}*\n\n";

        $text .= "*Started:* {$startedAt}\n";
        if ($incident->resolved_at) {
            $text .= "*Resolved:* {$resolvedAt}\n";
        }
        $text .= "*Duration:* {$duration}\n";
        $text .= "*Status:* {$incident->status}\n\n";

        // Only show services section if there are services
        if ($servicesText) {
            $text .= "*AFFECTED SERVICES:*\n{$servicesText}\n\n";
        }

        if ($incident->sites && $incident->sites->isNotEmpty()) {
            $text .= "*AFFECTED CELLS:*\n{$sitesText}\n\n";
        }

        if ($incident->fbbIslands && $incident->fbbIslands->isNotEmpty()) {
            $text .= "*AFFECTED FBB ISLANDS:*\n{$fbbText}\n\n";
        }

        if ($incident->category && is_object($incident->category)) {
            $text .= "*CATEGORY:* {$incident->category->name}\n";
        } elseif ($incident->category && is_string($incident->category)) {
            $text .= "*CATEGORY:* {$incident->category}\n";
        }
        if ($incident->outageCategory && is_object($incident->outageCategory)) {
            $text .= "*OUTAGE CATEGORY:* {$incident->outageCategory->name}\n";
        } elseif ($incident->outage_category && is_string($incident->outage_category)) {
            $text .= "*OUTAGE CATEGORY:* {$incident->outage_category}\n";
        }
        if ($incident->faultType && is_object($incident->faultType)) {
            $text .= "*FAULT TYPE:* {$incident->faultType->name}\n";
        } elseif ($incident->fault_type && is_string($incident->fault_type)) {
            $text .= "*FAULT TYPE:* {$incident->fault_type}\n";
        }
        if ($incident->root_cause) {
            $text .= "*ROOT CAUSE:* " . $cleanForWhatsApp($incident->root_cause) . "\n";
        }

        // Add spacing before summary/resolution if we had category info
        if ($incident->category || $incident->outageCategory || $incident->faultType || $incident->root_cause) {
            $text .= "\n";
        }

        if ($incident->summary) {
            $summaryText = $cleanForWhatsApp($incident->summary);

            // Intelligently detect and format cell names
            // Cell names typically contain underscores, hyphens, and alphanumeric characters
            // They're usually separated by newlines, commas, or semicolons
            $formattedSummary = $summaryText;

            // Check if summary contains cell name patterns (e.g., Name_Part_L900-C)
            // Pattern: Contains underscores/hyphens and appears to be technical cell identifiers
            $lines = preg_split('/\r\n|\r|\n/', $summaryText);
            $cellPattern = '/^[A-Za-z0-9_\-]+_[A-Za-z0-9_\-]+/'; // Matches cell name patterns

            $hasCellNames = false;
            $cellLines = [];
            $otherLines = [];

            foreach ($lines as $line) {
                $trimmedLine = trim($line);
                if (empty($trimmedLine)) continue;

                // Check if line looks like a cell name or comma-separated cell names
                if (preg_match($cellPattern, $trimmedLine)) {
                    // Split by comma if multiple cells on one line
                    $cellsOnLine = array_map('trim', preg_split('/[,;]/', $trimmedLine));
                    foreach ($cellsOnLine as $cell) {
                        if (!empty($cell) && preg_match($cellPattern, $cell)) {
                            $cellLines[] = $cell;
                            $hasCellNames = true;
                        }
                    }
                } else {
                    $otherLines[] = $trimmedLine;
                }
            }

            // If we detected cell names, format them as numbered list
            if ($hasCellNames && count($cellLines) > 0) {
                $formattedSummary = '';

                // Add other text first if any
                if (count($otherLines) > 0) {
                    $formattedSummary .= implode("\n", $otherLines) . "\n\n";
                }

                // Add formatted cell list
                $formattedSummary .= "*Affected Cells:*\n";
                $counter = 1;
                foreach ($cellLines as $cellName) {
                    $formattedSummary .= "{$counter}. {$cellName}\n";
                    $counter++;
                }
            }

            // Add visual separator and better formatting
            $text .= "━━━━━━━━━━━━━━━━━━\n";
            $text .= "*SUMMARY:*\n\n";
            $text .= $formattedSummary . "\n";
            $text .= "━━━━━━━━━━━━━━━━━━\n\n";
        }

        if ($incident->resolution_notes) {
            $resolutionText = $cleanForWhatsApp($incident->resolution_notes);
            $text .= "*RESOLUTION:*\n\n";
            $text .= $resolutionText . "\n\n";
        }

        // Add delay reason if applicable (incidents that took > 5 hours)
        if ($incident->delay_reason) {
            $delayReasonText = $cleanForWhatsApp($incident->delay_reason);
            $text .= "⚠️ *DELAY REASON:*\n";
            $text .= $delayReasonText . "\n\n";
        }

        // Check for recurring incidents in the past 2 months
        if ($incident->summary) {
            $twoMonthsAgo = \Carbon\Carbon::now()->subMonths(2);

            // Find similar incidents with the same summary in the past 2 months
            $recurringIncidents = Incident::where('summary', $incident->summary)
                ->where('id', '!=', $incident->id) // Exclude current incident
                ->where('started_at', '>=', $twoMonthsAgo)
                ->orderBy('started_at', 'desc')
                ->get();

            if ($recurringIncidents->count() > 0) {
                $text .= "🔁 *RECURRING INCIDENT ALERT:*\n";
                $text .= "This incident has occurred *{$recurringIncidents->count()} time(s)* in the past 2 months:\n\n";

                $counter = 1;
                foreach ($recurringIncidents as $recurring) {
                    $recurringDate = \Carbon\Carbon::parse($recurring->started_at)
                        ->setTimezone('Indian/Maldives')
                        ->format('d M Y, H:i');
                    $recurringDuration = $recurring->duration_hms ?? 'N/A';

                    $text .= "{$counter}. {$recurring->incident_code} - {$recurringDate}";
                    if ($recurring->duration_hms) {
                        $text .= " (Duration: {$recurringDuration})";
                    }
                    $text .= "\n";
                    $counter++;
                }
                $text .= "\n";
            }
        }

        return response($text, 200)
            ->header('Content-Type', 'text/plain');
    }

}