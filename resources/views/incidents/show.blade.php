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
            <div class="flex flex-wrap gap-3 mb-6">
                <a href="{{ route('incidents.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-700 px-5 py-2.5 text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>

                @if(auth()->user()->canEditIncidents())
                    <a href="{{ route('incidents.edit', $incident) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Incident
                    </a>

                    @if($incident->status !== 'Closed')
                        <button type="button" id="close-incident-btn"
                            class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-2.5 text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500/30 transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Close Incident
                        </button>
                    @endif
                @endif

                @if(auth()->user()->canDeleteIncidents())
                    <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete incident {{ $incident->incident_code }}?\n\nThis will permanently delete:\n• The incident record\n• All incident logs\n• All action points\n• All related data\n\nThis action cannot be undone!');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/30 transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Incident
                        </button>
                    </form>
                @endif
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
                                    <span class="text-xs text-gray-500">({{ $incident->created_at->format('M j, Y g:i A') }})</span>
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
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->started_at->format('M j, Y g:i A') }}</dd>
                        </div>

                        @if($incident->resolved_at)
                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">Resolved At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->resolved_at->format('M j, Y g:i A') }}</dd>
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
                                                        {{ $log->occurred_at->format('M j, Y g:i A') }}
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
                                                    {{ $log->created_at->format('M j, Y') }}<br>
                                                    {{ $log->created_at->format('g:i A') }}
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
                                   value="{{ $incident->resolved_at ? $incident->resolved_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Conditional Fields based on duration and severity -->
                        <div id="conditional-fields" class="space-y-4">
                            <!-- Travel Time (for Medium/High/Critical) -->
                            <div id="travel-time-field" class="hidden">
                                <label for="modal_travel_time" class="block text-sm font-heading font-medium text-gray-700">
                                    Travel Time (minutes) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="travel_time" id="modal_travel_time" min="0"
                                       value="{{ $incident->travel_time }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Work Time (for Medium/High/Critical) -->
                            <div id="work-time-field" class="hidden">
                                <label for="modal_work_time" class="block text-sm font-heading font-medium text-gray-700">
                                    Work Time (minutes) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="work_time" id="modal_work_time" min="0"
                                       value="{{ $incident->work_time }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Delay Reason (for duration > 5 hours) -->
                            <div id="delay-reason-field" class="hidden">
                                <label for="modal_delay_reason" class="block text-sm font-heading font-medium text-gray-700">
                                    Reason for Delay <span class="text-red-500">*</span>
                                </label>
                                <textarea name="delay_reason" id="modal_delay_reason" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ $incident->delay_reason }}</textarea>
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

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
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
                const resolvedAtValue = resolvedAtInput.value;
                let resolvedAt;

                if (!resolvedAtValue) {
                    return;
                }

                // Convert datetime-local format to proper Date
                resolvedAt = new Date(resolvedAtValue);

                const severity = '{{ $incident->severity }}';
                const durationHours = (resolvedAt - startedAt) / (1000 * 60 * 60);

                // Show/hide delay reason field
                const delayField = document.getElementById('delay-reason-field');
                const delayInput = document.getElementById('modal_delay_reason');
                if (durationHours > 5) {
                    delayField.classList.remove('hidden');
                    delayInput.required = true;
                } else {
                    delayField.classList.add('hidden');
                    delayInput.required = false;
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
@endsection
