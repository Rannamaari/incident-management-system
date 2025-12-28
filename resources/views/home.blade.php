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
    <div class="py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">

            <!-- Site Status Cards - Mobile View -->
            <div class="mb-6 lg:hidden space-y-3">
                @foreach(['2g', '3g', '4g', '5g', 'fbb'] as $type)
                    @php
                        $stats = $siteStats[$type] ?? ['total' => 0, 'online' => 0, 'offline' => 0, 'online_percentage' => 100];
                        $colors = config('sites.colors');
                        $labels = config('sites.labels');
                        $config = $colors[$type] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon_bg' => 'bg-gray-100', 'accent' => 'from-gray-50'];
                        $label = $labels[$type] ?? strtoupper($type);
                    @endphp
                    <div class="rounded-xl border border-gray-100/50 bg-white/80 backdrop-blur-sm p-4 shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 rounded-xl p-3 {{ $config['icon_bg'] }}">
                                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-heading font-medium text-gray-600 mb-1">{{ $label }}</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-2xl font-bold tracking-tight {{ $config['text'] }}">{{ $stats['online'] }}</p>
                                    <p class="text-xs text-gray-500">/ {{ $stats['total'] }}</p>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="text-gray-600">{{ $stats['online_percentage'] }}% online</span>
                                    @if($stats['offline'] > 0)
                                        <span class="text-red-600 font-semibold">{{ $stats['offline'] }} offline</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $config['bg'] }} transition-all duration-500"
                                 style="width: {{ $stats['online_percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Site Status Cards - Desktop View -->
            <div class="mb-8 hidden lg:grid grid-cols-3 gap-8 xl:grid-cols-5 xl:gap-6">
                @foreach(['2g', '3g', '4g', '5g', 'fbb'] as $type)
                    @php
                        $stats = $siteStats[$type] ?? ['total' => 0, 'online' => 0, 'offline' => 0, 'online_percentage' => 100];
                        $colors = config('sites.colors');
                        $labels = config('sites.labels');
                        $config = $colors[$type] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon_bg' => 'bg-gray-100', 'accent' => 'from-gray-50'];
                        $label = $labels[$type] ?? strtoupper($type);
                    @endphp
                    @php
                        $techUpper = strtoupper($type);
                        $impactedForThis = $impactedByTech[$techUpper] ?? [];
                    @endphp
                    <div x-data="{ expanded: false }" class="group relative rounded-2xl sm:rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm p-6 lg:p-7 shadow-lg transition-all duration-300 hover:border-gray-200/70 hover:shadow-2xl hover:bg-white/90">
                        <!-- Card Content -->
                        <div class="flex flex-col gap-4 mb-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="mb-3 text-sm sm:text-base font-heading font-semibold text-gray-600">{{ $label }}</p>
                                    <p class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight {{ $config['text'] }}">
                                        {{ $stats['online'] }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-500">of {{ $stats['total'] }} online</p>
                                </div>
                                <div class="flex-shrink-0 rounded-2xl p-3 lg:p-4 {{ $config['icon_bg'] }} transition-colors">
                                    <svg class="h-10 w-10 lg:h-12 lg:w-12 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Status Bar -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm text-gray-600">
                                <span class="font-medium">Online: {{ $stats['online_percentage'] }}%</span>
                                @if($stats['offline'] > 0)
                                    <span class="text-red-600 font-bold">{{ $stats['offline'] }} offline</span>
                                @endif
                            </div>
                            <div class="h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $config['bg'] }} transition-all duration-500"
                                     style="width: {{ $stats['online_percentage'] }}%"></div>
                            </div>
                        </div>

                        <!-- Impacted Sites Button/List -->
                        @if(count($impactedForThis) > 0)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button @click="expanded = !expanded"
                                        class="w-full flex items-center justify-between text-sm font-semibold text-red-600 hover:text-red-700 transition-colors">
                                    <span>{{ count($impactedForThis) }} site{{ count($impactedForThis) > 1 ? 's' : '' }} down</span>
                                    <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="expanded" x-transition class="mt-3 space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($impactedForThis as $site)
                                        <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg border border-red-100">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $site['site_code'] }}</p>
                                                @if($site['display_name'] !== $site['site_code'])
                                                    <p class="text-xs text-gray-500 truncate">{{ $site['display_name'] }}</p>
                                                @endif
                                            </div>
                                            <span class="flex-shrink-0 ml-2 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-red-100 text-red-800">
                                                {{ $site['region'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Gradient Overlay -->
                        <div class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition-all duration-300 group-hover:opacity-30 bg-gradient-to-br {{ $config['accent'] }} to-transparent"></div>
                        <div class="absolute top-5 right-5 w-2 h-2 rounded-full bg-gradient-to-r {{ $config['accent'] }} opacity-60"></div>
                    </div>
                @endforeach
            </div>

            <!-- Temporary Sites Section - Compact -->
            @if(config('sites.temp_sites_enabled', false) && isset($siteStats['temp_sites']))
                @php
                    $tempStats = $siteStats['temp_sites'];
                    $tempConfig = config('sites.colors.temp_sites');
                @endphp
                <div class="mb-6">
                    <div class="rounded-xl border border-teal-100/50 bg-white/80 backdrop-blur-sm p-4 shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-heading font-bold text-gray-900 flex items-center gap-2">
                                <svg class="h-4 w-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                </svg>
                                Temporary Sites
                            </h3>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="font-semibold {{ $tempConfig['text'] }}">{{ $tempStats['online'] }}/{{ $tempStats['total'] }}</span>
                                <span class="text-gray-500">•</span>
                                <span class="font-medium text-green-600">{{ $tempStats['online_percentage'] }}% online</span>
                            </div>
                        </div>

                        <!-- Technology Breakdown - Horizontal -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach(['2g', '3g', '4g', '5g'] as $tech)
                                @if($tempStats['breakdown'][$tech]['total'] > 0)
                                    <div class="rounded-lg border border-gray-200 p-2 bg-gray-50">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs font-semibold text-gray-700">{{ strtoupper($tech) }}</span>
                                            <span class="text-xs font-bold {{ $tempStats['breakdown'][$tech]['offline'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $tempStats['breakdown'][$tech]['online'] }}/{{ $tempStats['breakdown'][$tech]['total'] }}
                                            </span>
                                        </div>
                                        <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $tempStats['breakdown'][$tech]['offline'] > 0 ? 'from-red-500 to-red-600' : 'from-green-500 to-green-600' }} transition-all duration-500"
                                                 style="width: {{ $tempStats['breakdown'][$tech]['total'] > 0 ? round($tempStats['breakdown'][$tech]['online'] / $tempStats['breakdown'][$tech]['total'] * 100) : 100 }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Currently Impacted Sites Section -->
            @php
                // Collect all impacted sites from all active incidents
                $impactedSites = [];

                if (isset($siteOutages) && $siteOutages->count() > 0) {
                    foreach($siteOutages as $incident) {
                        // Try to get from relationship first
                        if ($incident->sites && $incident->sites->isNotEmpty()) {
                            foreach($incident->sites as $site) {
                                $techs = $site->pivot->affected_technologies ?? [];
                                if (is_string($techs)) {
                                    $techs = json_decode($techs, true) ?? [];
                                }
                                if (!empty($techs) && is_array($techs)) {
                                    $siteKey = $site->site_code;
                                    if (!isset($impactedSites[$siteKey])) {
                                        $impactedSites[$siteKey] = [
                                            'site_code' => $site->site_code,
                                            'display_name' => $site->display_name ?? $site->site_code,
                                            'region' => optional($site->region)->code ?? explode('-', $site->site_code)[0] ?? 'N/A',
                                            'technologies' => []
                                        ];
                                    }
                                    $impactedSites[$siteKey]['technologies'] = array_unique(array_merge(
                                        $impactedSites[$siteKey]['technologies'],
                                        $techs
                                    ));
                                }
                            }
                        }

                        // Fallback: parse from summary if no sites in relationship
                        if ($incident->summary && (!$incident->sites || $incident->sites->isEmpty())) {
                            $summaryParts = explode(',', $incident->summary);
                            foreach ($summaryParts as $part) {
                                $part = trim($part);
                                // Skip FBB islands (contain " FBB" or pattern like "GA - Island")
                                if (stripos($part, ' FBB') === false && !preg_match('/^[A-Z]{1,3}\s*-\s*[A-Za-z]/', $part)) {
                                    // Match pattern: AA-3752 2G/3G/4G or similar
                                    if (preg_match('/([A-Z]+-[A-Z0-9]+-\d+)\s+(.+)/', $part, $matches)) {
                                        $siteCode = $matches[1];
                                        $techString = $matches[2];
                                        $techs = explode('/', $techString);

                                        if (!isset($impactedSites[$siteCode])) {
                                            $impactedSites[$siteCode] = [
                                                'site_code' => $siteCode,
                                                'display_name' => $siteCode,
                                                'region' => explode('-', $siteCode)[0] ?? 'N/A',
                                                'technologies' => []
                                            ];
                                        }
                                        $impactedSites[$siteCode]['technologies'] = array_unique(array_merge(
                                            $impactedSites[$siteCode]['technologies'] ?? [],
                                            $techs
                                        ));
                                    }
                                }
                            }
                        }
                    }
                    ksort($impactedSites);
                }

                // Group impacted sites by technology for display on cards
                $impactedByTech = [
                    '2G' => [],
                    '3G' => [],
                    '4G' => [],
                    '5G' => [],
                    'FBB' => []
                ];

                foreach($impactedSites as $site) {
                    foreach($site['technologies'] as $tech) {
                        $techUpper = strtoupper($tech);
                        if (isset($impactedByTech[$techUpper])) {
                            $impactedByTech[$techUpper][] = $site;
                        }
                    }
                }
            @endphp


            <!-- Active Outages Section -->
            @if($siteOutages->count() > 0 || $cellOutages->count() > 0 || $fbbOutages->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-heading font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Active Outages
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                        <!-- Site Outages -->
                        @if($siteOutages->count() > 0)
                            <div class="rounded-2xl border border-red-100 bg-white/80 backdrop-blur-sm shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-red-50 to-rose-50 px-6 py-4 border-b border-red-100">
                                    <h3 class="font-heading font-semibold text-red-900 flex items-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                        </svg>
                                        Site Outages ({{ $siteOutages->count() }})
                                    </h3>
                                </div>
                                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                                    @foreach($siteOutages as $incident)
                                        <div class="p-4 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1 min-w-0">
                                                    @php
                                                        $siteSummary = [];

                                                        // Try to get sites from relationship first
                                                        if ($incident->sites->isNotEmpty()) {
                                                            foreach($incident->sites as $site) {
                                                                $techs = $site->pivot->affected_technologies ?? [];
                                                                // Handle if it's still a JSON string
                                                                if (is_string($techs)) {
                                                                    $techs = json_decode($techs, true) ?? [];
                                                                }
                                                                if (!empty($techs) && is_array($techs)) {
                                                                    $techStr = implode('/', $techs);
                                                                    $siteSummary[] = $site->site_code . ' ' . $techStr;
                                                                }
                                                            }
                                                        }

                                                        // Fallback: Parse summary field to extract site codes (for old incidents)
                                                        if (empty($siteSummary) && $incident->summary) {
                                                            // Extract site codes from summary (pattern: REGION-LOCATION-NUMBER)
                                                            // Split by comma and filter out FBB islands (containing " FBB")
                                                            $summaryParts = explode(',', $incident->summary);
                                                            foreach ($summaryParts as $part) {
                                                                $part = trim($part);
                                                                // Skip FBB islands (contain " FBB" or " - " pattern like "GA - Dhevvadhoo FBB")
                                                                if (stripos($part, ' FBB') === false &&
                                                                    !preg_match('/^[A-Z]+ - [A-Za-z]+/', $part)) {
                                                                    $siteSummary[] = $part;
                                                                }
                                                            }
                                                        }

                                                        $siteSummaryText = !empty($siteSummary) ? implode(', ', $siteSummary) : 'No site details available';
                                                    @endphp
                                                    <p class="text-sm font-semibold text-gray-900" style="overflow-wrap: break-word; word-wrap: break-word;">
                                                        {{ $siteSummaryText }}
                                                    </p>
                                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                                        @if($incident->sites_2g_impacted > 0)
                                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-md">2G: {{ $incident->sites_2g_impacted }}</span>
                                                        @endif
                                                        @if($incident->sites_3g_impacted > 0)
                                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-md">3G: {{ $incident->sites_3g_impacted }}</span>
                                                        @endif
                                                        @if($incident->sites_4g_impacted > 0)
                                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-md">4G: {{ $incident->sites_4g_impacted }}</span>
                                                        @endif
                                                        @if($incident->sites_5g_impacted > 0)
                                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-md">5G: {{ $incident->sites_5g_impacted }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-500">
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            Started: {{ $incident->started_at ? $incident->started_at->format('M d, H:i') : 'N/A' }}
                                                        </span>
                                                        @if($incident->duration_hms)
                                                            <span class="flex items-center gap-1 text-orange-600 font-medium">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Duration: {{ $incident->duration_hms }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="flex-shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full {{ $incident->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $incident->status }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Cell Outages -->
                        @if($cellOutages->count() > 0)
                            <div class="rounded-2xl border border-yellow-100 bg-white/80 backdrop-blur-sm shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-6 py-4 border-b border-yellow-100">
                                    <h3 class="font-heading font-semibold text-yellow-900 flex items-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        Cell Outages ({{ $cellOutages->count() }})
                                    </h3>
                                </div>
                                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                                    @foreach($cellOutages as $incident)
                                        <div class="p-4 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900">{{ $incident->summary }}</p>
                                                    <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-500">
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            Started: {{ $incident->started_at ? $incident->started_at->format('M d, H:i') : 'N/A' }}
                                                        </span>
                                                        @if($incident->duration_hms)
                                                            <span class="flex items-center gap-1 text-orange-600 font-medium">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Duration: {{ $incident->duration_hms }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="flex-shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full {{ $incident->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $incident->status }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- FBB Outages -->
                        @if($fbbOutages->count() > 0)
                            <div class="rounded-2xl border border-orange-100 bg-white/80 backdrop-blur-sm shadow-lg overflow-hidden">
                                <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b border-orange-100">
                                    <h3 class="font-heading font-semibold text-orange-900 flex items-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                        FBB Outages ({{ $fbbOutages->count() }})
                                    </h3>
                                </div>
                                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                                    @foreach($fbbOutages as $incident)
                                        <div class="p-4 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1 min-w-0">
                                                    @php
                                                        $fbbSummary = [];
                                                        foreach($incident->fbbIslands as $island) {
                                                            $fbbSummary[] = $island->full_name . ' FBB';
                                                        }
                                                        $fbbSummaryText = !empty($fbbSummary) ? implode(', ', $fbbSummary) : $incident->summary;
                                                    @endphp
                                                    <p class="text-sm font-semibold text-gray-900" style="overflow-wrap: break-word; word-wrap: break-word;">{{ $fbbSummaryText }}</p>
                                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                                        @if($incident->fbb_impacted > 0)
                                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-md">FBB: {{ $incident->fbb_impacted }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-500">
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            Started: {{ $incident->started_at ? $incident->started_at->format('M d, H:i') : 'N/A' }}
                                                        </span>
                                                        @if($incident->duration_hms)
                                                            <span class="flex items-center gap-1 text-orange-600 font-medium">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Duration: {{ $incident->duration_hms }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="flex-shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full {{ $incident->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $incident->status }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- All Systems Operational -->
                <div class="rounded-2xl border border-green-100 bg-gradient-to-r from-green-50/50 to-emerald-50/50 p-8 text-center shadow-lg">
                    <svg class="mx-auto h-16 w-16 text-green-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-2xl font-heading font-bold text-green-900 mb-2">All Systems Operational</h3>
                    <p class="text-green-700">No active outages at this time</p>
                </div>
            @endif

            <!-- Quick Links for Authenticated Users -->
            @auth
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('incidents.index') }}"
                       class="group relative rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-heading font-bold text-gray-900 mb-2">View All Incidents</h3>
                                <p class="text-gray-600 text-sm">Manage incident records</p>
                            </div>
                            <svg class="h-8 w-8 text-red-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </div>
                    </a>

                    @if(auth()->user()->canEditIncidents())
                        <a href="{{ route('incidents.create') }}"
                           class="group relative rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-heading font-bold text-gray-900 mb-2">Create Incident</h3>
                                    <p class="text-gray-600 text-sm">Report a new incident</p>
                                </div>
                                <svg class="h-8 w-8 text-blue-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('smart-parser.index') }}"
                           class="group relative rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-heading font-bold text-gray-900 mb-2">AI Incident Manager</h3>
                                    <p class="text-gray-600 text-sm">AI-powered incident creation</p>
                                </div>
                                <svg class="h-8 w-8 text-purple-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </a>
                    @endif
                </div>
            @endauth

        </div>
    </div>
@endsection
