<?php

namespace App\Http\Controllers;

use App\Models\FbbIsland;
use App\Models\Region;
use Illuminate\Http\Request;

class FbbIslandController extends Controller
{
    /**
     * Display a listing of FBB islands.
     */
    public function index(Request $request)
    {
        $query = FbbIsland::with('region');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by region
        if ($request->filled('region')) {
            $query->where('region_id', $request->region);
        }

        // Filter by technology
        if ($request->filled('technology')) {
            $query->where('technology', 'LIKE', "%{$request->technology}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sorting
        $sortBy = $request->get('sort', 'island_name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $fbbIslands = $query->paginate(20)->withQueryString();

        // Get filter options
        $regions = Region::orderBy('name')->get();
        $technologies = FbbIsland::select('technology')
            ->distinct()
            ->orderBy('technology')
            ->pluck('technology');

        return view('fbb-islands.index', compact('fbbIslands', 'regions', 'technologies'));
    }

    /**
     * Show the form for creating a new FBB island.
     */
    public function create()
    {
        $regions = Region::orderBy('name')->get();
        return view('fbb-islands.create', compact('regions'));
    }

    /**
     * Store a newly created FBB island in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'island_name' => ['required', 'string', 'max:255'],
            'technology' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        FbbIsland::create($validated);

        return redirect()->route('fbb-islands.index')
            ->with('success', 'FBB Island added successfully.');
    }

    /**
     * Display the specified FBB island.
     */
    public function show(FbbIsland $fbbIsland)
    {
        $fbbIsland->load(['region', 'incidents']);
        return view('fbb-islands.show', compact('fbbIsland'));
    }

    /**
     * Show the form for editing the specified FBB island.
     */
    public function edit(FbbIsland $fbbIsland)
    {
        $regions = Region::orderBy('name')->get();
        return view('fbb-islands.edit', compact('fbbIsland', 'regions'));
    }

    /**
     * Update the specified FBB island in storage.
     */
    public function update(Request $request, FbbIsland $fbbIsland)
    {
        $validated = $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'island_name' => ['required', 'string', 'max:255'],
            'technology' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $fbbIsland->update($validated);

        return redirect()->route('fbb-islands.show', $fbbIsland)
            ->with('success', 'FBB Island updated successfully.');
    }

    /**
     * Remove the specified FBB island from storage.
     */
    public function destroy(FbbIsland $fbbIsland)
    {
        $fbbIsland->delete();

        return redirect()->route('fbb-islands.index')
            ->with('success', 'FBB Island deleted successfully.');
    }

    /**
     * Remove multiple FBB islands from storage.
     */
    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'fbb_island_ids' => ['required', 'array'],
            'fbb_island_ids.*' => ['required', 'integer', 'exists:fbb_islands,id'],
        ]);

        $count = FbbIsland::whereIn('id', $validated['fbb_island_ids'])->delete();

        return redirect()->route('fbb-islands.index')
            ->with('success', "Successfully deleted {$count} FBB island(s).");
    }
}
