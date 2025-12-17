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
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/30 transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Close Incident
                        </button>
                    @endif
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
                        @elseif($incident->rca_file_path && $incident->hasRcaFile())
                        <div>
                            <a href="{{ route('incidents.download-rca', $incident) }}"
                               class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download RCA File
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm p-5 shadow-sm">
                    <h4 class="font-heading text-sm font-heading font-semibold text-gray-900 mb-3">Quick Stats</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-500">Created</dt>
                            <dd class="text-sm font-heading font-medium text-gray-900">{{ $incident->created_at->format('M j, Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-500">Updated</dt>
                            <dd class="text-sm font-heading font-medium text-gray-900">{{ $incident->updated_at->diffForHumans() }}</dd>
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
                                @if($incident->duration_minutes)
                                    {{ $incident->duration_hms }} ({{ $incident->duration_minutes }} minutes)
                                @else
                                    {{ $incident->resolved_at ? $incident->started_at->diffForHumans($incident->resolved_at, true) : 'Ongoing' }}
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

                        @if($incident->pir_rca_no)
                        <div>
                            <dt class="text-sm font-heading font-medium text-gray-500">PIR/RCA No</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->pir_rca_no }}</dd>
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
