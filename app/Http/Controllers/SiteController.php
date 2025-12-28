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

        // Site Type Filter
        if ($request->filled('site_type')) {
            if ($request->site_type === 'hub') {
                $query->where('site_type', 'Hub Site');
            } elseif ($request->site_type === 'end') {
                $query->where('site_type', 'End Site');
            }
        }

        // Link Site Filter
        if ($request->has('is_link_site')) {
            $query->where('is_link_site', true);
        }

        // Temp Site Filter
        if ($request->has('is_temp_site')) {
            $query->where('is_temp_site', true);
        }

        // Technologies Filter
        if ($request->filled('technologies')) {
            $query->whereHas('technologies', function($q) use ($request) {
                $q->whereIn('technology', $request->technologies)
                  ->where('is_active', true);
            });
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
        // Load regions for dropdown
        $regions = \App\Models\Region::orderBy('name')->get();

        // Load active hub sites for the hub connection dropdown
        $hubSites = Site::active()
            ->where('site_type', 'Hub Site')
            ->orderBy('site_code')
            ->get();

        return view('sites.create', compact('regions', 'hubSites'));
    }

    /**
     * Store a newly created site in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'site_name' => ['required', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'site_type' => ['required', 'in:End Site,Hub Site'],
            'transmission_backhaul' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'hub_sites' => ['nullable', 'array'],
            'hub_sites.*' => [
                'exists:sites,id',
                function ($attribute, $value, $fail) {
                    $hubSite = Site::find($value);
                    if (!$hubSite || $hubSite->site_type !== 'Hub Site') {
                        $fail('Selected hub site must be a Hub Site.');
                    }
                }
            ],
        ]);

        // Auto-generate site_code and site_number
        $generatedData = Site::generateSiteCode($validated['region_id']);

        // Merge generated data with validated data
        $validated = array_merge($validated, $generatedData);

        // Set display_name to site_code if not provided
        if (empty($validated['display_name'])) {
            $validated['display_name'] = $generatedData['site_code'];
        }

        // Handle checkboxes (they're not submitted if unchecked)
        $validated['is_active'] = $request->has('is_active');
        $validated['is_link_site'] = $request->has('is_link_site');

        // Temp site: if not active, it's a temp site
        $validated['is_temp_site'] = !$validated['is_active'];

        // Check if site_code already exists (race condition protection)
        if (Site::where('site_code', $validated['site_code'])->exists()) {
            return back()->withErrors([
                'site_code' => 'A site with this code already exists. Please try again.'
            ])->withInput();
        }

        // Create the site
        $site = Site::create($validated);

        // Create default technologies for the site
        $cellularTechs = ['2G', '3G', '4G', '5G'];
        $otherServices = ['ILL', 'SIP', 'IPTV', 'NCIT'];

        foreach ($cellularTechs as $technology) {
            \App\Models\SiteTechnology::create([
                'site_id' => $site->id,
                'technology' => $technology,
                'is_active' => true,
            ]);
        }

        foreach ($otherServices as $technology) {
            \App\Models\SiteTechnology::create([
                'site_id' => $site->id,
                'technology' => $technology,
                'is_active' => false,
            ]);
        }

        // Attach hub sites if provided
        if ($request->filled('hub_sites')) {
            $site->hubSites()->attach($request->hub_sites);
        }

        return redirect()->route('sites.show', $site)
            ->with('success', 'Site created successfully with code: ' . $site->site_code);
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
        $site->load(['region', 'location', 'technologies', 'hubSites', 'connectedSites']);
        $regions = \App\Models\Region::with('locations')->orderBy('name')->get();

        // Get all active hub sites except the current site (to prevent self-reference)
        $hubSites = Site::active()
            ->where('site_type', 'Hub Site')
            ->where('id', '!=', $site->id)
            ->orderBy('site_code')
            ->get();

        return view('sites.edit', compact('site', 'regions', 'hubSites'));
    }

    /**
     * Update the specified site in storage.
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'site_type' => ['required', 'in:End Site,Hub Site'],
            'transmission_backhaul' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'hub_sites' => ['nullable', 'array'],
            'hub_sites.*' => [
                'exists:sites,id',
                function ($attribute, $value, $fail) use ($site) {
                    // Prevent self-reference
                    if ($value == $site->id) {
                        $fail('A site cannot be connected to itself.');
                        return;
                    }

                    // Ensure selected site is a Hub Site
                    $hubSite = Site::find($value);
                    if (!$hubSite || $hubSite->site_type !== 'Hub Site') {
                        $fail('Selected hub site must be a Hub Site.');
                    }
                }
            ],
        ]);

        // Handle checkboxes (they're not submitted if unchecked)
        $validated['is_active'] = $request->has('is_active');
        $validated['is_link_site'] = $request->has('is_link_site');

        // Temp site: if not active, it's a temp site
        $validated['is_temp_site'] = !$validated['is_active'];

        // If changing from Hub Site to End Site, detach all connected sites
        if ($site->site_type === 'Hub Site' && $validated['site_type'] === 'End Site') {
            $site->connectedSites()->detach();
        }

        $site->update($validated);

        // Update hub site connections (sync will add new, remove old, keep existing)
        if ($request->filled('hub_sites')) {
            $site->hubSites()->sync($request->hub_sites);
        } else {
            $site->hubSites()->detach();
        }

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
