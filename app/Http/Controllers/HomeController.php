<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\TemporarySite;

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
        // Get total site counts from config
        $siteTotals = config('sites.total_counts');

        // Get OPEN incidents only (Open, In Progress, Monitoring)
        $openIncidents = Incident::whereIn('status', ['Open', 'In Progress', 'Monitoring'])
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

        // Calculate Temporary Sites statistics
        if (config('sites.temp_sites_enabled', false)) {
            $tempSites = TemporarySite::where('status', 'Temporary')->get();

            $tempStats = [
                '2g' => ['total' => 0, 'online' => 0, 'offline' => 0],
                '3g' => ['total' => 0, 'online' => 0, 'offline' => 0],
                '4g' => ['total' => 0, 'online' => 0, 'offline' => 0],
            ];

            foreach ($tempSites as $site) {
                $coverage = strtolower($site->coverage);

                // Count 2G
                if (str_contains($coverage, '2g')) {
                    $tempStats['2g']['total']++;
                    if ($site->is_2g_online) {
                        $tempStats['2g']['online']++;
                    } else {
                        $tempStats['2g']['offline']++;
                    }
                }

                // Count 3G
                if (str_contains($coverage, '3g')) {
                    $tempStats['3g']['total']++;
                    if ($site->is_3g_online) {
                        $tempStats['3g']['online']++;
                    } else {
                        $tempStats['3g']['offline']++;
                    }
                }

                // Count 4G
                if (str_contains($coverage, '4g')) {
                    $tempStats['4g']['total']++;
                    if ($site->is_4g_online) {
                        $tempStats['4g']['online']++;
                    } else {
                        $tempStats['4g']['offline']++;
                    }
                }
            }

            // Calculate total and percentage for temp sites
            $tempTotal = $tempStats['2g']['total'] + $tempStats['3g']['total'] + $tempStats['4g']['total'];
            $tempOnline = $tempStats['2g']['online'] + $tempStats['3g']['online'] + $tempStats['4g']['online'];
            $tempOffline = $tempStats['2g']['offline'] + $tempStats['3g']['offline'] + $tempStats['4g']['offline'];

            $siteStats['temp_sites'] = [
                'total' => $tempTotal,
                'online' => $tempOnline,
                'offline' => $tempOffline,
                'online_percentage' => $tempTotal > 0 ? round($tempOnline / $tempTotal * 100, 1) : 100,
                'breakdown' => $tempStats,
            ];
        }

        // Separate incidents into site outages and FBB outages
        $siteOutages = $openIncidents->filter(function ($incident) {
            $services = is_array($incident->affected_services)
                ? $incident->affected_services
                : explode(',', $incident->affected_services ?? '');

            return in_array('Single Site', $services) || in_array('Multiple Site', $services) || in_array('Cell', $services);
        });

        $fbbOutages = $openIncidents->filter(function ($incident) {
            $services = is_array($incident->affected_services)
                ? $incident->affected_services
                : explode(',', $incident->affected_services ?? '');

            return in_array('Single FBB', $services);
        });

        // Get temp sites for offline list display
        $tempSites = config('sites.temp_sites_enabled', false)
            ? TemporarySite::where('status', 'Temporary')->get()
            : collect();

        return view('home', compact('siteStats', 'siteOutages', 'fbbOutages', 'openIncidents', 'tempSites'));
    }
}
