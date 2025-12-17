@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Create New Incident
            </h2>
            <p class="mt-2 text-lg text-gray-600 font-medium">Report and track a new system incident</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('incidents.index') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-2.5 text-white shadow-lg hover:from-gray-800 hover:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 1200px;">

            <!-- Guidance and SLA Summary (Dismissible) -->
            <div id="help-section" class="mb-6 space-y-4">
                <!-- Guidance -->
                <div class="rounded-2xl border border-blue-100 bg-blue-50/50 backdrop-blur-sm p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h4 class="font-heading mb-3 flex items-center gap-2 text-sm font-heading font-semibold uppercase tracking-wide text-blue-900">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M12 19a7 7 0 100-14 7 7 0 000 14z" />
                                </svg>
                                Guidance
                            </h4>
                            <ul class="space-y-2 text-sm text-blue-800">
                                <li>• High/Critical severity incidents require an RCA document to close.</li>
                                <li>• SLA times by severity: High/Critical = 2 hours, Medium = 6 hours, Low = 12 hours.</li>
                                <li>• Leave duration empty to auto-calculate from start and resolved times.</li>
                            </ul>
                        </div>
                        <button type="button" onclick="dismissHelpSection()"
                            class="flex-shrink-0 text-blue-400 hover:text-blue-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- SLA Summary -->
                <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur-sm p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h4 class="font-heading mb-3 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">SLA Summary</h4>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div class="rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 p-3 border border-green-200/30">
                                    <dt class="text-green-800 font-medium text-sm">Low</dt>
                                    <dd class="font-bold text-green-900 text-lg">12h</dd>
                                </div>
                                <div class="rounded-xl bg-gradient-to-br from-amber-50 to-yellow-50 p-3 border border-amber-200/30">
                                    <dt class="text-amber-800 font-medium text-sm">Medium</dt>
                                    <dd class="font-bold text-amber-900 text-lg">6h</dd>
                                </div>
                                <div class="rounded-xl bg-gradient-to-br from-red-50 to-rose-50 p-3 border border-red-200/30">
                                    <dt class="text-red-800 font-medium text-sm">High</dt>
                                    <dd class="font-bold text-red-900 text-lg">2h</dd>
                                </div>
                                <div class="rounded-xl bg-gradient-to-br from-red-50 to-rose-50 p-3 border border-red-200/30">
                                    <dt class="text-red-800 font-medium text-sm">Critical</dt>
                                    <dd class="font-bold text-red-900 text-lg">2h</dd>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="dismissHelpSection()"
                            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Show Help Button (appears when help is dismissed) -->
                <div id="show-help-btn-container" class="hidden">
                    <button type="button" onclick="showHelpSection()"
                        class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-all duration-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M12 19a7 7 0 100-14 7 7 0 000 14z" />
                        </svg>
                        Show Guidance & SLA Info
                    </button>
                </div>
            </div>

            <!-- Main Form -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm shadow-lg">
                <!-- Section Header -->
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Incident Details</h3>
                            <p class="text-sm text-gray-600">Fill in the fields below</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data"
                        class="space-y-10">
                        @csrf

                        <!-- Group 1: Basics -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Basic Info</h4>
                            <div class="space-y-4">

                                <!-- Summary -->
                                <div>
                                    <label for="summary" class="block text-sm font-heading font-medium text-gray-700 mb-2">Outage
                                        Details (Incident Summary) <span class="text-red-500">*</span></label>
                                    <textarea name="summary" id="summary" rows="4" maxlength="1000"
                                        placeholder="Provide detailed description of the incident..."
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 resize-y @error('summary') border-red-300 @enderror"
                                        oninput="updateCharCount('summary', 1000)">{{ old('summary') }}</textarea>
                                    <div class="mt-1 flex justify-between">
                                        <div>@error('summary') <span class="text-sm text-red-600">{{ $message }}</span> @enderror</div>
                                        <div class="text-xs text-gray-500">
                                            <span id="summary-count">{{ strlen(old('summary', '')) }}</span>/1000 characters
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- Outage Category -->
                                    <div>
                                        <label for="outage_category_id"
                                            class="block text-sm font-heading font-medium text-gray-700 mb-2">Outage Category <span
                                                class="text-red-500">*</span></label>
                                        <select name="outage_category_id" id="outage_category_id"
                                            onchange="toggleNewInput('outage_category')"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('outage_category_id') border-red-300 @enderror">
                                            <option value="">Select Outage Category</option>
                                            @foreach($outageCategories as $outageCategory)
                                                <option value="{{ $outageCategory->id }}" {{ old('outage_category_id') == $outageCategory->id ? 'selected' : '' }}>
                                                    {{ $outageCategory->name }}
                                                </option>
                                            @endforeach
                                            <option value="new">Add new...</option>
                                        </select>
                                        @error('outage_category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                                        <!-- New Outage Category Input -->
                                        <div id="new_outage_category_input" class="mt-2" style="display: none;">
                                            <input type="text" name="new_outage_category_name" id="new_outage_category_name"
                                                placeholder="Enter new outage category name"
                                                value="{{ old('new_outage_category_name') }}"
                                                class="w-full rounded-xl border border-blue-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-blue-50/50 transition-all duration-300">
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <label for="category_id" class="block text-sm font-heading font-medium text-gray-700 mb-2">Category
                                            <span class="text-red-500">*</span></label>
                                        <select name="category_id" id="category_id"
                                            onchange="toggleNewInput('category')"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('category_id') border-red-300 @enderror">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                            <option value="new">Add new...</option>
                                        </select>
                                        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                                        <!-- New Category Input -->
                                        <div id="new_category_input" class="mt-2" style="display: none;">
                                            <input type="text" name="new_category_name" id="new_category_name"
                                                placeholder="Enter new category name"
                                                value="{{ old('new_category_name') }}"
                                                class="w-full rounded-xl border border-blue-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-blue-50/50 transition-all duration-300">
                                        </div>
                                    </div>
                                </div>

                                <!-- Affected Services -->
                                <div>
                                    <label class="block text-sm font-heading font-medium text-gray-700 mb-3">Affected Systems/Services
                                        <span class="text-red-500">*</span></label>
                                    <p class="text-xs text-gray-500 mb-3">Select one or more affected systems/services</p>
                                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                                        @php
                                            $affectedServicesOptions = ['Cell', 'Single FBB', 'Single Site', 'Multiple Site', 'P2P', 'ILL', 'SIP', 'IPTV', 'Peering', 'Mobile Data', 'Others'];
                                            $oldValues = old('affected_services', []);
                                            if (is_string($oldValues)) {
                                                $oldValues = explode(',', $oldValues);
                                            }
                                        @endphp
                                        @foreach($affectedServicesOptions as $option)
                                            <div class="flex items-center">
                                                <input type="checkbox"
                                                       name="affected_services[]"
                                                       id="affected_services_{{ str_replace(' ', '_', strtolower($option)) }}"
                                                       value="{{ $option }}"
                                                       {{ in_array($option, $oldValues) ? 'checked' : '' }}
                                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2 transition-all duration-200"
                                                       onchange="toggleOthersInput()">
                                                <label for="affected_services_{{ str_replace(' ', '_', strtolower($option)) }}"
                                                       class="ml-2 text-sm font-heading font-medium text-gray-700 cursor-pointer hover:text-blue-600 transition-colors">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Others Input Field -->
                                    <div id="affected_services_others_input" class="mt-3" style="display: none;">
                                        <label for="affected_services_others_text" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                            Please specify other affected services
                                        </label>
                                        <input type="text"
                                               name="affected_services_others_text"
                                               id="affected_services_others_text"
                                               placeholder="Enter other affected services..."
                                               value="{{ old('affected_services_others_text') }}"
                                               class="w-full rounded-xl border border-blue-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-blue-50/50 transition-all duration-300">
                                    </div>

                                    <input type="hidden" name="affected_services_validation" value="1">
                                    @error('affected_services') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    @error('affected_services.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    @error('affected_services_others_text') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-heading font-medium text-gray-700 mb-2">Incident
                                            Status <span class="text-red-500">*</span></label>
                                        <select name="status" id="status"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('status') border-red-300 @enderror">
                                            @foreach(\App\Models\Incident::STATUSES as $status)
                                                <option value="{{ $status }}" {{ old('status', 'Open') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Severity -->
                                    <div>
                                        <label for="severity" class="block text-sm font-heading font-medium text-gray-700 mb-2">Severity
                                            Level <span class="text-red-500">*</span></label>
                                        <select name="severity" id="severity"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('severity') border-red-300 @enderror">
                                            @foreach(\App\Models\Incident::SEVERITIES as $severity)
                                                <option value="{{ $severity }}" {{ old('severity', 'Low') === $severity ? 'selected' : '' }}>{{ $severity }}</option>
                                            @endforeach
                                        </select>
                                        <p id="slaHint" class="mt-2 text-sm text-gray-600">SLA is derived from Severity.</p>
                                        @error('severity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Group 2: Timing -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Timing</h4>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Started At -->
                                <div>
                                    <label for="started_at" class="block text-sm font-heading font-medium text-gray-700 mb-2">Start
                                        Date and Time <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="started_at" id="started_at"
                                        value="{{ old('started_at') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('started_at') border-red-300 @enderror">
                                    @error('started_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Enter the actual time when the incident started</p>
                                </div>

                                <!-- Resolved At (Only shown when status is Closed) -->
                                <div id="resolved-at-field" style="display: none;">
                                    <label for="resolved_at" class="block text-sm font-heading font-medium text-gray-700 mb-2">Date
                                        and Time Resolved <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="resolved_at" id="resolved_at"
                                        value="{{ old('resolved_at', now()->format('Y-m-d\TH:i')) }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('resolved_at') border-red-300 @enderror">
                                    @error('resolved_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Manual Duration -->
                                <div class="sm:col-span-2">
                                    <label for="duration_minutes"
                                        class="block text-sm font-heading font-medium text-gray-700 mb-2">Manual Duration (minutes,
                                        optional)</label>
                                    <input type="number" name="duration_minutes" id="duration_minutes" min="0"
                                        value="{{ old('duration_minutes') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('duration_minutes') border-red-300 @enderror">
                                    <p class="mt-2 text-sm text-gray-500">Leave blank to auto-calc from Start/Resolved</p>
                                    @error('duration_minutes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Group 3: Fault & Notes -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Fault & Notes</h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- Fault Type -->
                                    <div>
                                        <label for="fault_type_id"
                                            class="block text-sm font-heading font-medium text-gray-700 mb-2">Fault/Issue Type</label>
                                        <select name="fault_type_id" id="fault_type_id"
                                            onchange="toggleNewInput('fault_type')"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('fault_type_id') border-red-300 @enderror">
                                            <option value="">Select Fault Type</option>
                                            @foreach($faultTypes as $faultType)
                                                <option value="{{ $faultType->id }}" {{ old('fault_type_id') == $faultType->id ? 'selected' : '' }}>
                                                    {{ $faultType->name }}
                                                </option>
                                            @endforeach
                                            <option value="new">Add new...</option>
                                        </select>
                                        @error('fault_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                                        <!-- New Fault Type Input -->
                                        <div id="new_fault_type_input" class="mt-2" style="display: none;">
                                            <input type="text" name="new_fault_type_name" id="new_fault_type_name"
                                                placeholder="Enter new fault type name"
                                                value="{{ old('new_fault_type_name') }}"
                                                class="w-full rounded-xl border border-blue-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-blue-50/50 transition-all duration-300">
                                        </div>
                                    </div>

                                    <!-- Resolution Team -->
                                    <div>
                                        <label for="resolution_team_id"
                                            class="block text-sm font-heading font-medium text-gray-700 mb-2">Resolution Team</label>
                                        <select name="resolution_team_id" id="resolution_team_id"
                                            onchange="toggleNewInput('resolution_team')"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('resolution_team_id') border-red-300 @enderror">
                                            <option value="">Select Resolution Team</option>
                                            @foreach($resolutionTeams as $resolutionTeam)
                                                <option value="{{ $resolutionTeam->id }}" {{ old('resolution_team_id') == $resolutionTeam->id ? 'selected' : '' }}>
                                                    {{ $resolutionTeam->name }}
                                                </option>
                                            @endforeach
                                            <option value="new">Add new...</option>
                                        </select>
                                        @error('resolution_team_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                                        <!-- New Resolution Team Input -->
                                        <div id="new_resolution_team_input" class="mt-2" style="display: none;">
                                            <input type="text" name="new_resolution_team_name" id="new_resolution_team_name"
                                                placeholder="Enter new resolution team name"
                                                value="{{ old('new_resolution_team_name') }}"
                                                class="w-full rounded-xl border border-blue-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-blue-50/50 transition-all duration-300">
                                        </div>
                                    </div>
                                </div>

                                <!-- Root Cause -->
                                <div>
                                    <label for="root_cause" class="block text-sm font-heading font-medium text-gray-700 mb-2">Root Cause</label>
                                    <textarea name="root_cause" id="root_cause" rows="4"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('root_cause') border-red-300 @enderror">{{ old('root_cause') }}</textarea>
                                    @error('root_cause') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Delay Reason -->
                                <div>
                                    <label for="delay_reason" class="block text-sm font-heading font-medium text-gray-700 mb-2">Reason for Delay</label>
                                    <textarea name="delay_reason" id="delay_reason" rows="4"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('delay_reason') border-red-300 @enderror">{{ old('delay_reason') }}</textarea>
                                    @error('delay_reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Group 4: Travel & Work -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Travel & Work</h4>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="journey_started_at"
                                        class="block text-sm font-heading font-medium text-gray-700 mb-2">Journey Start Time</label>
                                    <input type="datetime-local" name="journey_started_at" id="journey_started_at"
                                        value="{{ old('journey_started_at') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                                </div>
                                <div>
                                    <label for="island_arrival_at"
                                        class="block text-sm font-heading font-medium text-gray-700 mb-2">Island Arrival Time</label>
                                    <input type="datetime-local" name="island_arrival_at" id="island_arrival_at"
                                        value="{{ old('island_arrival_at') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                                </div>
                                <div>
                                    <label for="work_started_at"
                                        class="block text-sm font-heading font-medium text-gray-700 mb-2">Work/Repair Start Time</label>
                                    <input type="datetime-local" name="work_started_at" id="work_started_at"
                                        value="{{ old('work_started_at') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                                </div>
                                <div>
                                    <label for="work_completed_at"
                                        class="block text-sm font-heading font-medium text-gray-700 mb-2">Repair Completion Time</label>
                                    <input type="datetime-local" name="work_completed_at" id="work_completed_at"
                                        value="{{ old('work_completed_at') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                                </div>

                                <div>
                                    <label for="travel_time"
                                        class="block text-sm font-heading font-medium text-gray-700 mb-2">Travel Time (minutes)</label>
                                    <input type="number" name="travel_time" id="travel_time" min="0"
                                        value="{{ old('travel_time') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('travel_time') border-red-300 @enderror"
                                        placeholder="Enter travel time in minutes">
                                    @error('travel_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="work_time"
                                        class="block text-sm font-heading font-medium text-gray-700 mb-2">Work Time (minutes)</label>
                                    <input type="number" name="work_time" id="work_time" min="0"
                                        value="{{ old('work_time') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('work_time') border-red-300 @enderror"
                                        placeholder="Enter work time in minutes">
                                    @error('work_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Group 5: Incident Logs -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-heading text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Incident Logs</h4>
                                <button type="button" id="add-log-btn"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Log Entry
                                </button>
                            </div>

                            <div id="logs-container" class="space-y-4">
                                <!-- Log entries will be added here dynamically -->
                            </div>

                            <div id="log-template" class="hidden">
                                <div class="log-entry border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div class="sm:col-span-1">
                                                <label class="block text-sm font-heading font-medium text-gray-700 mb-2">Occurred At</label>
                                                <input type="datetime-local" name="logs[INDEX][occurred_at]"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-heading font-medium text-gray-700 mb-2">Note</label>
                                                <textarea name="logs[INDEX][note]" rows="2"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                                    placeholder="Enter log note..."></textarea>
                                            </div>
                                        </div>
                                        <button type="button" class="remove-log-btn flex-shrink-0 text-red-500 hover:text-red-700 mt-6">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Group 6: Attachments -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Attachments</h4>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- PIR / RCA No -->
                                <div>
                                    <label for="pir_rca_no" class="block text-sm font-heading font-medium text-gray-700 mb-2">PIR/RCA No</label>
                                    <input type="text" name="pir_rca_no" id="pir_rca_no"
                                        value="{{ old('pir_rca_no') }}"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 @error('pir_rca_no') border-red-300 @enderror">
                                    @error('pir_rca_no') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- RCA File -->
                                <div>
                                    <label for="rca_file" class="block text-sm font-heading font-medium text-gray-700 mb-2">RCA File (PDF, DOC, DOCX)</label>
                                    <input type="file" name="rca_file" id="rca_file" accept=".pdf,.doc,.docx"
                                        class="w-full cursor-pointer rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:font-semibold file:text-blue-700 hover:file:bg-blue-100 transition-all duration-300">
                                    <p class="mt-2 text-sm text-gray-500">Max size: 10MB</p>
                                    @error('rca_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Points (Critical Incidents) -->
                        <div class="border-t border-gray-200 pt-8" id="action-points-section" style="display: none;">
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="font-heading text-lg font-heading font-semibold text-gray-900">Action Points</h4>
                                <button type="button" id="add-action-point-btn"
                                    class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-all duration-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Action Point
                                </button>
                            </div>

                            <div id="action-points-container" class="space-y-4">
                                <!-- Action points will be dynamically added here -->
                            </div>

                            <div id="action-point-template" class="hidden">
                                <div class="action-point-entry border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-heading font-medium text-gray-700 mb-2">Description</label>
                                                <textarea name="action_points[INDEX][description]" rows="2"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                                    placeholder="Enter action point description..."></textarea>
                                            </div>
                                            <div class="sm:col-span-1 flex flex-col">
                                                <label class="block text-sm font-heading font-medium text-gray-700 mb-2">Due Date</label>
                                                <input type="date" name="action_points[INDEX][due_date]"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                                <div class="flex items-center mt-3">
                                                    <input type="checkbox" name="action_points[INDEX][completed]" value="1"
                                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <label class="ml-2 text-sm text-gray-600">Completed</label>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="remove-action-point-btn flex-shrink-0 text-red-500 hover:text-red-700 mt-6">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('incidents.index') }}"
                                class="rounded-xl bg-gray-200 px-6 py-3 font-medium text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400/30 transition-all duration-300">Cancel</a>
                            <button type="submit"
                                class="rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:from-blue-700 hover:to-blue-800 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-400/30">
                                Create Incident
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // Help section dismiss/show functionality with localStorage
        function dismissHelpSection() {
            document.getElementById('help-section').style.display = 'none';
            document.getElementById('show-help-btn-container').classList.remove('hidden');
            localStorage.setItem('incidentCreateHelpDismissed', 'true');
        }

        function showHelpSection() {
            document.getElementById('help-section').style.display = 'block';
            document.getElementById('show-help-btn-container').classList.add('hidden');
            localStorage.setItem('incidentCreateHelpDismissed', 'false');
        }

        // Check localStorage on page load
        (function() {
            const helpDismissed = localStorage.getItem('incidentCreateHelpDismissed');
            if (helpDismissed === 'true') {
                document.getElementById('help-section').style.display = 'none';
                document.getElementById('show-help-btn-container').classList.remove('hidden');
            }
        })();

        // SLA hint update
        (function () {
            const select = document.getElementById('severity');
            const hint = document.getElementById('slaHint');
            const actionPointsSection = document.getElementById('action-points-section');
            if (!select || !hint) return;

            const map = { Low: '12 hours', Medium: '6 hours', High: '2 hours', Critical: '2 hours' };
            const update = () => {
                const v = select.value || 'Low';
                hint.textContent = `SLA for ${v}: ${map[v]}.`;

                // Show/hide action points section for Critical incidents
                if (actionPointsSection) {
                    if (v === 'Critical') {
                        actionPointsSection.style.display = 'block';
                    } else {
                        actionPointsSection.style.display = 'none';
                    }
                }
            };
            select.addEventListener('change', update);
            update();
        })();

        // Toggle new input fields for inline value creation
        function toggleNewInput(type) {
            const select = document.getElementById(type + '_id');
            const newInput = document.getElementById('new_' + type + '_input');

            if (select && newInput) {
                if (select.value === 'new') {
                    newInput.style.display = 'block';
                    // Clear the main select and focus on the new input
                    select.value = '';
                    const textInput = newInput.querySelector('input[type="text"]');
                    if (textInput) {
                        setTimeout(() => textInput.focus(), 100);
                    }
                } else {
                    newInput.style.display = 'none';
                    // Clear the new input value
                    const textInput = newInput.querySelector('input[type="text"]');
                    if (textInput) {
                        textInput.value = '';
                    }
                }
            }
        }

        // Auto-update resolved_at when started_at changes
        (function() {
            const startedAtInput = document.getElementById('started_at');
            const resolvedAtInput = document.getElementById('resolved_at');

            if (startedAtInput && resolvedAtInput) {
                startedAtInput.addEventListener('change', function() {
                    if (this.value) {
                        const startDate = new Date(this.value);
                        const endDate = new Date(startDate.getTime() + (60 * 60 * 1000)); // Add 1 hour

                        // Format to datetime-local format (YYYY-MM-DDTHH:MM)
                        const year = endDate.getFullYear();
                        const month = String(endDate.getMonth() + 1).padStart(2, '0');
                        const day = String(endDate.getDate()).padStart(2, '0');
                        const hours = String(endDate.getHours()).padStart(2, '0');
                        const minutes = String(endDate.getMinutes()).padStart(2, '0');

                        resolvedAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                    }
                });
            }
        })();

        // Log entries repeater functionality
        (function() {
            let logIndex = 0;
            const addLogBtn = document.getElementById('add-log-btn');
            const logsContainer = document.getElementById('logs-container');
            const logTemplate = document.getElementById('log-template');

            if (!addLogBtn || !logsContainer || !logTemplate) return;

            // Add new log entry
            addLogBtn.addEventListener('click', function() {
                const templateClone = logTemplate.cloneNode(true);
                templateClone.id = '';
                templateClone.classList.remove('hidden');

                // Replace INDEX placeholder with current index
                const html = templateClone.innerHTML.replace(/INDEX/g, logIndex);
                templateClone.innerHTML = html;

                // Set default occurred_at to current time
                const occurredAtInput = templateClone.querySelector('input[type="datetime-local"]');
                if (occurredAtInput) {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    occurredAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                }

                // Add remove functionality
                const removeBtn = templateClone.querySelector('.remove-log-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        templateClone.remove();
                    });
                }

                logsContainer.appendChild(templateClone);
                logIndex++;

                // Focus on the note textarea
                const noteTextarea = templateClone.querySelector('textarea');
                if (noteTextarea) {
                    setTimeout(() => noteTextarea.focus(), 100);
                }
            });
        })();

        // Action points repeater functionality
        (function() {
            let actionPointIndex = 0;
            const addActionPointBtn = document.getElementById('add-action-point-btn');
            const actionPointsContainer = document.getElementById('action-points-container');
            const actionPointTemplate = document.getElementById('action-point-template');

            if (!addActionPointBtn || !actionPointsContainer || !actionPointTemplate) return;

            // Add new action point entry
            addActionPointBtn.addEventListener('click', function() {
                const templateClone = actionPointTemplate.cloneNode(true);
                templateClone.id = '';
                templateClone.classList.remove('hidden');

                // Replace INDEX placeholder with current index
                const html = templateClone.innerHTML.replace(/INDEX/g, actionPointIndex);
                templateClone.innerHTML = html;

                // Set default due date to tomorrow
                const dueDateInput = templateClone.querySelector('input[type="date"]');
                if (dueDateInput) {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    const year = tomorrow.getFullYear();
                    const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
                    const day = String(tomorrow.getDate()).padStart(2, '0');
                    dueDateInput.value = `${year}-${month}-${day}`;
                }

                // Add remove functionality
                const removeBtn = templateClone.querySelector('.remove-action-point-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        templateClone.remove();
                    });
                }

                actionPointsContainer.appendChild(templateClone);
                actionPointIndex++;

                // Focus on the description textarea
                const descriptionTextarea = templateClone.querySelector('textarea');
                if (descriptionTextarea) {
                    setTimeout(() => descriptionTextarea.focus(), 100);
                }
            });
        })();

        // Character counter function
        function updateCharCount(fieldId, maxLength) {
            const field = document.getElementById(fieldId);
            const counter = document.getElementById(fieldId + '-count');
            if (field && counter) {
                counter.textContent = field.value.length;

                // Change color when approaching limit
                if (field.value.length > maxLength * 0.9) {
                    counter.className = 'text-red-600 font-medium';
                } else if (field.value.length > maxLength * 0.8) {
                    counter.className = 'text-yellow-600';
                } else {
                    counter.className = '';
                }
            }
        }

        // Initialize counter on page load
        updateCharCount('summary', 1000);

        // Validate affected services checkboxes
        (function() {
            const form = document.querySelector('form[action="{{ route('incidents.store') }}"]');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                const checkboxes = form.querySelectorAll('input[name="affected_services[]"]:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one Affected System/Service.');
                    const firstCheckbox = form.querySelector('input[name="affected_services[]"]');
                    if (firstCheckbox) {
                        firstCheckbox.focus();
                    }
                    return false;
                }
            });
        })();

        // Show/hide resolved_at field based on status
        function toggleResolvedAtField() {
            const statusSelect = document.getElementById('status');
            const resolvedAtField = document.getElementById('resolved-at-field');
            const resolvedAtInput = document.getElementById('resolved_at');

            if (statusSelect && resolvedAtField && resolvedAtInput) {
                if (statusSelect.value === 'Closed') {
                    resolvedAtField.style.display = 'block';
                    resolvedAtInput.setAttribute('required', 'required');
                } else {
                    resolvedAtField.style.display = 'none';
                    resolvedAtInput.removeAttribute('required');
                    resolvedAtInput.value = ''; // Clear the value when hiding
                }
            }
        }

        // Listen for status changes
        const statusSelect = document.getElementById('status');
        if (statusSelect) {
            statusSelect.addEventListener('change', toggleResolvedAtField);
            // Initialize on page load
            toggleResolvedAtField();
        }

        // Toggle "Others" input field for affected services
        function toggleOthersInput() {
            const othersCheckbox = document.getElementById('affected_services_others');
            const othersInput = document.getElementById('affected_services_others_input');
            const othersTextField = document.getElementById('affected_services_others_text');

            if (othersCheckbox && othersInput && othersTextField) {
                if (othersCheckbox.checked) {
                    othersInput.style.display = 'block';
                    setTimeout(() => othersTextField.focus(), 100);
                } else {
                    othersInput.style.display = 'none';
                    othersTextField.value = '';
                }
            }
        }

        // Initialize "Others" input on page load (in case of validation errors)
        (function() {
            const othersCheckbox = document.getElementById('affected_services_others');
            if (othersCheckbox && othersCheckbox.checked) {
                document.getElementById('affected_services_others_input').style.display = 'block';
            }
        })();
    </script>
@endsection
