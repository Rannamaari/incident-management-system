@extends('layouts.app')

@section('header')
    @guest
        <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-red-50/50 dark:from-slate-900 dark:via-slate-800 dark:to-gray-900 px-4 sm:px-6 lg:px-8 py-6 sm:py-8 border-b border-gray-200 dark:border-white/10 dark:shadow-lg dark:shadow-black/40">
            <div class="mx-auto max-w-7xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg grid place-items-center">
                            <svg class="h-7 w-7 sm:h-8 sm:w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-100 dark:to-white bg-clip-text text-transparent">
                                Incident Management System
                            </h1>
                            <p class="mt-1 sm:mt-2 text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-400 font-medium">Real-time network site status and availability</p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base text-white shadow-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400/30 transition-all duration-300 transform hover:-translate-y-0.5">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Login to Manage</span>
                        <span class="sm:hidden">Login</span>
                    </a>
                </div>
            </div>
        </div>
    @endguest
@endsection

@section('content')
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="mx-auto max-w-[1920px]">

            @php
                $totalOffline = array_sum(array_column($siteStats, 'offline'));
                $totalSites = array_sum(array_column($siteStats, 'total'));
                $totalOnline = array_sum(array_column($siteStats, 'online'));
            @endphp

            <!-- 1️⃣ NETWORK SUMMARY CARDS — FLAT ENTERPRISE STYLE -->
            <div class="bg-gray-50 dark:bg-slate-900 border-b border-gray-300 dark:border-white/10 px-4 sm:px-6 py-6 sm:py-8">
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">
                    @foreach(['2g', '3g', '4g', '5g', 'fbb'] as $type)
                        @php
                            $stats = $siteStats[$type] ?? ['offline' => 0, 'online' => 0, 'total' => 0, 'online_percentage' => 100];
                            // Custom labels for each type
                            $labels = [
                                '2g' => '2G',
                                '3g' => '3G',
                                '4g' => '4G',
                                '5g' => '5G',
                                'fbb' => 'SUPERNET (FBB)',
                            ];
                            $label = $labels[$type] ?? strtoupper($type);
                        @endphp
                        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                            <!-- Card Header -->
                            <div class="border-b border-gray-200 dark:border-white/10 px-4 py-3">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">{{ $label }}</h3>
                            </div>

                            <!-- Card Body -->
                            <div class="px-5 py-6">
                                <!-- Offline Label -->
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">OFFLINE</div>

                                <!-- Offline Count (Dominant) -->
                                <div class="text-4xl sm:text-5xl font-bold text-red-600 dark:text-red-400 tabular-nums mb-4">
                                    {{ $stats['offline'] }}
                                </div>

                                <!-- Online Count (Secondary) -->
                                <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                    <span class="font-medium tabular-nums">{{ $stats['online'] }} / {{ $stats['total'] }}</span> online
                                </div>

                                <!-- Availability (Tertiary) -->
                                <div class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                                    {{ $stats['online_percentage'] }}% availability
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ISP BACKHAUL & PEERING CARDS -->
            <div class="bg-gray-50 dark:bg-slate-900 border-b border-gray-300 dark:border-white/10 px-4 sm:px-6 py-6 sm:py-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">
                    <!-- BACKHAUL CARD -->
                    @php
                        $backhaul = $ispStats['backhaul'];
                    @endphp
                    <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                        <!-- Card Header -->
                        <div class="border-b border-gray-200 dark:border-white/10 px-4 py-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">ISP BACKHAUL LINKS</h3>
                        </div>

                        <!-- Card Body -->
                        <div class="px-5 py-6">
                            <!-- Links Down (Dominant) -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">LINKS DOWN</div>
                            <div class="text-4xl sm:text-5xl font-bold text-red-600 dark:text-red-400 tabular-nums mb-4">
                                {{ $backhaul['down'] }}
                            </div>

                            <!-- Links Up (Secondary) -->
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                <span class="font-medium tabular-nums">{{ $backhaul['up'] }} / {{ $backhaul['total'] }}</span> links up
                            </div>

                            <!-- Capacity Lost -->
                            @if($backhaul['lost_capacity'] > 0)
                                <div class="text-sm text-red-600 dark:text-red-400 mb-2">
                                    <span class="font-bold tabular-nums">{{ number_format($backhaul['lost_capacity'], 2) }} Gbps</span> capacity lost
                                </div>
                            @endif

                            <!-- Availability (Tertiary) -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                                {{ $backhaul['availability_percentage'] }}% availability
                            </div>

                            <!-- Total Capacity -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ number_format($backhaul['available_capacity'], 2) }} / {{ number_format($backhaul['total_capacity'], 2) }} Gbps available
                            </div>
                        </div>
                    </div>

                    <!-- PEERING CARD -->
                    @php
                        $peering = $ispStats['peering'];
                    @endphp
                    <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                        <!-- Card Header -->
                        <div class="border-b border-gray-200 dark:border-white/10 px-4 py-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">ISP PEERING LINKS</h3>
                        </div>

                        <!-- Card Body -->
                        <div class="px-5 py-6">
                            <!-- Links Down (Dominant) -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">LINKS DOWN</div>
                            <div class="text-4xl sm:text-5xl font-bold text-red-600 dark:text-red-400 tabular-nums mb-4">
                                {{ $peering['down'] }}
                            </div>

                            <!-- Links Up (Secondary) -->
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                <span class="font-medium tabular-nums">{{ $peering['up'] }} / {{ $peering['total'] }}</span> links up
                            </div>

                            <!-- Capacity Lost -->
                            @if($peering['lost_capacity'] > 0)
                                <div class="text-sm text-red-600 dark:text-red-400 mb-2">
                                    <span class="font-bold tabular-nums">{{ number_format($peering['lost_capacity'], 2) }} Gbps</span> capacity lost
                                </div>
                            @endif

                            <!-- Availability (Tertiary) -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                                {{ $peering['availability_percentage'] }}% availability
                            </div>

                            <!-- Total Capacity -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ number_format($peering['available_capacity'], 2) }} / {{ number_format($peering['total_capacity'], 2) }} Gbps available
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ISP LINKS DOWN DETAILS -->
            @if($backhaulLinksDown->count() > 0 || $peeringLinksDown->count() > 0)
                <div class="bg-white dark:bg-slate-900 px-4 sm:px-6 py-4 sm:py-6 border-b border-gray-300 dark:border-white/10">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- BACKHAUL LINKS DOWN -->
                        @if($backhaulLinksDown->count() > 0)
                            <div class="border border-gray-300 dark:border-white/10 dark:shadow-black/40">
                                <div class="bg-gray-900 text-white px-4 py-2 border-b-2 border-blue-600">
                                    <h3 class="text-sm font-bold uppercase">Backhaul Links Down ({{ $backhaulLinksDown->count() }})</h3>
                                </div>
                                <div class="divide-y divide-gray-200 dark:divide-white/10">
                                    @foreach($backhaulLinksDown as $linkData)
                                        @php
                                            $link = $linkData['link'];
                                            $totalCapacityLost = $linkData['total_capacity_lost'];
                                            $incidentCount = count($linkData['incidents']);
                                            // Get the earliest started_at from all incidents affecting this link
                                            $earliestIncident = collect($linkData['incidents'])->sortBy('started_at')->first();
                                            $startedAt = $earliestIncident->started_at ?? null;
                                        @endphp
                                        <div class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-white/5 transition-colors">
                                            <div class="font-semibold text-sm text-gray-900 dark:text-white mb-2">{{ $link->circuit_id }} - {{ $link->isp_name }}</div>
                                            <div class="flex items-center gap-4 mb-2">
                                                <div class="text-2xl font-bold text-red-600 dark:text-red-400 tabular-nums duration-display"
                                                     data-started="{{ $startedAt ? $startedAt->timestamp : '' }}">
                                                    {{ $earliestIncident->duration_hms ?? '—' }}
                                                </div>
                                                <div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        Started {{ $startedAt ? $startedAt->format('H:i d/m') : 'N/A' }}
                                                    </div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $link->location_b }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                                <span class="font-semibold text-red-600 dark:text-red-400 tabular-nums">{{ number_format($totalCapacityLost, 2) }} Gbps</span>
                                                <span class="text-gray-400">•</span>
                                                <span>{{ $incidentCount }} incident{{ $incidentCount > 1 ? 's' : '' }}</span>
                                                <span class="text-gray-400">•</span>
                                                <span>{{ number_format($link->total_capacity_gbps, 2) }} Gbps total</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- PEERING LINKS DOWN -->
                        @if($peeringLinksDown->count() > 0)
                            <div class="border border-gray-300 dark:border-white/10 dark:shadow-black/40">
                                <div class="bg-gray-900 text-white px-4 py-2 border-b-2 border-green-600">
                                    <h3 class="text-sm font-bold uppercase">Peering Links Down ({{ $peeringLinksDown->count() }})</h3>
                                </div>
                                <div class="divide-y divide-gray-200 dark:divide-white/10">
                                    @foreach($peeringLinksDown as $linkData)
                                        @php
                                            $link = $linkData['link'];
                                            $totalCapacityLost = $linkData['total_capacity_lost'];
                                            $incidentCount = count($linkData['incidents']);
                                            // Get the earliest started_at from all incidents affecting this link
                                            $earliestIncident = collect($linkData['incidents'])->sortBy('started_at')->first();
                                            $startedAt = $earliestIncident->started_at ?? null;
                                        @endphp
                                        <div class="px-4 py-3 hover:bg-green-50 dark:hover:bg-white/5 transition-colors">
                                            <div class="font-semibold text-sm text-gray-900 dark:text-white mb-2">{{ $link->circuit_id }} - {{ $link->isp_name }}</div>
                                            <div class="flex items-center gap-4 mb-2">
                                                <div class="text-2xl font-bold text-red-600 dark:text-red-400 tabular-nums duration-display"
                                                     data-started="{{ $startedAt ? $startedAt->timestamp : '' }}">
                                                    {{ $earliestIncident->duration_hms ?? '—' }}
                                                </div>
                                                <div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        Started {{ $startedAt ? $startedAt->format('H:i d/m') : 'N/A' }}
                                                    </div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $link->location_b }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                                <span class="font-semibold text-red-600 dark:text-red-400 tabular-nums">{{ number_format($totalCapacityLost, 2) }} Gbps</span>
                                                <span class="text-gray-400">•</span>
                                                <span>{{ $incidentCount }} incident{{ $incidentCount > 1 ? 's' : '' }}</span>
                                                <span class="text-gray-400">•</span>
                                                <span>{{ number_format($link->total_capacity_gbps, 2) }} Gbps total</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 2️⃣ ACTIVE OUTAGES (DOMINANT SECTION) -->
            @if($siteOutages->count() > 0 || $cellOutages->count() > 0 || $fbbOutages->count() > 0)
                <div class="bg-white dark:bg-slate-900 px-4 sm:px-6 py-4 sm:py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                        <!-- SITE OUTAGES -->
                        @if($siteOutages->count() > 0)
                            <div class="border border-gray-300 dark:border-white/10 dark:shadow-black/40">
                                <div class="bg-gray-900 text-white px-4 py-2 border-b-2 border-red-600">
                                    <h3 class="text-sm font-bold uppercase">Site Outages ({{ $siteOutages->count() }})</h3>
                                </div>
                                <div class="divide-y divide-gray-200 dark:divide-white/10">
                                    @foreach($siteOutages->take(15) as $incident)
                                        @php
                                            $siteSummary = [];
                                            if ($incident->sites->isNotEmpty()) {
                                                foreach($incident->sites as $site) {
                                                    $techs = $site->pivot->affected_technologies ?? [];
                                                    if (is_string($techs)) {
                                                        $techs = json_decode($techs, true) ?? [];
                                                    }
                                                    if (!empty($techs) && is_array($techs)) {
                                                        $siteSummary[] = $site->site_code . ' ' . implode('/', $techs);
                                                    }
                                                }
                                            }
                                            $siteSummaryText = !empty($siteSummary) ? implode(', ', $siteSummary) : $incident->summary;
                                        @endphp
                                        <div class="px-4 py-3 hover:bg-red-50 dark:hover:bg-white/5 transition-colors">
                                            <div class="font-semibold text-sm text-gray-900 dark:text-white mb-2">{{ Str::limit($siteSummaryText, 80) }}</div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-2xl font-bold text-red-600 dark:text-red-400 tabular-nums duration-display"
                                                     data-started="{{ $incident->started_at ? $incident->started_at->timestamp : '' }}">
                                                    {{ $incident->duration_hms ?? '—' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Started {{ $incident->started_at ? $incident->started_at->format('H:i d/m') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- CELL OUTAGES -->
                        @if($cellOutages->count() > 0)
                            <div class="border border-gray-300 dark:border-white/10 dark:shadow-black/40">
                                <div class="bg-gray-900 text-white px-4 py-2 border-b-2 border-amber-600">
                                    <h3 class="text-sm font-bold uppercase">Cell Outages ({{ $cellOutages->count() }})</h3>
                                </div>
                                <div class="divide-y divide-gray-200 dark:divide-white/10">
                                    @foreach($cellOutages->take(15) as $incident)
                                        <div class="px-4 py-3 hover:bg-amber-50 dark:hover:bg-white/5 transition-colors">
                                            <div class="font-semibold text-sm text-gray-900 dark:text-white mb-2">{{ Str::limit($incident->summary, 80) }}</div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-2xl font-bold text-amber-600 tabular-nums duration-display"
                                                     data-started="{{ $incident->started_at ? $incident->started_at->timestamp : '' }}">
                                                    {{ $incident->duration_hms ?? '—' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Started {{ $incident->started_at ? $incident->started_at->format('H:i d/m') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- FBB OUTAGES -->
                        @if($fbbOutages->count() > 0)
                            <div class="border border-gray-300 dark:border-white/10 dark:shadow-black/40">
                                <div class="bg-gray-900 text-white px-4 py-2 border-b-2 border-orange-600">
                                    <h3 class="text-sm font-bold uppercase">FBB Outages ({{ $fbbOutages->count() }})</h3>
                                </div>
                                <div class="divide-y divide-gray-200 dark:divide-white/10">
                                    @foreach($fbbOutages->take(15) as $incident)
                                        @php
                                            $fbbSummary = [];
                                            foreach($incident->fbbIslands as $island) {
                                                $fbbSummary[] = $island->full_name . ' FBB';
                                            }
                                            $fbbSummaryText = !empty($fbbSummary) ? implode(', ', $fbbSummary) : $incident->summary;
                                        @endphp
                                        <div class="px-4 py-3 hover:bg-orange-50 dark:hover:bg-white/5 transition-colors">
                                            <div class="font-semibold text-sm text-gray-900 dark:text-white mb-2">{{ Str::limit($fbbSummaryText, 80) }}</div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-2xl font-bold text-orange-600 tabular-nums duration-display"
                                                     data-started="{{ $incident->started_at ? $incident->started_at->timestamp : '' }}">
                                                    {{ $incident->duration_hms ?? '—' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Started {{ $incident->started_at ? $incident->started_at->format('H:i d/m') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 px-4 sm:px-6 py-6 sm:py-8 text-center border-b border-gray-300 dark:border-white/10">
                    <div class="text-lg sm:text-xl font-bold text-green-700 dark:text-green-400 uppercase">NO ACTIVE OUTAGES</div>
                </div>
            @endif

            <!-- 3️⃣ CONTEXT STRIP (CALM NEUTRAL METRICS) -->
            <div class="bg-gray-50 dark:bg-slate-900 border-y border-gray-300 dark:border-white/10 px-4 sm:px-6 py-3">
                <div class="space-y-2">
                    <!-- Sites Metrics -->
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 lg:gap-8 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Total Sites:</span>
                            <span class="ml-2 font-semibold text-gray-900 dark:text-gray-100 tabular-nums">{{ $totalSites }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Online:</span>
                            <span class="ml-2 font-semibold text-gray-900 dark:text-gray-100 tabular-nums">{{ $totalOnline }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Offline:</span>
                            <span class="ml-2 font-semibold tabular-nums {{ $totalOffline > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">{{ $totalOffline }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Availability:</span>
                            <span class="ml-2 font-semibold text-gray-900 dark:text-gray-100 tabular-nums">{{ $totalSites > 0 ? round(($totalOnline / $totalSites) * 100, 1) : 100 }}%</span>
                        </div>
                    </div>

                    <!-- ISP Metrics -->
                    @php
                        $totalIspLinks = $ispStats['backhaul']['total'] + $ispStats['peering']['total'];
                        $totalIspDown = $ispStats['backhaul']['down'] + $ispStats['peering']['down'];
                        $totalCapacityLost = $ispStats['backhaul']['lost_capacity'] + $ispStats['peering']['lost_capacity'];
                    @endphp
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 lg:gap-8 text-sm border-t border-gray-300 dark:border-white/10 pt-2">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">ISP Links:</span>
                            <span class="ml-2 font-semibold text-gray-900 dark:text-gray-100 tabular-nums">{{ $totalIspLinks }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Links Down:</span>
                            <span class="ml-2 font-semibold tabular-nums {{ $totalIspDown > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">{{ $totalIspDown }}</span>
                        </div>
                        @if($totalCapacityLost > 0)
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Capacity Lost:</span>
                                <span class="ml-2 font-semibold text-red-600 dark:text-red-400 tabular-nums">{{ number_format($totalCapacityLost, 2) }} Gbps</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 4️⃣ TEMPORARY SITES (DE-EMPHASIZED, BOTTOM) -->
            @if(config('sites.temp_sites_enabled', false) && isset($siteStats['temp_sites']))
                @php
                    $tempStats = $siteStats['temp_sites'];
                @endphp
                <div class="bg-gray-100 dark:bg-gray-900 px-6 py-2 border-t border-gray-300 dark:border-gray-600">
                    <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                        <span>Temporary Sites</span>
                        <span>{{ $tempStats['online'] }}/{{ $tempStats['total'] }} online ({{ $tempStats['online_percentage'] }}%)</span>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- LIVE DURATION UPDATE SCRIPT -->
    <script>
        (function() {
            'use strict';

            // Global duration updater - single timer for all durations
            function updateDurations() {
                const now = Math.floor(Date.now() / 1000);
                const durationElements = document.querySelectorAll('.duration-display[data-started]');

                durationElements.forEach(el => {
                    const startedTimestamp = parseInt(el.dataset.started, 10);
                    if (!startedTimestamp || isNaN(startedTimestamp)) return;

                    const seconds = now - startedTimestamp;
                    if (seconds < 0) return;

                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    const secs = seconds % 60;

                    el.textContent =
                        String(hours).padStart(2, '0') + ':' +
                        String(minutes).padStart(2, '0') + ':' +
                        String(secs).padStart(2, '0');
                });
            }

            // Initial update
            updateDurations();

            // Update every 30 seconds
            setInterval(updateDurations, 30000);
        })();
    </script>
@endsection
