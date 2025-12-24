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
        $query = Site::with(['region', 'location', 'technologies']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filters
        if ($request->filled('region')) {
            $query->where('region_id', $request->region);
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'site_code');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $sites = $query->paginate(20)->withQueryString();

        // Get unique values for filter dropdowns
        $regions = \App\Models\Region::orderBy('name')->get();
        $atolls = $regions->pluck('code'); // For backwards compatibility with the view
        $coverages = collect(['2G', '3G', '4G', '5G']); // Available technologies
        $statuses = ['Active', 'Inactive'];

        return view('sites.index', compact('sites', 'regions', 'atolls', 'coverages', 'statuses'));
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
        $site->load(['region', 'location', 'technologies']);

        return view('sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified site.
     */
    public function edit(Site $site)
    {
        $site->load(['region', 'location', 'technologies']);
        $regions = \App\Models\Region::with('locations')->orderBy('name')->get();

        return view('sites.edit', compact('site', 'regions'));
    }

    /**
     * Update the specified site in storage.
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'has_fbb' => ['nullable', 'boolean'],
        ]);

        // Handle checkboxes (they're not submitted if unchecked)
        $validated['is_active'] = $request->has('is_active');
        $validated['has_fbb'] = $request->has('has_fbb');

        $site->update($validated);

        // Update technologies - handle both checked and unchecked
        $submittedTechIds = array_keys($request->input('technologies', []));

        // Get all technology IDs for this site
        $allSiteTechIds = $site->technologies()->pluck('id')->toArray();

        // Update each technology: active if checked, inactive if unchecked
        foreach ($allSiteTechIds as $techId) {
            \App\Models\SiteTechnology::where('id', $techId)->update([
                'is_active' => in_array($techId, $submittedTechIds)
            ]);
        }

        return redirect()->route('sites.show', $site)
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
