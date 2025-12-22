<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;

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

        return view('home', compact('siteStats', 'siteOutages', 'fbbOutages', 'openIncidents'));
    }
}
