@extends('layouts.app')

@section('content')
<div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">ISP Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Monitor ISP backhaul links capacity and availability</p>
        </div>

        <div class="flex gap-3">
            @if(Auth::user()->canEditIncidents())
                <a href="{{ route('incidents.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Report ISP Outage
                </a>
            @endif
            <a href="{{ route('isp.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                View All Links
            </a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('isp.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add ISP Link
                </a>
            @endif
        </div>
    </div>

    {{-- Backhaul, Peering & Backup Capacity Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Backhaul Capacity Section --}}
        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-950/20 dark:to-indigo-950/20 border-2 border-purple-200 dark:border-purple-900/50 rounded-xl shadow-lg dark:shadow-black/40 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-purple-900 dark:text-purple-100 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Backhaul Capacity
                    </h3>
                    <p class="text-sm text-purple-700 dark:text-purple-300 mt-1">{{ $backhaulCount }} link{{ $backhaulCount != 1 ? 's' : '' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold text-purple-600 dark:text-purple-400 tabular-nums">
                        {{ number_format($backhaulTotalCapacity, 2) }}
                    </p>
                    <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold">Gbps Total</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-purple-200 dark:border-purple-900/30">
                    <p class="text-xs text-purple-700 dark:text-purple-300 font-semibold mb-1">Current</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 tabular-nums">{{ number_format($backhaulCurrentCapacity, 2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gbps</p>
                </div>

                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-purple-200 dark:border-purple-900/30">
                    <p class="text-xs text-purple-700 dark:text-purple-300 font-semibold mb-1">Lost</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 tabular-nums">{{ number_format($backhaulLostCapacity, 2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gbps</p>
                </div>

                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-purple-200 dark:border-purple-900/30">
                    <p class="text-xs text-purple-700 dark:text-purple-300 font-semibold mb-1">Available</p>
                    <p class="text-2xl font-bold tabular-nums
                        @if($backhaulAvailability >= 95) text-green-600 dark:text-green-400
                        @elseif($backhaulAvailability >= 90) text-amber-600 dark:text-amber-400
                        @else text-red-600 dark:text-red-400
                        @endif">
                        {{ number_format($backhaulAvailability, 1) }}%
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Uptime</p>
                </div>
            </div>
        </div>

        {{-- Peering Capacity Section --}}
        <div class="bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-teal-950/20 dark:to-cyan-950/20 border-2 border-teal-200 dark:border-teal-900/50 rounded-xl shadow-lg dark:shadow-black/40 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-teal-900 dark:text-teal-100 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        Peering Capacity
                    </h3>
                    <p class="text-sm text-teal-700 dark:text-teal-300 mt-1">{{ $peeringCount }} peer{{ $peeringCount != 1 ? 's' : '' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold text-teal-600 dark:text-teal-400 tabular-nums">
                        {{ number_format($peeringTotalCapacity, 2) }}
                    </p>
                    <p class="text-xs text-teal-600 dark:text-teal-400 font-semibold">Gbps Total</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-teal-200 dark:border-teal-900/30">
                    <p class="text-xs text-teal-700 dark:text-teal-300 font-semibold mb-1">Current</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 tabular-nums">{{ number_format($peeringCurrentCapacity, 2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gbps</p>
                </div>

                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-teal-200 dark:border-teal-900/30">
                    <p class="text-xs text-teal-700 dark:text-teal-300 font-semibold mb-1">Lost</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 tabular-nums">{{ number_format($peeringLostCapacity, 2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gbps</p>
                </div>

                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-teal-200 dark:border-teal-900/30">
                    <p class="text-xs text-teal-700 dark:text-teal-300 font-semibold mb-1">Available</p>
                    <p class="text-2xl font-bold tabular-nums
                        @if($peeringAvailability >= 95) text-green-600 dark:text-green-400
                        @elseif($peeringAvailability >= 90) text-amber-600 dark:text-amber-400
                        @else text-red-600 dark:text-red-400
                        @endif">
                        {{ number_format($peeringAvailability, 1) }}%
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Uptime</p>
                </div>
            </div>
        </div>

        {{-- Backup Links Section --}}
        <div class="bg-gradient-to-br from-blue-50 to-sky-50 dark:from-blue-950/20 dark:to-sky-950/20 border-2 border-blue-200 dark:border-blue-900/50 rounded-xl shadow-lg dark:shadow-black/40 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-blue-900 dark:text-blue-100 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Backup Links
                    </h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">{{ $backupCount }} link{{ $backupCount != 1 ? 's' : '' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold text-blue-600 dark:text-blue-400 tabular-nums">
                        {{ $backupEnabledCount }}
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold">Enabled</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-blue-200 dark:border-blue-900/30">
                    <p class="text-xs text-blue-700 dark:text-blue-300 font-semibold mb-1">Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ number_format($backupTotalCapacity, 2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gbps</p>
                </div>

                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-blue-200 dark:border-blue-900/30">
                    <p class="text-xs text-blue-700 dark:text-blue-300 font-semibold mb-1">Enabled</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 tabular-nums">{{ number_format($backupEnabledCapacity, 2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gbps</p>
                </div>

                <div class="bg-white/60 dark:bg-gray-900/40 rounded-lg p-4 border border-blue-200 dark:border-blue-900/30">
                    <p class="text-xs text-blue-700 dark:text-blue-300 font-semibold mb-1">Disabled</p>
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400 tabular-nums">{{ number_format($backupDisabledCapacity, 2) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gbps</p>
                </div>
            </div>

            {{-- Note about backup links --}}
            <div class="mt-4 text-xs text-blue-700 dark:text-blue-300 bg-blue-100/50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-900/30">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Enabled capacity is automatically added to backhaul totals
            </div>
        </div>
    </div>

    {{-- Status Breakdown --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-6">
        {{-- Links Up --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 px-5 py-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $statusCounts['up'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Links Up</p>
                </div>
            </div>
        </div>

        {{-- Links Down --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 px-5 py-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $statusCounts['down'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Links Down</p>
                </div>
            </div>
        </div>

        {{-- Links Degraded --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 px-5 py-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $statusCounts['degraded'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Links Degraded</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Links Currently Under Incident - Prominent Alert --}}
    @if(count($linksWithIncidents) > 0)
        <div class="bg-gradient-to-r from-red-500 to-orange-500 border-2 border-red-600 dark:border-red-700 rounded-xl shadow-lg dark:shadow-black/40 mb-6 overflow-hidden">
            <div class="px-6 py-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center animate-pulse">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">⚠️ Links Currently Under Incident</h3>
                        <p class="text-sm text-white/90 mt-1">{{ count($linksWithIncidents) }} ISP {{ count($linksWithIncidents) === 1 ? 'link is' : 'links are' }} experiencing active outages</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($linksWithIncidents as $linkData)
                        @php
                            $link = $linkData['link'];
                            $incidents = $linkData['incidents'];
                            $totalCapacityLost = $linkData['total_capacity_lost'];
                            $impactPercent = $link->total_capacity_gbps > 0 ? round(($totalCapacityLost / $link->total_capacity_gbps) * 100, 1) : 0;
                        @endphp
                        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border-2 {{ $link->link_type === 'Backhaul' ? 'border-purple-300 dark:border-purple-700' : 'border-teal-300 dark:border-teal-700' }} shadow-md">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $link->link_type === 'Backhaul' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300' }}">
                                            {{ $link->link_type }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                            {{ count($incidents) }} incident{{ count($incidents) > 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                    <a href="{{ route('isp.show', $link) }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm hover:underline">
                                        {{ $link->circuit_id }}
                                    </a>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $link->isp_name }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="text-gray-600 dark:text-gray-400">Capacity Lost</span>
                                        <span class="font-bold text-red-600 dark:text-red-400">{{ number_format($totalCapacityLost, 2) }} Gbps</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full transition-all duration-500"
                                             style="width: {{ min($impactPercent, 100) }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs mt-1">
                                        <span class="text-gray-500 dark:text-gray-400">0 Gbps</span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ number_format($link->total_capacity_gbps, 2) }} Gbps</span>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-center font-semibold text-red-600 dark:text-red-400">{{ $impactPercent }}% Impact</p>
                                </div>

                                <div class="pt-2">
                                    @foreach($incidents as $incident)
                                        <a href="{{ route('incidents.show', $incident) }}"
                                           class="block text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline mb-1">
                                            → {{ $incident->incident_code }}: {{ Str::limit($incident->title, 30) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Active ISP Outages --}}
    @if($activeOutages->count() > 0)
        <div class="bg-white dark:bg-slate-900 border border-red-400 dark:border-red-700/50 rounded shadow-sm dark:shadow-black/40 mb-6">
            <div class="border-b border-red-200 dark:border-red-700/50 bg-red-50 dark:bg-red-950/30 px-5 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-red-900 dark:text-red-300">Active ISP Outages</h3>
                            <p class="text-sm text-red-700 dark:text-red-400 mt-0.5">{{ $activeOutages->count() }} {{ $activeOutages->count() === 1 ? 'outage' : 'outages' }} currently in progress</p>
                        </div>
                    </div>
                    <div class="hidden sm:block">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-600 dark:bg-red-700 text-white">
                            {{ $activeOutages->count() }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-slate-800">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Incident</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ISP Link</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($activeOutages as $outage)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $outage->incident_code }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $outage->category->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($outage->ispLink)
                                        {{-- Old single ISP link system --}}
                                        <a href="{{ route('isp.show', $outage->ispLink) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">
                                            {{ $outage->ispLink->circuit_id }}
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $outage->ispLink->isp_name }}</div>
                                    @elseif($outage->ispLinks && $outage->ispLinks->count() > 0)
                                        {{-- New multi-select ISP links system --}}
                                        <div class="flex flex-col gap-1">
                                            @foreach($outage->ispLinks as $link)
                                                <div>
                                                    <a href="{{ route('isp.show', $link) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">
                                                        {{ $link->circuit_id }}
                                                    </a>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $link->isp_name }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="font-medium text-gray-900 dark:text-white">Unknown</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                    {{ Str::limit($outage->description, 60) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @php
                                        $statusColors = [
                                            'Open' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            'Monitoring' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$outage->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                                        {{ $outage->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $outage->created_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <a href="{{ route('incidents.show', $outage) }}"
                                       class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Recent ISP Outages History --}}
    @if($recentIspOutages->count() > 0)
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 mb-6">
            <div class="border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-slate-800 px-5 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent ISP Outages</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Last {{ $recentIspOutages->count() }} ISP-related incidents</p>
                        </div>
                    </div>
                    <div class="hidden sm:block">
                        <a href="{{ route('logs.index', ['isp_outages' => '1']) }}"
                           class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">
                            View All ISP Logs →
                        </a>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-slate-800">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Incident</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ISP Link</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Started</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($recentIspOutages as $incident)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <a href="{{ route('incidents.show', $incident) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">
                                        {{ $incident->incident_code }}
                                    </a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $incident->category->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($incident->ispLink)
                                        {{-- Old single ISP link system --}}
                                        <a href="{{ route('isp.show', $incident->ispLink) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">
                                            {{ $incident->ispLink->circuit_id }}
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $incident->ispLink->isp_name }}</div>
                                    @elseif($incident->ispLinks && $incident->ispLinks->count() > 0)
                                        {{-- New multi-select ISP links system --}}
                                        <div class="flex flex-col gap-1">
                                            @foreach($incident->ispLinks as $link)
                                                <div>
                                                    <a href="{{ route('isp.show', $link) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">
                                                        {{ $link->circuit_id }}
                                                    </a>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $link->isp_name }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="font-medium text-gray-900 dark:text-white">Unknown</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                    {{ Str::limit($incident->description, 60) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @php
                                        $statusColors = [
                                            'Open' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            'Monitoring' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                                            'Closed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$incident->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                                        {{ $incident->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $incident->started_at ? $incident->started_at->format('M d, Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    @if($incident->status === 'Closed' && $incident->duration_hms)
                                        {{ $incident->duration_hms }}
                                    @elseif($incident->started_at)
                                        {{ $incident->started_at->diffForHumans() }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
