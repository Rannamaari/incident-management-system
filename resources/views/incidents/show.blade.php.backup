@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                    {{ $incident->incident_code }}
                </h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($incident->status === 'Open') bg-yellow-100 text-yellow-800
                    @elseif($incident->status === 'In Progress') bg-blue-100 text-blue-800
                    @elseif($incident->status === 'Monitoring') bg-purple-100 text-purple-800
                    @elseif($incident->status === 'Closed') bg-green-100 text-green-800
                    @endif">
                    {{ $incident->status }}
                </span>
            </div>
            <p class="text-lg text-gray-600 font-medium">{{ $incident->summary }}</p>
            <div class="flex items-center gap-4 mt-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium
                    @if($incident->severity === 'Critical') bg-red-100 text-red-800
                    @elseif($incident->severity === 'High') bg-orange-100 text-orange-800
                    @elseif($incident->severity === 'Medium') bg-yellow-100 text-yellow-800
                    @elseif($incident->severity === 'Low') bg-green-100 text-green-800
                    @endif">
                    {{ $incident->severity }} Priority
                </span>
                @if($incident->exceeded_sla)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                        SLA Breached
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                        SLA Met
                    </span>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 1200px;">

            <!-- Action Buttons -->
            <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                <!-- Left Side: Back Button -->
                <a href="{{ route('incidents.index') }}"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-all duration-200"
                    title="Back to List">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>

                <!-- Right Side: Action Icons -->
                <div class="flex items-center gap-2">
                    <!-- Copy Incident Text -->
                    <div x-data="{ copied: false }">
                        <button @click="async () => {
                            try {
                                const response = await fetch('{{ route('incidents.copy-text', $incident) }}', {
                                    credentials: 'same-origin',
                                    headers: { 'Accept': 'text/plain' }
                                });
                                if (!response.ok) throw new Error('Failed to fetch');
                                const text = await response.text();
                                if (text.includes('<!DOCTYPE') || text.includes('<html')) throw new Error('Session expired');
                                await navigator.clipboard.writeText(text);
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            } catch (err) {
                                console.error('Copy error:', err);
                            }
                        }"
                            :class="copied ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                            class="p-2 rounded-lg transition-all duration-200"
                            title="Copy Incident Text">
                            <svg x-show="!copied" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg x-show="copied" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Copy Image -->
                    <div x-data="{ copied: false }">
                        <button @click="async () => {
                            try {
                                await window.generateIncidentImage({{ $incident->id }});
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            } catch (err) {
                                console.error('Image generation error:', err);
                            }
                        }"
                            :class="copied ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                            class="p-2 rounded-lg transition-all duration-200"
                            title="Copy Image">
                            <svg x-show="!copied" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <svg x-show="copied" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>

                    @if(auth()->user()->canEditIncidents())
                        <!-- Edit Button -->
                        <a href="{{ route('incidents.edit', $incident) }}"
                            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200"
                            title="Edit Incident">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        @if($incident->status !== 'Closed')
                            <!-- Close Button -->
                            <button type="button" id="close-incident-btn"
                                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200"
                                title="Close Incident">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        @endif
                    @endif

                    @if(auth()->user()->canDeleteIncidents())
                        <!-- Delete Button -->
                        <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete incident {{ $incident->incident_code }}?\n\nThis will permanently delete:\n• The incident record\n• All incident logs\n• All action points\n• All related data\n\nThis action cannot be undone!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200"
                                title="Delete Incident">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- SLA and RCA Information Cards (Top) -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
                <!-- SLA Information -->
                <div class="rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm p-5 shadow-sm">
                    <h4 class="font-heading text-sm font-heading font-semibold text-gray-900 mb-3">SLA Information</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs font-heading font-medium text-gray-500">SLA Target</dt>
                            <dd class="text-sm font-heading font-semibold text-gray-900">{{ $incident->sla_minutes / 60 }} hours</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-heading font-medium text-gray-500">Status</dt>
                            <dd class="text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-heading font-medium {{ $incident->getSlaColorClass() }}">
                                    {{ $incident->getCurrentSlaStatus() }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- RCA Information -->
                <div class="rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm p-5 shadow-sm">
                    <h4 class="font-heading text-sm font-heading font-semibold text-gray-900 mb-3">RCA Status</h4>
                    <div class="space-y-2">
                        <div>
                            <dt class="text-xs font-heading font-medium text-gray-500">Required</dt>
                            <dd class="text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-heading font-medium {{ $incident->getRcaColorClass() }}">
                                    {{ $incident->getRcaStatus() }}
                                </span>
                            </dd>
                        </div>

                        @if($incident->rca)
                        <div>
                            <a href="{{ route('rcas.show', $incident->rca) }}"
                               class="inline-flex items-center gap-2 text-sm text-orange-600 hover:text-orange-700 font-medium">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                View RCA Document
                            </a>
                            <p class="text-xs text-gray-500 mt-1">{{ $incident->rca->rca_number }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm p-5 shadow-sm">
                    <h4 class="font-heading text-sm font-heading font-semibold text-gray-900 mb-3">Quick Stats</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-500">Created By</dt>
                            <dd class="text-sm font-heading font-medium text-gray-900">
                                @if($incident->creator)
                                    {{ $incident->creator->name }}
                                    <span class="text-xs text-gray-500">({{ $incident->created_at->timezone('Indian/Maldives')->format('M j, Y g:i A') }})</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-500">Last Updated By</dt>
                            <dd class="text-sm font-heading font-medium text-gray-900">
                                @if($incident->updater)
                                    {{ $incident->updater->name }}
                                    <span class="text-xs text-gray-500">({{ $incident->updated_at->diffForHumans() }})</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </dd>
                        </div>
                        @if($incident->logs->count() > 0)
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-500">Log Entries</dt>
                            <dd class="text-sm font-heading font-medium text-gray-900">{{ $incident->logs->count() }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Incident Details -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm shadow-lg mb-6">
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Incident Details</h3>
                            <p class="text-sm text-gray-600">Complete incident information</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Affected Services</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->affected_services }}</dd>
                        </div>

                        @if($incident->sites && $incident->sites->count() > 0)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-heading font-medium text-gray-500 mb-3">Affected Sites</dt>
                            <dd class="mt-1">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($incident->sites as $site)
                                        <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-blue-50/30 to-white p-4 hover:shadow-md transition-shadow">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex-1">
                                                    <h4 class="font-heading font-semibold text-gray-900 text-base">{{ $site->site_code }}</h4>
                                                    <p class="text-xs text-gray-600 mt-0.5">{{ $site->display_name }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-2 space-y-2">
                                                <div>
                                                    <span class="text-xs font-medium text-gray-500">Region:</span>
                                                    <span class="text-xs text-gray-900 ml-1">{{ $site->region->name ?? 'N/A' }} ({{ $site->region->code ?? 'N/A' }})</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-medium text-gray-500">Site Name:</span>
                                                    <span class="text-xs text-gray-900 ml-1">{{ $site->site_name ?? 'N/A' }}</span>
                                                </div>

                                                @php
                                                    // affected_technologies should be cast to array by IncidentSite pivot model
                                                    // But handle cases where it might still be a JSON string
                                                    $affectedTechs = $site->pivot->affected_technologies ?? [];

                                                    // Ensure it's an array
                                                    if (is_string($affectedTechs)) {
                                                        $affectedTechs = json_decode($affectedTechs, true) ?? [];
                                                    }

                                                    // Final safety check
                                                    if (!is_array($affectedTechs)) {
                                                        $affectedTechs = [];
                                                    }
                                                @endphp

                                                @if(!empty($affectedTechs) && is_array($affectedTechs))
                                                <div class="mt-2 pt-2 border-t border-gray-200">
                                                    <span class="text-xs font-medium text-gray-500 block mb-1.5">Affected Technologies:</span>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach($affectedTechs as $tech)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                                                @if($tech === '5G') bg-purple-100 text-purple-800
                                                                @elseif($tech === '4G') bg-blue-100 text-blue-800
                                                                @elseif($tech === '3G') bg-green-100 text-green-800
                                                                @elseif($tech === '2G') bg-gray-100 text-gray-800
                                                                @endif">
                                                                {{ $tech }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </dd>
                        </div>
                        @endif

                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->category ?? 'Not specified' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Outage Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->outage_category ?? 'Not specified' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Fault Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->fault_type ?? 'Not specified' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Resolution Team</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->resolution_team ?? 'Not assigned' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Started At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->started_at->timezone('Indian/Maldives')->format('M j, Y g:i A') }}</dd>
                        </div>

                        @if($incident->resolved_at)
                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Resolved At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->resolved_at->timezone('Indian/Maldives')->format('M j, Y g:i A') }}</dd>
                        </div>
                        @endif

                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Duration</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($incident->duration_hms)
                                    {{ $incident->duration_hms }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>

                        @if($incident->travel_time || $incident->work_time)
                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Travel Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->travel_time ? $incident->travel_time . ' minutes' : 'Not specified' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Work Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->work_time ? $incident->work_time . ' minutes' : 'Not specified' }}</dd>
                        </div>
                        @endif

                        @if($incident->root_cause)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-heading font-medium text-gray-500">Root Cause</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->root_cause }}</dd>
                        </div>
                        @endif

                        @if($incident->delay_reason)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-heading font-medium text-gray-500">Reason for Delay</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->delay_reason }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Timeline Updates -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm shadow-lg">
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-indigo-50/80 to-white/60 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Timeline Updates</h3>
                            <p class="text-sm text-gray-600">Add updates and notes about this incident</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Add Timeline Update Form (only for non-closed incidents) -->
                    @if($incident->status !== 'Closed')
                        <form action="{{ route('incidents.timeline.add', $incident) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="rounded-xl bg-gradient-to-br from-indigo-50/50 to-blue-50/30 border border-indigo-100 p-4">
                                <label for="timeline_note" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Add Update
                                </label>
                                <textarea name="timeline_note" id="timeline_note" rows="3" required
                                          placeholder="Enter your update about this incident..."
                                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('timeline_note') border-red-500 @enderror"></textarea>
                                @error('timeline_note')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="mt-3 flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-700 px-4 py-2 text-white font-heading font-medium shadow-sm hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="mb-6 rounded-xl bg-gray-50 border border-gray-200 p-4">
                            <p class="text-sm text-gray-600 flex items-center gap-2">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                This incident is closed. No further updates can be added.
                            </p>
                        </div>
                    @endif

                    <!-- Display Timeline Updates -->
                    @if($incident->timeline && count($incident->timeline) > 0)
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach(array_reverse($incident->timeline) as $index => $entry)
                                    <li>
                                        <div class="relative pb-8">
                                            @if($index < count($incident->timeline) - 1)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gradient-to-b from-indigo-200 to-transparent" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center ring-8 ring-white shadow-md">
                                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-heading font-medium text-indigo-600 mb-1">{{ $entry['user_name'] }}</p>
                                                        <p class="text-sm text-gray-900 bg-white/50 rounded-lg p-3 border border-gray-100">{{ $entry['note'] }}</p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $entry['timestamp'] }}">
                                                            {{ \Carbon\Carbon::parse($entry['timestamp'])->timezone('Indian/Maldives')->format('M j, Y') }}<br>
                                                            {{ \Carbon\Carbon::parse($entry['timestamp'])->timezone('Indian/Maldives')->format('g:i A') }}
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No timeline updates yet</p>
                            @if($incident->status !== 'Closed')
                                <p class="text-xs text-gray-400">Add your first update above</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Incident Timeline/Logs -->
            @if($incident->logs->count() > 0)
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm shadow-lg">
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-600 to-green-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Incident Timeline</h3>
                            <p class="text-sm text-gray-600">{{ $incident->logs->count() }} log entries</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($incident->logs as $log)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">{{ $log->note }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $log->occurred_at->toISOString() }}">
                                                        {{ $log->occurred_at->timezone('Indian/Maldives')->format('M j, Y g:i A') }}
                                                    </time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Activity Audit Trail -->
            @if($incident->activityLogs->count() > 0)
            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm shadow-lg">
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-purple-50/80 to-white/60 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-600 to-purple-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-semibold text-gray-900">Audit Trail</h3>
                            <p class="text-sm text-gray-600">Complete history of all changes made to this incident</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="space-y-4">
                            @foreach($incident->activityLogs as $log)
                                <li>
                                    <div class="relative flex gap-x-4 rounded-xl p-4 transition-all duration-200
                                        @if($log->action === 'created') bg-green-50/50 hover:bg-green-50
                                        @elseif($log->action === 'updated') bg-blue-50/50 hover:bg-blue-50
                                        @elseif($log->action === 'deleted') bg-red-50/50 hover:bg-red-50
                                        @else bg-gray-50/50 hover:bg-gray-50
                                        @endif">

                                        <!-- Icon -->
                                        <div class="relative flex h-8 w-8 flex-none items-center justify-center rounded-full
                                            @if($log->action === 'created') bg-green-100
                                            @elseif($log->action === 'updated') bg-blue-100
                                            @elseif($log->action === 'deleted') bg-red-100
                                            @else bg-gray-100
                                            @endif">
                                            <div class="
                                                @if($log->action === 'created') text-green-600
                                                @elseif($log->action === 'updated') text-blue-600
                                                @elseif($log->action === 'deleted') text-red-600
                                                @else text-gray-600
                                                @endif">
                                                {!! $log->action_icon !!}
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-auto">
                                            <div class="flex items-start justify-between gap-x-4">
                                                <div class="flex-1">
                                                    <p class="text-sm font-heading font-semibold text-gray-900">
                                                        {{ $log->user ? $log->user->name : 'System' }}
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium ml-2 {{ $log->action_color_class }}">
                                                            {{ ucfirst($log->action) }}
                                                        </span>
                                                    </p>

                                                    <p class="mt-1 text-sm text-gray-700">
                                                        {{ $log->description }}
                                                    </p>

                                                    @if($log->field_name && $log->action === 'updated')
                                                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                                            <div class="rounded-lg bg-white/80 border border-gray-200 p-2">
                                                                <span class="font-heading font-medium text-gray-500">Previous:</span>
                                                                <span class="text-gray-900 ml-1">{{ $log->old_value ?? '-' }}</span>
                                                            </div>
                                                            <div class="rounded-lg bg-white/80 border border-gray-200 p-2">
                                                                <span class="font-heading font-medium text-gray-500">New:</span>
                                                                <span class="text-gray-900 ml-1">{{ $log->new_value ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <time datetime="{{ $log->created_at->toISOString() }}"
                                                    class="flex-none text-xs text-gray-500 whitespace-nowrap">
                                                    {{ $log->created_at->timezone('Indian/Maldives')->format('M j, Y') }}<br>
                                                    {{ $log->created_at->timezone('Indian/Maldives')->format('g:i A') }}
                                                </time>
                                            </div>

                                            @if($log->ip_address)
                                                <p class="mt-2 text-xs text-gray-500">
                                                    IP: {{ $log->ip_address }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Close Incident Modal -->
    <div id="close-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form action="{{ route('incidents.update', $incident) }}" method="POST" id="close-incident-form">
                    @csrf
                    @method('PUT')

                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="font-heading text-lg leading-6 font-heading font-medium text-gray-900" id="modal-title">
                                Close Incident
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Please provide the required information to close this incident.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        <!-- Copy current values as hidden fields -->
                        <input type="hidden" name="summary" value="{{ $incident->summary }}">
                        <input type="hidden" name="affected_services" value="{{ $incident->affected_services }}">
                        <input type="hidden" name="started_at" value="{{ $incident->started_at->format('Y-m-d H:i:s') }}">
                        <input type="hidden" name="severity" value="{{ $incident->severity }}">
                        <input type="hidden" name="status" value="Closed">

                        <!-- Resolved At -->
                        <div>
                            <label for="modal_resolved_at" class="block text-sm font-heading font-medium text-gray-700">
                                Resolved At <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="resolved_at" id="modal_resolved_at" required
                                   value="{{ old('resolved_at', $incident->resolved_at ? $incident->resolved_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('resolved_at') border-red-500 @enderror">
                            @error('resolved_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Root Cause -->
                        <div>
                            <label for="modal_root_cause" class="block text-sm font-heading font-medium text-gray-700">
                                Root Cause <span class="text-red-500">*</span>
                            </label>
                            <textarea name="root_cause" id="modal_root_cause" rows="4" required
                                      placeholder="Enter the root cause of this incident..."
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('root_cause') border-red-500 @enderror">{{ old('root_cause', $incident->root_cause) }}</textarea>
                            @error('root_cause')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Root cause is required when closing an incident</p>
                        </div>

                        <!-- Conditional Fields based on duration and severity -->
                        @php
                            $showTravelWork = in_array($incident->severity, ['Medium', 'High', 'Critical']);
                        @endphp
                        <div id="conditional-fields" class="space-y-4">
                            <!-- Travel Time (for Medium/High/Critical) -->
                            <div id="travel-time-field" class="{{ $showTravelWork ? '' : 'hidden' }}">
                                <label for="modal_travel_time" class="block text-sm font-heading font-medium text-gray-700">
                                    Travel Time (minutes) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="travel_time" id="modal_travel_time" min="0"
                                       value="{{ old('travel_time', $incident->travel_time) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('travel_time') border-red-500 @enderror">
                                @error('travel_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Work Time (for Medium/High/Critical) -->
                            <div id="work-time-field" class="{{ $showTravelWork ? '' : 'hidden' }}">
                                <label for="modal_work_time" class="block text-sm font-heading font-medium text-gray-700">
                                    Work Time (minutes) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="work_time" id="modal_work_time" min="0"
                                       value="{{ old('work_time', $incident->work_time) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('work_time') border-red-500 @enderror">
                                @error('work_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Delay Reason (for duration > 5 hours) -->
                            @php
                                $currentDurationHours = $incident->started_at->diffInHours(now());
                                $showDelayReason = $currentDurationHours > 5;
                            @endphp
                            <div id="delay-reason-field" class="{{ $showDelayReason ? '' : 'hidden' }}">
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-2">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                This incident has been open for more than 5 hours. Please explain the reason for the delay.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <label for="modal_delay_reason" class="block text-sm font-heading font-medium text-gray-700">
                                    Reason for Delay <span class="text-red-500">*</span>
                                </label>
                                <textarea name="delay_reason" id="modal_delay_reason" rows="4"
                                          placeholder="Please provide a detailed explanation for why this incident took more than 5 hours to resolve..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('delay_reason') border-red-500 @enderror">{{ old('delay_reason', $incident->delay_reason) }}</textarea>
                                @error('delay_reason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">This field is required for incidents with duration exceeding 5 hours.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                            Close Incident
                        </button>
                        <button type="button" id="cancel-close"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-heading font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Close modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const closeBtn = document.getElementById('close-incident-btn');
            const modal = document.getElementById('close-modal');
            const cancelBtn = document.getElementById('cancel-close');
            const resolvedAtInput = document.getElementById('modal_resolved_at');

            // Auto-open modal if there are validation errors
            @if($errors->has('delay_reason') || $errors->has('resolved_at') || $errors->has('travel_time') || $errors->has('work_time'))
                modal.classList.remove('hidden');
                updateConditionalFields();
            @endif

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    // Ensure resolved_at has a default value when modal opens
                    if (!resolvedAtInput.value) {
                        const now = new Date();
                        const year = now.getFullYear();
                        const month = String(now.getMonth() + 1).padStart(2, '0');
                        const day = String(now.getDate()).padStart(2, '0');
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        resolvedAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                    }
                    modal.classList.remove('hidden');
                    updateConditionalFields();
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            }

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });

            // Update conditional fields when resolved_at changes
            if (resolvedAtInput) {
                resolvedAtInput.addEventListener('change', updateConditionalFields);
            }

            function updateConditionalFields() {
                const startedAt = new Date('{{ $incident->started_at->toISOString() }}');

                // Handle datetime-local input format properly
                let resolvedAtValue = resolvedAtInput.value;
                let resolvedAt;

                // If no value, set default to current time
                if (!resolvedAtValue) {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    resolvedAtValue = `${year}-${month}-${day}T${hours}:${minutes}`;
                    resolvedAtInput.value = resolvedAtValue;
                }

                // Convert datetime-local format to proper Date
                resolvedAt = new Date(resolvedAtValue);

                const severity = '{{ $incident->severity }}';
                const durationHours = (resolvedAt - startedAt) / (1000 * 60 * 60);

                // Show/hide delay reason field
                const delayField = document.getElementById('delay-reason-field');
                const delayInput = document.getElementById('modal_delay_reason');

                console.log('Duration Hours:', durationHours);
                console.log('Delay reason required:', durationHours > 5);

                if (durationHours > 5) {
                    delayField.classList.remove('hidden');
                    delayInput.required = true;
                    console.log('Showing delay reason field');
                } else {
                    delayField.classList.add('hidden');
                    delayInput.required = false;
                    console.log('Hiding delay reason field');
                }

                // Show/hide travel/work time fields
                const travelField = document.getElementById('travel-time-field');
                const workField = document.getElementById('work-time-field');
                const travelInput = document.getElementById('modal_travel_time');
                const workInput = document.getElementById('modal_work_time');

                if (['Medium', 'High', 'Critical'].includes(severity)) {
                    travelField.classList.remove('hidden');
                    workField.classList.remove('hidden');
                    travelInput.required = true;
                    workInput.required = true;
                } else {
                    travelField.classList.add('hidden');
                    workField.classList.add('hidden');
                    travelInput.required = false;
                    workInput.required = false;
                }
            }
        });
    </script>

    <!-- HTML2Canvas Library for Image Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        window.generateIncidentImage = async function(incidentId) {
            try {
                // Fetch incident data
                const response = await fetch(`/incidents/${incidentId}/copy-text`);
                if (!response.ok) throw new Error('Failed to fetch incident data');
                const incidentText = await response.text();

                // Create a temporary container for the incident display
                const container = document.createElement('div');
                container.style.cssText = `
                    position: fixed;
                    left: -9999px;
                    top: 0;
                    width: 800px;
                    background: #cb2c30;
                    padding: 6px;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    border-radius: 12px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                `;

                // Create content div with white background
                const contentDiv = document.createElement('div');
                contentDiv.style.cssText = `
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    color: #1a202c;
                    line-height: 1.6;
                `;

                // Extract incident code from the first line
                const lines = incidentText.split('\n');
                const incidentCodeMatch = lines[0].match(/INCIDENT\s+(.+)/);
                const incidentCode = incidentCodeMatch ? incidentCodeMatch[1].trim() : '';

                // Remove the first line (incident code) from the content
                const contentWithoutCode = lines.slice(2).join('\n'); // Skip first line and empty line

                // Create professional header
                const header = `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 3px solid #cb2c30;">
                        <div style="font-size: 24px; font-weight: 700; color: #1a202c; letter-spacing: 0.5px;">INCIDENT SUMMARY</div>
                        <div style="font-size: 14px; color: #718096; font-weight: 500;">#${incidentCode}</div>
                    </div>
                `;

                // Convert text to HTML with proper formatting
                const formattedHTML = header + contentWithoutCode
                    .replace(/\*([^*]+)\*/g, '<strong style="color: #2d3748;">$1</strong>')
                    .replace(/━━━━━━━━━━━━━━━━━━/g, '<hr style="border: none; border-top: 2px solid #e2e8f0; margin: 15px 0;">')
                    .split('\n')
                    .map(line => {
                        if (line.trim() === '') return '<div style="height: 10px;"></div>';
                        if (line.match(/^\d+\./)) {
                            return `<div style="margin-left: 20px; color: #4a5568;">${line}</div>`;
                        }
                        return `<div style="margin-bottom: 5px;">${line}</div>`;
                    })
                    .join('');

                // Get current date and time
                const now = new Date();
                const generatedTime = now.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });

                // Add footer
                const footer = `
                    <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #e2e8f0; text-align: center; color: #718096; font-size: 12px;">
                        <div style="font-weight: 600; margin-bottom: 4px;">Generated by Incident Management System</div>
                        <div>${generatedTime}</div>
                    </div>
                `;

                contentDiv.innerHTML = formattedHTML + footer;
                container.appendChild(contentDiv);
                document.body.appendChild(container);

                // Generate image using html2canvas
                const canvas = await html2canvas(container, {
                    backgroundColor: null,
                    scale: 2,
                    logging: false,
                    useCORS: true
                });

                // Remove temporary container
                document.body.removeChild(container);

                // Convert canvas to blob and copy to clipboard
                canvas.toBlob(async (blob) => {
                    try {
                        // Copy image to clipboard
                        const item = new ClipboardItem({ 'image/png': blob });
                        await navigator.clipboard.write([item]);
                    } catch (clipboardError) {
                        console.error('Clipboard error:', clipboardError);

                        // Fallback: Download the image if clipboard fails
                        try {
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `incident-${incidentId}.png`;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        } catch (downloadError) {
                            console.error('Download error:', downloadError);
                            throw new Error('Failed to copy to clipboard or download image');
                        }
                    }
                }, 'image/png');

            } catch (error) {
                console.error('Error generating image:', error);
                throw error;
            }
        };
    </script>
@endsection
