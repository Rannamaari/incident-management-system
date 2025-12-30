<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\TemporarySite;
use App\Models\Site;
use App\Models\SiteTechnology;
use App\Models\IspLink;

class HomeController extends Controller
{
    /**
     * Show the public network dashboard.
     * No authentication required - this is a public status page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get total site counts from database (active sites only)
        $siteTotals = [
            '2g' => SiteTechnology::where('technology', '2G')
                ->where('is_active', true)
                ->whereHas('site', function($query) {
                    $query->where('is_active', true);
                })
                ->count(),
            '3g' => SiteTechnology::where('technology', '3G')
                ->where('is_active', true)
                ->whereHas('site', function($query) {
                    $query->where('is_active', true);
                })
                ->count(),
            '4g' => SiteTechnology::where('technology', '4G')
                ->where('is_active', true)
                ->whereHas('site', function($query) {
                    $query->where('is_active', true);
                })
                ->count(),
            '5g' => SiteTechnology::where('technology', '5G')
                ->where('is_active', true)
                ->whereHas('site', function($query) {
                    $query->where('is_active', true);
                })
                ->count(),
            'fbb' => \App\Models\FbbIsland::where('is_active', true)
                ->count(),
        ];

        // Get OPEN incidents only (Open, In Progress, Monitoring)
        $openIncidents = Incident::whereIn('status', ['Open', 'In Progress', 'Monitoring'])
            ->with(['sites.technologies', 'fbbIslands.region'])
            ->orderBy('started_at', 'desc')
            ->get();

        // Calculate total impacted counts
        $impactedCounts = [
            '2g' => $openIncidents->sum('sites_2g_impacted'),
            '3g' => $openIncidents->sum('sites_3g_impacted'),
            '4g' => $openIncidents->sum('sites_4g_impacted'),
            '5g' => $openIncidents->sum('sites_5g_impacted'),
            'fbb' => $openIncidents->sum('fbb_impacted'),
        ];

        // Calculate online/offline counts for status cards
        $siteStats = [];
        foreach (['2g', '3g', '4g', '5g', 'fbb'] as $type) {
            $total = $siteTotals[$type] ?? 0;
            $impacted = $impactedCounts[$type] ?? 0;

            $siteStats[$type] = [
                'total' => $total,
                'online' => max(0, $total - $impacted),
                'offline' => $impacted,
                'online_percentage' => $total > 0 ? round(($total - $impacted) / $total * 100, 1) : 100,
            ];
        }

        // Calculate Temporary Sites statistics (inactive sites)
        $tempSites = Site::where('is_active', false)->with('technologies')->get();

        $tempStats = [
            '2g' => ['total' => 0, 'online' => 0, 'offline' => 0],
            '3g' => ['total' => 0, 'online' => 0, 'offline' => 0],
            '4g' => ['total' => 0, 'online' => 0, 'offline' => 0],
            '5g' => ['total' => 0, 'online' => 0, 'offline' => 0],
        ];

        foreach ($tempSites as $site) {
            foreach ($site->technologies->where('is_active', true) as $tech) {
                $technology = strtolower($tech->technology);

                if (isset($tempStats[$technology])) {
                    $tempStats[$technology]['total']++;
                    // For temp sites, we assume all are online since they're temporary/inactive sites
                    $tempStats[$technology]['online']++;
                }
            }
        }

        // Calculate total and percentage for temp sites (include 5G)
        $tempTotal = $tempStats['2g']['total'] + $tempStats['3g']['total'] + $tempStats['4g']['total'] + $tempStats['5g']['total'];
        $tempOnline = $tempStats['2g']['online'] + $tempStats['3g']['online'] + $tempStats['4g']['online'] + $tempStats['5g']['online'];
        $tempOffline = $tempStats['2g']['offline'] + $tempStats['3g']['offline'] + $tempStats['4g']['offline'] + $tempStats['5g']['offline'];

        $siteStats['temp_sites'] = [
            'total' => $tempTotal,
            'online' => $tempOnline,
            'offline' => $tempOffline,
            'online_percentage' => $tempTotal > 0 ? round($tempOnline / $tempTotal * 100, 1) : 100,
            'breakdown' => $tempStats,
        ];

        // Separate incidents into site outages, cell outages, and FBB outages
        $siteOutages = $openIncidents->filter(function ($incident) {
            // Check if incident has any site impact (either through services or direct counts)
            $services = is_array($incident->affected_services)
                ? $incident->affected_services
                : explode(',', $incident->affected_services ?? '');

            $hasSiteService = in_array('Single Site', $services)
                || in_array('Multiple Site', $services);

            // Also include if any site technology counts are greater than 0 (but not if it's ONLY a cell outage)
            $hasSiteImpact = ($incident->sites_2g_impacted ?? 0) > 0
                || ($incident->sites_3g_impacted ?? 0) > 0
                || ($incident->sites_4g_impacted ?? 0) > 0
                || ($incident->sites_5g_impacted ?? 0) > 0;

            $isCellOnly = in_array('Cell', $services) && !$hasSiteService;

            return ($hasSiteService || $hasSiteImpact) && !$isCellOnly;
        });

        $cellOutages = $openIncidents->filter(function ($incident) {
            $services = is_array($incident->affected_services)
                ? $incident->affected_services
                : explode(',', $incident->affected_services ?? '');

            return in_array('Cell', $services);
        });

        $fbbOutages = $openIncidents->filter(function ($incident) {
            $services = is_array($incident->affected_services)
                ? $incident->affected_services
                : explode(',', $incident->affected_services ?? '');

            return in_array('Single FBB', $services);
        });

        // Get temp sites for offline list display (inactive sites)
        $tempSites = Site::where('is_active', false)->with(['region', 'location', 'technologies'])->get();

        // Calculate ISP Link Statistics
        $allLinks = IspLink::all();
        $backhaulLinks = $allLinks->where('link_type', 'Backhaul');
        $peeringLinks = $allLinks->where('link_type', 'Peering');

        // Get active ISP outages
        $activeIspOutages = Incident::with(['ispLink', 'ispLinks', 'category'])
            ->where(function($query) {
                $query->whereNotNull('isp_link_id')
                      ->orWhereHas('ispLinks');
            })
            ->whereIn('status', ['Open', 'In Progress', 'Monitoring'])
            ->get();

        // Build set of link IDs that have active incidents and collect link details
        $linksWithActiveIncidents = collect();
        $backhaulCapacityLostFromIncidents = 0;
        $peeringCapacityLostFromIncidents = 0;
        $linksWithIncidentsDetails = []; // Store detailed info about affected links

        foreach ($activeIspOutages as $incident) {
            // Handle old single ISP link
            if ($incident->isp_link_id && $incident->ispLink) {
                $linkId = $incident->isp_link_id;
                $linksWithActiveIncidents->push($linkId);
                $capacityLost = $incident->isp_capacity_lost_gbps ?? 0;

                if (!isset($linksWithIncidentsDetails[$linkId])) {
                    $linksWithIncidentsDetails[$linkId] = [
                        'link' => $incident->ispLink,
                        'incidents' => [],
                        'total_capacity_lost' => 0,
                    ];
                }
                $linksWithIncidentsDetails[$linkId]['incidents'][] = $incident;
                $linksWithIncidentsDetails[$linkId]['total_capacity_lost'] += $capacityLost;

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
                    $linksWithActiveIncidents->push($linkId);
                    $capacityLost = $link->pivot->capacity_lost_gbps ?? 0;

                    if (!isset($linksWithIncidentsDetails[$linkId])) {
                        $linksWithIncidentsDetails[$linkId] = [
                            'link' => $link,
                            'incidents' => [],
                            'total_capacity_lost' => 0,
                        ];
                    }
                    $linksWithIncidentsDetails[$linkId]['incidents'][] = $incident;
                    $linksWithIncidentsDetails[$linkId]['total_capacity_lost'] += $capacityLost;

                    if ($link->link_type === 'Backhaul') {
                        $backhaulCapacityLostFromIncidents += $capacityLost;
                    } else {
                        $peeringCapacityLostFromIncidents += $capacityLost;
                    }
                }
            }
        }
        $linksWithActiveIncidents = $linksWithActiveIncidents->unique();

        // Separate links by type for display
        $backhaulLinksDown = collect($linksWithIncidentsDetails)->filter(function($item) {
            return $item['link']->link_type === 'Backhaul';
        });
        $peeringLinksDown = collect($linksWithIncidentsDetails)->filter(function($item) {
            return $item['link']->link_type === 'Peering';
        });

        // Calculate statistics for Backhaul
        $backhaulTotal = $backhaulLinks->count();
        $backhaulDown = $linksWithActiveIncidents->intersect($backhaulLinks->pluck('id'))->count();
        $backhaulUp = $backhaulTotal - $backhaulDown;
        $backhaulTotalCapacity = $backhaulLinks->sum('total_capacity_gbps');
        $backhaulAvailableCapacity = max(0, $backhaulTotalCapacity - $backhaulCapacityLostFromIncidents);
        $backhaulAvailability = $backhaulTotalCapacity > 0 ? round(($backhaulAvailableCapacity / $backhaulTotalCapacity) * 100, 1) : 100;

        // Calculate statistics for Peering
        $peeringTotal = $peeringLinks->count();
        $peeringDown = $linksWithActiveIncidents->intersect($peeringLinks->pluck('id'))->count();
        $peeringUp = $peeringTotal - $peeringDown;
        $peeringTotalCapacity = $peeringLinks->sum('total_capacity_gbps');
        $peeringAvailableCapacity = max(0, $peeringTotalCapacity - $peeringCapacityLostFromIncidents);
        $peeringAvailability = $peeringTotalCapacity > 0 ? round(($peeringAvailableCapacity / $peeringTotalCapacity) * 100, 1) : 100;

        $ispStats = [
            'backhaul' => [
                'total' => $backhaulTotal,
                'up' => $backhaulUp,
                'down' => $backhaulDown,
                'total_capacity' => $backhaulTotalCapacity,
                'available_capacity' => $backhaulAvailableCapacity,
                'lost_capacity' => $backhaulCapacityLostFromIncidents,
                'availability_percentage' => $backhaulAvailability,
            ],
            'peering' => [
                'total' => $peeringTotal,
                'up' => $peeringUp,
                'down' => $peeringDown,
                'total_capacity' => $peeringTotalCapacity,
                'available_capacity' => $peeringAvailableCapacity,
                'lost_capacity' => $peeringCapacityLostFromIncidents,
                'availability_percentage' => $peeringAvailability,
            ],
        ];

        return view('home', compact('siteStats', 'siteOutages', 'cellOutages', 'fbbOutages', 'openIncidents', 'tempSites', 'ispStats', 'backhaulLinksDown', 'peeringLinksDown'));
    }
}
