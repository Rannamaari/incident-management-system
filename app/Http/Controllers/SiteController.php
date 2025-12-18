<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    /**
     * Display a listing of sites.
     */
    public function index(Request $request)
    {
        $query = Site::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filters
        if ($request->filled('atoll')) {
            $query->filterAtoll($request->atoll);
        }

        if ($request->filled('coverage')) {
            $query->filterCoverage($request->coverage);
        }

        if ($request->filled('status')) {
            $query->filterStatus($request->status);
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->filterDateRange($request->start_date, $request->end_date);
        }

        // Sorting
        $sortBy = $request->get('sort', 'site_id');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $sites = $query->paginate(20)->withQueryString();

        // Get unique values for filter dropdowns
        $atolls = Site::select('atoll_code')->distinct()->orderBy('atoll_code')->pluck('atoll_code');
        $coverages = Site::select('coverage')->distinct()->orderBy('coverage')->pluck('coverage');
        $statuses = ['Active', 'Monitoring', 'Maintenance', 'Inactive'];

        return view('sites.index', compact('sites', 'atolls', 'coverages', 'statuses'));
    }

    /**
     * Show the form for creating a new site.
     */
    public function create()
    {
        return view('sites.create');
    }

    /**
     * Store a newly created site in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => ['required', 'string', 'max:255', 'unique:sites,site_id'],
            'atoll_code' => ['required', 'string', 'max:255'],
            'site_name' => ['required', 'string', 'max:255'],
            'coverage' => ['required', 'string', 'max:255'],
            'operational_date' => ['required', 'date'],
            'transmission_or_backhaul' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'status' => ['required', 'in:Active,Monitoring,Maintenance,Inactive'],
            'review_date' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Site::create($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site created successfully.');
    }

    /**
     * Display the specified site.
     */
    public function show(Site $site)
    {
        $site->load(['creator', 'updater']);

        return view('sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified site.
     */
    public function edit(Site $site)
    {
        return view('sites.edit', compact('site'));
    }

    /**
     * Update the specified site in storage.
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'site_id' => ['required', 'string', 'max:255', 'unique:sites,site_id,' . $site->id],
            'atoll_code' => ['required', 'string', 'max:255'],
            'site_name' => ['required', 'string', 'max:255'],
            'coverage' => ['required', 'string', 'max:255'],
            'operational_date' => ['required', 'date'],
            'transmission_or_backhaul' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'status' => ['required', 'in:Active,Monitoring,Maintenance,Inactive'],
            'review_date' => ['nullable', 'date'],
        ]);

        $validated['updated_by'] = Auth::id();

        $site->update($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site updated successfully.');
    }

    /**
     * Remove the specified site from storage.
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site deleted successfully.');
    }

    /**
     * Remove multiple sites from storage.
     */
    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'site_ids' => ['required', 'array'],
            'site_ids.*' => ['required', 'integer', 'exists:sites,id'],
        ]);

        $count = Site::whereIn('id', $validated['site_ids'])->delete();

        return redirect()->route('sites.index')
            ->with('success', "Successfully deleted {$count} site(s).");
    }
}
