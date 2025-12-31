@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    {{-- Header with Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $ispLink->circuit_id }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">ISP Link Details</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('isp.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            @if(Auth::user()->canEditIncidents())
                @if($ispLink->hasActiveIncidents())
                    <button type="button" onclick="openRestoreModal()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Restore Link
                    </button>
                @else
                    <a href="{{ route('incidents.create', ['isp_link_id' => $ispLink->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Report Outage
                    </a>
                @endif
                @if($ispLink->link_type === 'Backup')
                    <form method="POST" action="{{ route('isp.toggle-enabled', $ispLink) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 {{ $ispLink->is_enabled ? 'bg-gray-500 dark:bg-gray-600 hover:bg-gray-600 dark:hover:bg-gray-700' : 'bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600' }} text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($ispLink->is_enabled)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                @endif
                            </svg>
                            {{ $ispLink->is_enabled ? 'Disable Link' : 'Enable Link' }}
                        </button>
                    </form>
                @endif
            @endif
            @if(Auth::user()->isAdmin())
                <a href="{{ route('isp.edit', $ispLink) }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-600 dark:bg-amber-700 hover:bg-amber-700 dark:hover:bg-amber-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('isp.destroy', $ispLink) }}" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this ISP link? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- 2 Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column - ISP Link Details --}}
        <div class="space-y-6">
            {{-- Basic Information --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                <div class="border-b border-gray-200 dark:border-white/10 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">ISP Link Information</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Circuit ID</p>
                        <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $ispLink->circuit_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">ISP Name</p>
                        <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $ispLink->isp_name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Link Type</p>
                            <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $ispLink->link_type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                            @if($ispLink->hasActiveIncidents())
                                <div class="mt-1 flex flex-col gap-1">
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 border border-red-300 dark:border-red-700 animate-pulse">
                                        ⚠️ Under Incident
                                    </span>
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $ispLink->status_color_class }} opacity-60">
                                        Base: {{ $ispLink->status }}
                                    </span>
                                </div>
                            @else
                                <span class="mt-1 inline-block px-3 py-1 rounded-full text-sm font-medium {{ $ispLink->status_color_class }}">
                                    {{ $ispLink->status }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location Information --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                <div class="border-b border-gray-200 dark:border-white/10 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Locations</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 p-3 bg-gray-50 dark:bg-slate-800 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Location A</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ispLink->location_a }}</p>
                        </div>
                        <svg class="w-6 h-6 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                        <div class="flex-1 p-3 bg-gray-50 dark:bg-slate-800 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Location B</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ispLink->location_b }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Capacity Information --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                <div class="border-b border-gray-200 dark:border-white/10 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Capacity Information</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                            <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400 tabular-nums">
                                {{ number_format($ispLink->total_capacity_gbps, 2) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Gbps</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Current</p>
                            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400 tabular-nums">
                                {{ number_format($ispLink->current_capacity_gbps, 2) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Gbps</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Lost</p>
                            <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400 tabular-nums">
                                {{ number_format($ispLink->lost_capacity_gbps, 2) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Gbps</p>
                        </div>
                    </div>

                    {{-- Capacity Progress Bar --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Availability</p>
                            <p class="text-sm font-semibold
                                @if($ispLink->availability_percentage >= 95) text-green-600 dark:text-green-400
                                @elseif($ispLink->availability_percentage >= 90) text-amber-600 dark:text-amber-400
                                @else text-red-600 dark:text-red-400
                                @endif">
                                {{ number_format($ispLink->availability_percentage, 2) }}%
                            </p>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-300
                                @if($ispLink->availability_percentage >= 95) bg-green-500
                                @elseif($ispLink->availability_percentage >= 90) bg-amber-500
                                @else bg-red-500
                                @endif"
                                 style="width: {{ $ispLink->availability_percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PRTG Integration --}}
            @if($ispLink->prtg_sensor_id || $ispLink->prtg_api_endpoint)
                <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                    <div class="border-b border-gray-200 dark:border-white/10 px-5 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">PRTG Integration</h2>
                    </div>
                    <div class="p-5 space-y-3">
                        @if($ispLink->prtg_sensor_id)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Sensor ID</p>
                                <p class="mt-1 text-base font-medium text-gray-900 dark:text-white font-mono">{{ $ispLink->prtg_sensor_id }}</p>
                            </div>
                        @endif
                        @if($ispLink->prtg_api_endpoint)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">API Endpoint</p>
                                <p class="mt-1 text-base font-medium text-gray-900 dark:text-white break-all">
                                    <a href="{{ $ispLink->prtg_api_endpoint }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $ispLink->prtg_api_endpoint }}
                                    </a>
                                </p>
                            </div>
                        @endif
                        @if($ispLink->last_prtg_sync)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Last Sync</p>
                                <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $ispLink->last_prtg_sync->format('M d, Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Notes --}}
            @if($ispLink->notes)
                <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                    <div class="border-b border-gray-200 dark:border-white/10 px-5 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Notes</h2>
                    </div>
                    <div class="p-5">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $ispLink->notes }}</p>
                    </div>
                </div>
            @endif

            {{-- Metadata --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                <div class="border-b border-gray-200 dark:border-white/10 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Metadata</h2>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Created By:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $ispLink->creator->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Created:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $ispLink->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    @if($ispLink->updater)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Last Updated By:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $ispLink->updater->name }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $ispLink->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Escalation Matrix & Incident History --}}
        <div class="space-y-6">
            {{-- Escalation Matrix --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                <div class="border-b border-gray-200 dark:border-white/10 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Escalation Matrix</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Contact information for escalation levels</p>
                </div>
                <div class="p-5">
                    @foreach(['L1', 'L2', 'L3'] as $level)
                        @php
                            $levelContacts = $ispLink->escalationContacts->where('escalation_level', $level);
                        @endphp
                        @if($levelContacts->count() > 0)
                            <div class="mb-6 last:mb-0">
                                <div class="flex items-center mb-3">
                                    <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $level }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Level {{ substr($level, 1) }} Support</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $levelContacts->count() }} contact(s)</p>
                                    </div>
                                </div>
                                
                                <div class="ml-15 space-y-3">
                                    @foreach($levelContacts as $contact)
                                        <div class="p-4 bg-gray-50 dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-white/10">
                                            <div class="flex items-start justify-between mb-2">
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $contact->contact_name }}</p>
                                                @if($contact->is_primary)
                                                    <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-medium rounded">Primary</span>
                                                @endif
                                            </div>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    <a href="tel:{{ $contact->contact_phone }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                        {{ $contact->contact_phone }}
                                                    </a>
                                                </div>
                                                @if($contact->contact_email)
                                                    <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        <a href="mailto:{{ $contact->contact_email }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                            {{ $contact->contact_email }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if($ispLink->escalationContacts->count() == 0)
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No escalation contacts defined</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Incident History --}}
            <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40">
                <div class="border-b border-gray-200 dark:border-white/10 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Incident History</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Recent outages and incidents for this link</p>
                </div>
                <div class="p-5">
                    @if($incidents->count() > 0)
                        <div class="space-y-3">
                            @foreach($incidents as $incident)
                                <div class="p-4 bg-gray-50 dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-white/10 hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <a href="{{ route('incidents.show', $incident) }}" class="font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                                {{ $incident->incident_code }}
                                            </a>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $incident->category->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        @php
                                            $statusColors = [
                                                'Open' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                'Monitoring' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                                                'Resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'Closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$incident->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                                            {{ $incident->status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                        {{ Str::limit($incident->description, 80) }}
                                    </p>
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center gap-3">
                                            <span class="flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $incident->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <a href="{{ route('incidents.show', $incident) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">
                                            View Details →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($ispLink->incidents()->count() > 10)
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Showing 10 most recent incidents. Total: {{ $ispLink->incidents()->count() }}
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No incidents reported for this link</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Link Modal -->
    <div id="restoreModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-2xl bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">Restore ISP Link</h3>
                    <button onclick="closeRestoreModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="restoreLinkForm" method="POST" action="{{ route('isp.restore', $ispLink) }}">
                    @csrf

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            This will close all <span id="activeIncidentCount" class="font-semibold text-gray-900 dark:text-gray-100"></span> active incident(s) affecting this ISP link.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label for="restored_at" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Restored Date and Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="restored_at" name="restored_at" required
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-green-600 dark:focus:border-green-400 focus:ring-2 focus:ring-green-600/20 dark:focus:ring-green-400/20 bg-white dark:bg-gray-800 transition-all duration-300">
                    </div>

                    <div class="mb-4">
                        <label for="root_cause" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Root Cause <span class="text-red-500">*</span>
                        </label>
                        <textarea id="root_cause" name="root_cause" rows="4" required
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-green-600 dark:focus:border-green-400 focus:ring-2 focus:ring-green-600/20 dark:focus:ring-green-400/20 bg-white dark:bg-gray-800 transition-all duration-300"
                            placeholder="Enter the root cause for this ISP link outage..."></textarea>
                    </div>

                    <!-- Delay Reason Field (conditional) -->
                    <div id="delayReasonField" class="hidden mb-4">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-3 mb-2 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                        Some incidents have been open for more than 5 hours. Please explain the reason for the delay.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <label for="delay_reason" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason for Delay <span class="text-red-500">*</span>
                        </label>
                        <textarea id="delay_reason" name="delay_reason" rows="4"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-green-600 dark:focus:border-green-400 focus:ring-2 focus:ring-green-600/20 dark:focus:ring-green-400/20 bg-white dark:bg-gray-800 transition-all duration-300"
                            placeholder="Please provide a detailed explanation for why this outage took more than 5 hours to resolve..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeRestoreModal()"
                            class="px-6 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-heading font-medium hover:bg-gray-200 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-green-700 text-white font-heading font-semibold shadow-lg transition-all duration-300 hover:from-green-700 hover:to-green-800 hover:shadow-xl">
                            Restore Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Get active incidents data from the server (from both old and new systems)
        @php
            $allActiveIncidents = \App\Models\Incident::where(function($query) use ($ispLink) {
                $query->where('isp_link_id', $ispLink->id)
                      ->orWhereHas('ispLinks', function($q) use ($ispLink) {
                          $q->where('isp_links.id', $ispLink->id);
                      });
            })
            ->whereIn('status', ['Open', 'In Progress', 'Monitoring'])
            ->get();
        @endphp
        const activeIncidentsData = @json($allActiveIncidents->map(function($incident) {
            return [
                'id' => $incident->id,
                'started_at' => $incident->started_at ? $incident->started_at->toIso8601String() : null
            ];
        }));

        function openRestoreModal() {
            const modal = document.getElementById('restoreModal');
            const restoredAtInput = document.getElementById('restored_at');
            const incidentCountSpan = document.getElementById('activeIncidentCount');

            // Set incident count
            incidentCountSpan.textContent = activeIncidentsData.length;

            // Set default restored time to now
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            restoredAtInput.value = now.toISOString().slice(0, 16);

            // Update conditional fields
            updateConditionalFields();

            // Show modal
            modal.classList.remove('hidden');
        }

        function updateConditionalFields() {
            const restoredAtInput = document.getElementById('restored_at');
            const delayReasonField = document.getElementById('delayReasonField');
            const delayReasonInput = document.getElementById('delay_reason');

            // Check if any incident requires delay reason (duration > 5 hours)
            let requiresDelayReason = false;
            const restoredDate = new Date(restoredAtInput.value);

            for (const incident of activeIncidentsData) {
                if (incident.started_at) {
                    const startDate = new Date(incident.started_at);
                    const durationHours = (restoredDate - startDate) / (1000 * 60 * 60);
                    if (durationHours > 5) {
                        requiresDelayReason = true;
                        break;
                    }
                }
            }

            // Show/hide delay reason field
            if (requiresDelayReason) {
                delayReasonField.classList.remove('hidden');
                delayReasonInput.required = true;
            } else {
                delayReasonField.classList.add('hidden');
                delayReasonInput.required = false;
            }
        }

        function closeRestoreModal() {
            const modal = document.getElementById('restoreModal');
            modal.classList.add('hidden');
        }

        // Update conditional fields when restored_at changes
        document.addEventListener('DOMContentLoaded', function() {
            const restoredAtInput = document.getElementById('restored_at');
            if (restoredAtInput) {
                restoredAtInput.addEventListener('change', updateConditionalFields);
            }
        });

        // Close modal when clicking outside
        document.getElementById('restoreModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeRestoreModal();
            }
        });
    </script>
</div>
@endsection
