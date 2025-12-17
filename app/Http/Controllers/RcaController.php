<?php

namespace App\Http\Controllers;

use App\Models\Rca;
use App\Models\Incident;
use App\Models\RcaTimeLog;
use App\Models\RcaActionPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RcaController extends Controller
{
    /**
     * Display a listing of RCAs and incidents requiring RCA.
     */
    public function index(Request $request)
    {
        // Get all RCAs with pagination
        $rcas = Rca::with(['incident', 'creator'])
            ->when($request->search, function($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('rca_number', 'like', "%{$search}%")
                    ->orWhereHas('incident', function($q) use ($search) {
                        $q->where('incident_code', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Get incidents requiring RCA (High/Critical severity without RCA)
        $incidentsRequiringRca = Incident::whereIn('severity', ['High', 'Critical'])
            ->whereDoesntHave('rca')
            ->orderByDesc('started_at')
            ->limit(50)
            ->get();

        return view('rcas.index', compact('rcas', 'incidentsRequiringRca'));
    }

    /**
     * Show the form for creating a new RCA.
     */
    public function create(Request $request)
    {
        // Get incident if passed in query string
        $selectedIncident = null;
        if ($request->has('incident_id')) {
            $selectedIncident = Incident::findOrFail($request->incident_id);
        }

        // Get all incidents that can have RCA (all incidents)
        $incidents = Incident::orderByDesc('started_at')
            ->limit(100)
            ->get();

        return view('rcas.create', compact('incidents', 'selectedIncident'));
    }

    /**
     * Store a newly created RCA in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'title' => 'required|string|max:255',
            'problem_description' => 'nullable|string',
            'problem_analysis' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'workaround' => 'nullable|string',
            'solution' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'status' => 'required|in:Draft,In Review,Approved,Closed',

            // Time logs
            'time_logs.*.occurred_at' => 'nullable|date',
            'time_logs.*.event_description' => 'nullable|string',

            // Action points
            'action_points.*.action_item' => 'nullable|string',
            'action_points.*.responsible_person' => 'nullable|string',
            'action_points.*.due_date' => 'nullable|date',
            'action_points.*.status' => 'nullable|in:Pending,In Progress,Completed,Cancelled',
        ]);

        DB::beginTransaction();
        try {
            // Create RCA
            $rca = Rca::create([
                'incident_id' => $validated['incident_id'],
                'title' => $validated['title'],
                'problem_description' => $validated['problem_description'] ?? null,
                'problem_analysis' => $validated['problem_analysis'] ?? null,
                'root_cause' => $validated['root_cause'] ?? null,
                'workaround' => $validated['workaround'] ?? null,
                'solution' => $validated['solution'] ?? null,
                'recommendation' => $validated['recommendation'] ?? null,
                'status' => $validated['status'],
                'created_by' => auth()->id(),
            ]);

            // Handle time logs
            if (isset($validated['time_logs'])) {
                foreach ($validated['time_logs'] as $log) {
                    if (!empty($log['occurred_at']) && !empty($log['event_description'])) {
                        RcaTimeLog::create([
                            'rca_id' => $rca->id,
                            'occurred_at' => $log['occurred_at'],
                            'event_description' => $log['event_description'],
                        ]);
                    }
                }
            }

            // Handle action points
            if (isset($validated['action_points'])) {
                foreach ($validated['action_points'] as $point) {
                    if (!empty($point['action_item'])) {
                        RcaActionPoint::create([
                            'rca_id' => $rca->id,
                            'action_item' => $point['action_item'],
                            'responsible_person' => $point['responsible_person'] ?? '',
                            'due_date' => $point['due_date'] ?? null,
                            'status' => $point['status'] ?? 'Pending',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('rcas.show', $rca)
                ->with('success', 'RCA created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create RCA: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified RCA.
     */
    public function show(Rca $rca)
    {
        $rca->load(['incident', 'timeLogs', 'actionPoints', 'creator', 'reviewer']);

        return view('rcas.show', compact('rca'));
    }

    /**
     * Show the form for editing the specified RCA.
     */
    public function edit(Rca $rca)
    {
        $rca->load(['timeLogs', 'actionPoints']);

        // Get all incidents for selection
        $incidents = Incident::orderByDesc('started_at')
            ->limit(100)
            ->get();

        return view('rcas.edit', compact('rca', 'incidents'));
    }

    /**
     * Update the specified RCA in storage.
     */
    public function update(Request $request, Rca $rca)
    {
        $validated = $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'title' => 'required|string|max:255',
            'problem_description' => 'nullable|string',
            'problem_analysis' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'workaround' => 'nullable|string',
            'solution' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'status' => 'required|in:Draft,In Review,Approved,Closed',

            // Time logs
            'time_logs.*.occurred_at' => 'nullable|date',
            'time_logs.*.event_description' => 'nullable|string',

            // Action points
            'action_points.*.action_item' => 'nullable|string',
            'action_points.*.responsible_person' => 'nullable|string',
            'action_points.*.due_date' => 'nullable|date',
            'action_points.*.status' => 'nullable|in:Pending,In Progress,Completed,Cancelled',
        ]);

        DB::beginTransaction();
        try {
            // Update RCA
            $rca->update([
                'incident_id' => $validated['incident_id'],
                'title' => $validated['title'],
                'problem_description' => $validated['problem_description'] ?? null,
                'problem_analysis' => $validated['problem_analysis'] ?? null,
                'root_cause' => $validated['root_cause'] ?? null,
                'workaround' => $validated['workaround'] ?? null,
                'solution' => $validated['solution'] ?? null,
                'recommendation' => $validated['recommendation'] ?? null,
                'status' => $validated['status'],
            ]);

            // Delete existing time logs and action points, then recreate
            $rca->timeLogs()->delete();
            $rca->actionPoints()->delete();

            // Handle time logs
            if (isset($validated['time_logs'])) {
                foreach ($validated['time_logs'] as $log) {
                    if (!empty($log['occurred_at']) && !empty($log['event_description'])) {
                        RcaTimeLog::create([
                            'rca_id' => $rca->id,
                            'occurred_at' => $log['occurred_at'],
                            'event_description' => $log['event_description'],
                        ]);
                    }
                }
            }

            // Handle action points
            if (isset($validated['action_points'])) {
                foreach ($validated['action_points'] as $point) {
                    if (!empty($point['action_item'])) {
                        RcaActionPoint::create([
                            'rca_id' => $rca->id,
                            'action_item' => $point['action_item'],
                            'responsible_person' => $point['responsible_person'] ?? '',
                            'due_date' => $point['due_date'] ?? null,
                            'status' => $point['status'] ?? 'Pending',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('rcas.show', $rca)
                ->with('success', 'RCA updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update RCA: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified RCA from storage.
     */
    public function destroy(Rca $rca)
    {
        try {
            $rca->delete();

            return redirect()->route('rcas.index')
                ->with('success', 'RCA deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete RCA: ' . $e->getMessage()]);
        }
    }
}
