@extends('layouts.app')

@section('header')
    @guest
        <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-red-50/50 px-4 sm:px-6 lg:px-8 py-6 sm:py-8 border-b border-gray-200/30">
            <div class="mx-auto max-w-7xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg grid place-items-center">
                            <svg class="h-7 w-7 sm:h-8 sm:w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                                Incident Management System
                            </h1>
                            <p class="mt-1 sm:mt-2 text-sm sm:text-base lg:text-lg text-gray-600 font-medium">Real-time network site status and availability</p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base text-white shadow-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
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
    <div class="bg-gray-100 min-h-screen">
        <div class="mx-auto max-w-[1920px]">

            @php
                $totalOffline = array_sum(array_column($siteStats, 'offline'));
                $totalSites = array_sum(array_column($siteStats, 'total'));
                $totalOnline = array_sum(array_column($siteStats, 'online'));
            @endphp

            <!-- 1️⃣ NETWORK SUMMARY CARDS — FLAT ENTERPRISE STYLE -->
            <div class="bg-gray-50 border-b border-gray-300 px-4 sm:px-6 py-6 sm:py-8">
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">
                    @foreach(['2g', '3g', '4g', '5g', 'fbb'] as $type)
                        @php
                            $stats = $siteStats[$type] ?? ['offline' => 0, 'online' => 0, 'total' => 0, 'online_percentage' => 100];
                            $label = config('sites.labels')[$type] ?? strtoupper($type);
                        @endphp
                        <div class="bg-white border border-gray-400 rounded shadow-sm">
                            <!-- Card Header -->
                            <div class="border-b border-gray-200 px-4 py-3">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ $label }} SITES</h3>
                            </div>

                            <!-- Card Body -->
                            <div class="px-5 py-6">
                                <!-- Offline Label -->
                                <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">OFFLINE</div>

                                <!-- Offline Count (Dominant) -->
                                <div class="text-4xl sm:text-5xl font-bold text-red-600 tabular-nums mb-4">
                                    {{ $stats['offline'] }}
                                </div>

                                <!-- Online Count (Secondary) -->
                                <div class="text-sm text-gray-700 mb-2">
                                    <span class="font-medium tabular-nums">{{ $stats['online'] }} / {{ $stats['total'] }}</span> online
                                </div>

                                <!-- Availability (Tertiary) -->
                                <div class="text-xs text-gray-500 tabular-nums">
                                    {{ $stats['online_percentage'] }}% availability
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 2️⃣ ACTIVE OUTAGES (DOMINANT SECTION) -->
            @if($siteOutages->count() > 0 || $cellOutages->count() > 0 || $fbbOutages->count() > 0)
                <div class="bg-white px-4 sm:px-6 py-4 sm:py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                        <!-- SITE OUTAGES -->
                        @if($siteOutages->count() > 0)
                            <div class="border border-gray-300">
                                <div class="bg-gray-900 text-white px-4 py-2 border-b-2 border-red-600">
                                    <h3 class="text-sm font-bold uppercase">Site Outages ({{ $siteOutages->count() }})</h3>
                                </div>
                                <div class="divide-y divide-gray-200">
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
                                        <div class="px-4 py-3">
                                            <div class="font-semibold text-sm text-gray-900 mb-2">{{ Str::limit($siteSummaryText, 80) }}</div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-2xl font-bold text-red-600 tabular-nums duration-display"
                                                     data-started="{{ $incident->started_at ? $incident->started_at->timestamp : '' }}">
                                                    {{ $incident->duration_hms ?? '—' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
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
                            <div class="border border-gray-300">
                                <div class="bg-gray-900 text-white px-4 py-2 border-b-2 border-amber-600">
                                    <h3 class="text-sm font-bold uppercase">Cell Outages ({{ $cellOutages->count() }})</h3>
                                </div>
                                <div class="divide-y divide-gray-200">
                                    @foreach($cellOutages->take(15) as $incident)
                                        <div class="px-4 py-3">
                                            <div class="font-semibold text-sm text-gray-900 mb-2">{{ Str::limit($incident->summary, 80) }}</div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-2xl font-bold text-amber-600 tabular-nums duration-display"
                                                     data-started="{{ $incident->started_at ? $incident->started_at->timestamp : '' }}">
                                                    {{ $incident->duration_hms ?? '—' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
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
                            <div class="border border-gray-300">
                                <div class="bg-gray-900 text-white px-4 py-2 border-b-2 border-orange-600">
                                    <h3 class="text-sm font-bold uppercase">FBB Outages ({{ $fbbOutages->count() }})</h3>
                                </div>
                                <div class="divide-y divide-gray-200">
                                    @foreach($fbbOutages->take(15) as $incident)
                                        @php
                                            $fbbSummary = [];
                                            foreach($incident->fbbIslands as $island) {
                                                $fbbSummary[] = $island->full_name . ' FBB';
                                            }
                                            $fbbSummaryText = !empty($fbbSummary) ? implode(', ', $fbbSummary) : $incident->summary;
                                        @endphp
                                        <div class="px-4 py-3">
                                            <div class="font-semibold text-sm text-gray-900 mb-2">{{ Str::limit($fbbSummaryText, 80) }}</div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-2xl font-bold text-orange-600 tabular-nums duration-display"
                                                     data-started="{{ $incident->started_at ? $incident->started_at->timestamp : '' }}">
                                                    {{ $incident->duration_hms ?? '—' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
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
                <div class="bg-white px-4 sm:px-6 py-6 sm:py-8 text-center border-b border-gray-300">
                    <div class="text-lg sm:text-xl font-bold text-green-700 uppercase">NO ACTIVE OUTAGES</div>
                </div>
            @endif

            <!-- 3️⃣ CONTEXT STRIP (CALM NEUTRAL METRICS) -->
            <div class="bg-gray-50 border-y border-gray-300 px-4 sm:px-6 py-3">
                <div class="flex flex-wrap items-center gap-4 sm:gap-6 lg:gap-8 text-sm">
                    <div>
                        <span class="text-gray-600">Total Sites:</span>
                        <span class="ml-2 font-semibold text-gray-900 tabular-nums">{{ $totalSites }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Online:</span>
                        <span class="ml-2 font-semibold text-gray-900 tabular-nums">{{ $totalOnline }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Offline:</span>
                        <span class="ml-2 font-semibold tabular-nums {{ $totalOffline > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $totalOffline }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Availability:</span>
                        <span class="ml-2 font-semibold text-gray-900 tabular-nums">{{ $totalSites > 0 ? round(($totalOnline / $totalSites) * 100, 1) : 100 }}%</span>
                    </div>
                </div>
            </div>

            <!-- 4️⃣ TEMPORARY SITES (DE-EMPHASIZED, BOTTOM) -->
            @if(config('sites.temp_sites_enabled', false) && isset($siteStats['temp_sites']))
                @php
                    $tempStats = $siteStats['temp_sites'];
                @endphp
                <div class="bg-gray-100 px-6 py-2 border-t border-gray-300">
                    <div class="flex items-center justify-between text-xs text-gray-600">
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
