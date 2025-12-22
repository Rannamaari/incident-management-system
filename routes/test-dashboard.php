<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-dashboard', function () {
    $siteTotals = config('sites.total_counts');
    $openIncidents = \App\Models\Incident::whereIn('status', ['Open', 'In Progress', 'Monitoring'])->get();
    
    $impactedCounts = [
        '2g' => $openIncidents->sum('sites_2g_impacted'),
        '3g' => $openIncidents->sum('sites_3g_impacted'),
        '4g' => $openIncidents->sum('sites_4g_impacted'),
        '5g' => $openIncidents->sum('sites_5g_impacted'),
        'fbb' => $openIncidents->sum('fbb_impacted'),
    ];
    
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
    
    return response()->json([
        'config_loaded' => !empty($siteTotals),
        'site_totals' => $siteTotals,
        'open_incidents_count' => $openIncidents->count(),
        'impacted_counts' => $impactedCounts,
        'site_stats' => $siteStats,
        'colors_config' => config('sites.colors'),
        'labels_config' => config('sites.labels'),
    ]);
})->middleware('auth');
