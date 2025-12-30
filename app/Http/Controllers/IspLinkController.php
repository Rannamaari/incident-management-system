<?php

namespace App\Http\Controllers;

use App\Models\IspLink;
use App\Models\IspEscalationContact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IspLinkController extends Controller
{
    /**
     * Display the ISP Dashboard with statistics
     */
    public function dashboard()
    {
        // Get all ISP links for calculations
        $allLinks = IspLink::all();

        // Separate links by type
        $backhaulLinks = $allLinks->where('link_type', 'Backhaul');
        $peeringLinks = $allLinks->where('link_type', 'Peering');

        // Get active ISP outages (incidents with ISP links that are still open)
        // Include both old single ISP link and new multi-select ISP links
        $activeOutages = \App\Models\Incident::with(['ispLink', 'ispLinks', 'category'])
            ->where(function($query) {
                $query->whereNotNull('isp_link_id') // Old single ISP link system
                      ->orWhereHas('ispLinks'); // New multi-select ISP links system
            })
            ->whereIn('status', ['Open', 'In Progress', 'Monitoring'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Build set of link IDs that have active incidents
        $linksWithActiveIncidents = collect();
        foreach ($activeOutages as $incident) {
            if ($incident->isp_link_id && $incident->ispLink) {
                $linksWithActiveIncidents->push($incident->isp_link_id);
            }
            if ($incident->ispLinks && $incident->ispLinks->count() > 0) {
                foreach ($incident->ispLinks as $link) {
                    $linksWithActiveIncidents->push($link->id);
                }
            }
        }
        $linksWithActiveIncidents = $linksWithActiveIncidents->unique();

        // Count links by status (accounting for active incidents)
        // Links with active incidents should be counted as "down" regardless of their database status
        $statusCounts = [
            'up' => $allLinks->whereNotIn('id', $linksWithActiveIncidents)->where('status', 'Up')->count(),
            'down' => $allLinks->where('status', 'Down')->count() + $linksWithActiveIncidents->count(),
            'degraded' => $allLinks->whereNotIn('id', $linksWithActiveIncidents)->where('status', 'Degraded')->count(),
        ];

        // Get list of ISP links with active incidents and calculate capacity impact
        $linksWithIncidents = [];
        $backhaulCapacityLostFromIncidents = 0;
        $peeringCapacityLostFromIncidents = 0;

        foreach ($activeOutages as $incident) {
            // Handle old single ISP link
            if ($incident->isp_link_id && $incident->ispLink) {
                $linkId = $incident->isp_link_id;
                $capacityLost = $incident->isp_capacity_lost_gbps ?? 0;

                if (!isset($linksWithIncidents[$linkId])) {
                    $linksWithIncidents[$linkId] = [
                        'link' => $incident->ispLink,
                        'incidents' => [],
                        'total_capacity_lost' => 0
                    ];
                }
                $linksWithIncidents[$linkId]['incidents'][] = $incident;
                $linksWithIncidents[$linkId]['total_capacity_lost'] += $capacityLost;

                // Add to type-specific capacity lost
                if ($incident->ispLink->link_type === 'Backhaul') {
                    $backhaulCapacityLostFromIncidents += $capacityLost;
                } else {
                    $peeringCapacityLostFromIncidents += $capacityLost;
                }
            }

            // Handle new multi-select ISP links
            if ($incident->ispLinks && $incident->ispLinks->count() > 0) {
                foreach ($incident->ispLinks as $link) {
                    $linkId = $link->id;
                    $capacityLost = $link->pivot->capacity_lost_gbps ?? 0;

                    if (!isset($linksWithIncidents[$linkId])) {
                        $linksWithIncidents[$linkId] = [
                            'link' => $link,
                            'incidents' => [],
                            'total_capacity_lost' => 0
                        ];
                    }
                    $linksWithIncidents[$linkId]['incidents'][] = $incident;
                    $linksWithIncidents[$linkId]['total_capacity_lost'] += $capacityLost;

                    // Add to type-specific capacity lost
                    if ($link->link_type === 'Backhaul') {
                        $backhaulCapacityLostFromIncidents += $capacityLost;
                    } else {
                        $peeringCapacityLostFromIncidents += $capacityLost;
                    }
                }
            }
        }

        // Calculate Backhaul capacity statistics (accounting for active incidents)
        $backhaulTotalCapacity = $backhaulLinks->sum('total_capacity_gbps');
        $backhaulCurrentCapacity = max(0, $backhaulTotalCapacity - $backhaulCapacityLostFromIncidents);
        $backhaulLostCapacity = $backhaulCapacityLostFromIncidents;
        $backhaulAvailability = $backhaulTotalCapacity > 0 ? round(($backhaulCurrentCapacity / $backhaulTotalCapacity) * 100, 2) : 0;
        $backhaulCount = $backhaulLinks->count();

        // Calculate Peering capacity statistics (accounting for active incidents)
        $peeringTotalCapacity = $peeringLinks->sum('total_capacity_gbps');
        $peeringCurrentCapacity = max(0, $peeringTotalCapacity - $peeringCapacityLostFromIncidents);
        $peeringLostCapacity = $peeringCapacityLostFromIncidents;
        $peeringAvailability = $peeringTotalCapacity > 0 ? round(($peeringCurrentCapacity / $peeringTotalCapacity) * 100, 2) : 0;
        $peeringCount = $peeringLinks->count();

        // Calculate total capacity statistics (combined, accounting for active incidents)
        $totalCapacity = $allLinks->sum('total_capacity_gbps');
        $totalCapacityLostFromIncidents = $backhaulCapacityLostFromIncidents + $peeringCapacityLostFromIncidents;
        $currentCapacity = max(0, $totalCapacity - $totalCapacityLostFromIncidents);
        $lostCapacity = $totalCapacityLostFromIncidents;
        $overallAvailability = $totalCapacity > 0 ? round(($currentCapacity / $totalCapacity) * 100, 2) : 0;

        // Get recent updates (last 10 modified links)
        $recentLinks = IspLink::with(['creator', 'updater'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('isp.dashboard', compact(
            'totalCapacity',
            'currentCapacity',
            'lostCapacity',
            'overallAvailability',
            'backhaulTotalCapacity',
            'backhaulCurrentCapacity',
            'backhaulLostCapacity',
            'backhaulAvailability',
            'backhaulCount',
            'peeringTotalCapacity',
            'peeringCurrentCapacity',
            'peeringLostCapacity',
            'peeringAvailability',
            'peeringCount',
            'statusCounts',
            'activeOutages',
            'linksWithIncidents',
            'recentLinks'
        ));
    }

    /**
     * Display a listing of ISP links
     */
    public function index(Request $request)
    {
        $query = IspLink::query()
            ->with(['escalationContacts', 'creator', 'updater'])
            ->withCount(['activeIncidents']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Filter by link type
        if ($request->filled('link_type')) {
            $query->linkType($request->link_type);
        }

        // Order by most recently updated
        $query->orderBy('updated_at', 'desc');

        // Paginate results
        $ispLinks = $query->paginate(25)->withQueryString();

        return view('isp.index', compact('ispLinks'));
    }

    /**
     * Show the form for creating a new ISP link
     */
    public function create()
    {
        return view('isp.create');
    }

    /**
     * Store a newly created ISP link
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isp_name' => 'required|string|max:100',
            'circuit_id' => 'required|string|max:100|unique:isp_links,circuit_id',
            'link_type' => ['required', Rule::in(IspLink::LINK_TYPES)],
            'total_capacity_gbps' => 'required|numeric|min:0',
            'current_capacity_gbps' => 'required|numeric|min:0|lte:total_capacity_gbps',
            'status' => ['required', Rule::in(IspLink::STATUSES)],
            'location_a' => 'required|string|max:255',
            'location_b' => 'required|string|max:255',
            'prtg_sensor_id' => 'nullable|string|max:100',
            'prtg_api_endpoint' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'escalation_contacts' => 'required|array|min:1',
            'escalation_contacts.*.escalation_level' => ['required', Rule::in(IspEscalationContact::ESCALATION_LEVELS)],
            'escalation_contacts.*.contact_name' => 'required|string|max:100',
            'escalation_contacts.*.contact_phone' => 'required|string|max:50',
            'escalation_contacts.*.contact_email' => 'nullable|email|max:100',
            'escalation_contacts.*.is_primary' => 'nullable|boolean',
        ]);

        // Create ISP Link
        $ispLink = new IspLink();
        $ispLink->fill($validated);
        $ispLink->created_by = auth()->id();
        $ispLink->save();

        // Create escalation contacts
        if ($request->has('escalation_contacts')) {
            foreach ($request->escalation_contacts as $contactData) {
                $ispLink->escalationContacts()->create([
                    'escalation_level' => $contactData['escalation_level'],
                    'contact_name' => $contactData['contact_name'],
                    'contact_phone' => $contactData['contact_phone'],
                    'contact_email' => $contactData['contact_email'] ?? null,
                    'is_primary' => $contactData['is_primary'] ?? false,
                ]);
            }
        }

        return redirect()->route('isp.index')
            ->with('success', 'ISP Link created successfully.');
    }

    /**
     * Display the specified ISP link
     */
    public function show(IspLink $ispLink)
    {
        $ispLink->load(['escalationContacts', 'creator', 'updater']);

        // Load incidents for this ISP link from BOTH old and new systems (most recent first)
        // Old system: incidents where isp_link_id matches
        $oldSystemIncidents = $ispLink->incidents()
            ->with('category')
            ->get();

        // New system: incidents linked via many-to-many pivot table
        $newSystemIncidents = \App\Models\Incident::whereHas('ispLinks', function($query) use ($ispLink) {
                $query->where('isp_links.id', $ispLink->id);
            })
            ->with('category')
            ->get();

        // Merge and remove duplicates (in case an incident exists in both systems)
        $incidents = $oldSystemIncidents->merge($newSystemIncidents)
            ->unique('id')
            ->sortByDesc('created_at')
            ->take(10);

        return view('isp.show', compact('ispLink', 'incidents'));
    }

    /**
     * Show the form for editing the specified ISP link
     */
    public function edit(IspLink $ispLink)
    {
        $ispLink->load('escalationContacts');

        return view('isp.edit', compact('ispLink'));
    }

    /**
     * Update the specified ISP link
     */
    public function update(Request $request, IspLink $ispLink)
    {
        $validated = $request->validate([
            'isp_name' => 'required|string|max:100',
            'circuit_id' => ['required', 'string', 'max:100', Rule::unique('isp_links')->ignore($ispLink->id)],
            'link_type' => ['required', Rule::in(IspLink::LINK_TYPES)],
            'total_capacity_gbps' => 'required|numeric|min:0',
            'current_capacity_gbps' => 'required|numeric|min:0|lte:total_capacity_gbps',
            'status' => ['required', Rule::in(IspLink::STATUSES)],
            'location_a' => 'required|string|max:255',
            'location_b' => 'required|string|max:255',
            'prtg_sensor_id' => 'nullable|string|max:100',
            'prtg_api_endpoint' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'escalation_contacts' => 'required|array|min:1',
            'escalation_contacts.*.escalation_level' => ['required', Rule::in(IspEscalationContact::ESCALATION_LEVELS)],
            'escalation_contacts.*.contact_name' => 'required|string|max:100',
            'escalation_contacts.*.contact_phone' => 'required|string|max:50',
            'escalation_contacts.*.contact_email' => 'nullable|email|max:100',
            'escalation_contacts.*.is_primary' => 'nullable|boolean',
        ]);

        // Update ISP Link
        $ispLink->fill($validated);
        $ispLink->updated_by = auth()->id();
        $ispLink->save();

        // Delete existing escalation contacts and recreate
        $ispLink->escalationContacts()->delete();

        if ($request->has('escalation_contacts')) {
            foreach ($request->escalation_contacts as $contactData) {
                $ispLink->escalationContacts()->create([
                    'escalation_level' => $contactData['escalation_level'],
                    'contact_name' => $contactData['contact_name'],
                    'contact_phone' => $contactData['contact_phone'],
                    'contact_email' => $contactData['contact_email'] ?? null,
                    'is_primary' => $contactData['is_primary'] ?? false,
                ]);
            }
        }

        return redirect()->route('isp.show', $ispLink)
            ->with('success', 'ISP Link updated successfully.');
    }

    /**
     * Remove the specified ISP link
     */
    public function destroy(IspLink $ispLink)
    {
        $ispLink->delete();

        return redirect()->route('isp.index')
            ->with('success', 'ISP Link deleted successfully.');
    }

    /**
     * Restore ISP link by closing all active incidents affecting it
     */
    public function restoreLink(IspLink $ispLink)
    {
        // Find all active incidents affecting this ISP link (both old and new systems)
        $activeIncidents = \App\Models\Incident::where(function($query) use ($ispLink) {
            // Old single ISP link system
            $query->where('isp_link_id', $ispLink->id)
                  // New many-to-many system
                  ->orWhereHas('ispLinks', function($q) use ($ispLink) {
                      $q->where('isp_links.id', $ispLink->id);
                  });
        })
        ->whereIn('status', ['Open', 'In Progress', 'Monitoring'])
        ->get();

        if ($activeIncidents->count() === 0) {
            return redirect()->back()
                ->with('info', 'No active incidents found for this ISP link.');
        }

        // Close all active incidents
        $closedCount = 0;
        foreach ($activeIncidents as $incident) {
            $incident->status = 'Closed';
            $incident->resolved_at = now();
            $incident->updated_by = auth()->id();

            // Calculate duration if not already set
            if (!$incident->duration_minutes && $incident->started_at) {
                $incident->duration_minutes = $incident->started_at->diffInMinutes($incident->resolved_at);
            }

            $incident->save();
            $closedCount++;
        }

        return redirect()->back()
            ->with('success', "ISP link restored successfully. Closed {$closedCount} active incident(s).");
    }
}
