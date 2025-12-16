@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                {{ __('Edit Incident') }} - {{ $incident->incident_code }}
            </h2>
            <p class="mt-2 text-lg text-gray-600 font-medium">Update incident details and status</p>
        </div>
        <a href="{{ route('incidents.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-2.5 text-white shadow-lg hover:from-gray-800 hover:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>
@endsection

@section('content')

    <div class="py-6 sm:py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl rounded-3xl border border-gray-100/50">
                <div class="p-8 text-gray-900">
                    <form method="POST" action="{{ route('incidents.update', $incident) }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')
                        

                        <!-- Summary -->
                        <div>
                            <label for="summary" class="block text-sm font-medium text-gray-700">Outage Details (Incident Summary) *</label>
                            <textarea name="summary" 
                                      id="summary" 
                                      rows="4" 
                                      maxlength="1000"
                                      placeholder="Provide detailed description of the incident..."
                                      class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white resize-y @error('summary') border-red-300 @enderror"
                                      oninput="updateCharCount('summary', 1000)">{{ old('summary', $incident->summary) }}</textarea>
                            <div class="mt-1 flex justify-between">
                                <div>@error('summary') <span class="text-sm text-red-600">{{ $message }}</span> @enderror</div>
                                <div class="text-xs text-gray-500">
                                    <span id="summary-count">{{ strlen(old('summary', $incident->summary)) }}</span>/1000 characters
                                </div>
                            </div>
                        </div>

                        <!-- Outage Category -->
                        <div>
                            <label for="outage_category" class="block text-sm font-medium text-gray-700">Outage Category *</label>
                            <input list="outage_categories" 
                                   name="outage_category" 
                                   id="outage_category" 
                                   value="{{ old('outage_category', $incident->outage_category) }}"
                                   class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('outage_category') border-red-300 @enderror">
                            <datalist id="outage_categories">
                                @foreach(\App\Models\Incident::OUTAGE_CATEGORIES as $category)
                                    <option value="{{ $category }}">
                                @endforeach
                            </datalist>
                            @error('outage_category')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                            <input list="categories" 
                                   name="category" 
                                   id="category" 
                                   value="{{ old('category', $incident->category) }}"
                                   class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('category') border-red-300 @enderror">
                            <datalist id="categories">
                                @foreach(\App\Models\Incident::CATEGORIES as $category)
                                    <option value="{{ $category }}">
                                @endforeach
                            </datalist>
                            @error('category')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Affected Services -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Affected Systems/Services *</label>
                            <p class="text-xs text-gray-500 mb-2">Select one or more affected systems/services</p>
                            <div class="mt-2 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-5">
                                @php
                                    $affectedServicesOptions = ['Cell', 'Single FBB', 'Single Site', 'Multiple Site', 'P2P', 'ILL', 'SIP', 'IPTV', 'Peering', 'Mobile Data'];
                                    $currentValues = old('affected_services', []);
                                    if (empty($currentValues) && $incident->affected_services) {
                                        $currentValues = is_array($incident->affected_services) 
                                            ? $incident->affected_services 
                                            : explode(', ', $incident->affected_services);
                                    }
                                    if (is_string($currentValues)) {
                                        $currentValues = explode(',', $currentValues);
                                    }
                                @endphp
                                @foreach($affectedServicesOptions as $option)
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="affected_services[]" 
                                               id="affected_services_{{ str_replace(' ', '_', strtolower($option)) }}" 
                                               value="{{ $option }}"
                                               {{ in_array(trim($option), array_map('trim', $currentValues)) ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2 transition-all duration-200">
                                        <label for="affected_services_{{ str_replace(' ', '_', strtolower($option)) }}" 
                                               class="ml-2 text-sm font-medium text-gray-700 cursor-pointer hover:text-blue-600 transition-colors">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="affected_services_validation" value="1">
                            @error('affected_services')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('affected_services.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status and Severity Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Incident Status *</label>
                                <select name="status" 
                                        id="status"
                                        class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('status') border-red-300 @enderror">
                                    @foreach(\App\Models\Incident::STATUSES as $status)
                                        <option value="{{ $status }}" {{ old('status', $incident->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Severity -->
                            <div>
                                <label for="severity" class="block text-sm font-medium text-gray-700">Severity Level *</label>
                                <select name="severity" 
                                        id="severity"
                                        class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('severity') border-red-300 @enderror">
                                    @foreach(\App\Models\Incident::SEVERITIES as $severity)
                                        <option value="{{ $severity }}" {{ old('severity', $incident->severity) === $severity ? 'selected' : '' }}>{{ $severity }}</option>
                                    @endforeach
                                </select>
                                <p id="slaHint" class="mt-1 text-sm text-gray-500">SLA is derived from Severity.</p>
                                @error('severity')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Started At -->
                        <div>
                            <label for="started_at" class="block text-sm font-medium text-gray-700">Start Date and Time *</label>
                            <input type="datetime-local" 
                                   name="started_at" 
                                   id="started_at" 
                                   value="{{ old('started_at', $incident->started_at ? $incident->started_at->format('Y-m-d\TH:i') : '') }}"
                                   class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('started_at') border-red-300 @enderror">
                            @error('started_at')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Resolved At (Only shown when status is Closed) -->
                        <div id="resolved-at-field" style="{{ $incident->status === 'Closed' ? '' : 'display: none;' }}">
                            <label for="resolved_at" class="block text-sm font-medium text-gray-700">Date and Time Resolved <span class="text-red-500">*</span></label>
                            <input type="datetime-local" 
                                   name="resolved_at" 
                                   id="resolved_at" 
                                   value="{{ old('resolved_at', $incident->resolved_at ? $incident->resolved_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                   class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('resolved_at') border-red-300 @enderror">
                            @error('resolved_at')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Manual Duration -->
                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Manual Duration (minutes, optional)</label>
                            <input type="number" 
                                   name="duration_minutes" 
                                   id="duration_minutes" 
                                   min="0"
                                   value="{{ old('duration_minutes', $incident->duration_minutes) }}"
                                   class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('duration_minutes') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Leave blank to auto-calculate from start/resolved times</p>
                            @error('duration_minutes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fault Type -->
                        <div>
                            <label for="fault_type" class="block text-sm font-medium text-gray-700">Fault/Issue Type</label>
                            <input list="fault_types" 
                                   name="fault_type" 
                                   id="fault_type" 
                                   value="{{ old('fault_type', $incident->fault_type) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('fault_type') border-red-300 @enderror">
                            <datalist id="fault_types">
                                @foreach(\App\Models\Incident::FAULT_TYPES as $type)
                                    <option value="{{ $type }}">
                                @endforeach
                            </datalist>
                            @error('fault_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Root Cause -->
                        <div>
                            <label for="root_cause" class="block text-sm font-medium text-gray-700">Root Cause</label>
                            <textarea name="root_cause" 
                                      id="root_cause" 
                                      rows="4"
                                      class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('root_cause') border-red-300 @enderror">{{ old('root_cause', $incident->root_cause) }}</textarea>
                            @error('root_cause')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Delay Reason -->
                        <div>
                            <label for="delay_reason" class="block text-sm font-medium text-gray-700">Reason for Delay</label>
                            <textarea name="delay_reason" 
                                      id="delay_reason" 
                                      rows="4"
                                      class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('delay_reason') border-red-300 @enderror">{{ old('delay_reason', $incident->delay_reason) }}</textarea>
                            @error('delay_reason')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Resolution Team -->
                        <div>
                            <label for="resolution_team" class="block text-sm font-medium text-gray-700">Resolution Team</label>
                            <input type="text" 
                                   name="resolution_team" 
                                   id="resolution_team" 
                                   value="{{ old('resolution_team', $incident->resolution_team) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('resolution_team') border-red-300 @enderror">
                            @error('resolution_team')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Journey Times Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="journey_started_at" class="block text-sm font-medium text-gray-700">Journey Start Time</label>
                                <input type="datetime-local" 
                                       name="journey_started_at" 
                                       id="journey_started_at" 
                                       value="{{ old('journey_started_at', $incident->journey_started_at ? $incident->journey_started_at->format('Y-m-d\TH:i') : '') }}"
                                       class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                            </div>

                            <div>
                                <label for="island_arrival_at" class="block text-sm font-medium text-gray-700">Island Arrival Time</label>
                                <input type="datetime-local" 
                                       name="island_arrival_at" 
                                       id="island_arrival_at" 
                                       value="{{ old('island_arrival_at', $incident->island_arrival_at ? $incident->island_arrival_at->format('Y-m-d\TH:i') : '') }}"
                                       class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                            </div>

                            <div>
                                <label for="work_started_at" class="block text-sm font-medium text-gray-700">Work/Repair Start Time</label>
                                <input type="datetime-local" 
                                       name="work_started_at" 
                                       id="work_started_at" 
                                       value="{{ old('work_started_at', $incident->work_started_at ? $incident->work_started_at->format('Y-m-d\TH:i') : '') }}"
                                       class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                            </div>

                            <div>
                                <label for="work_completed_at" class="block text-sm font-medium text-gray-700">Repair Completion Time</label>
                                <input type="datetime-local" 
                                       name="work_completed_at" 
                                       id="work_completed_at" 
                                       value="{{ old('work_completed_at', $incident->work_completed_at ? $incident->work_completed_at->format('Y-m-d\TH:i') : '') }}"
                                       class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                            </div>
                        </div>

                        <!-- PIR/RCA No -->
                        <div>
                            <label for="pir_rca_no" class="block text-sm font-medium text-gray-700">PIR/RCA No</label>
                            <input type="text" 
                                   name="pir_rca_no" 
                                   id="pir_rca_no" 
                                   value="{{ old('pir_rca_no', $incident->pir_rca_no) }}"
                                   class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('pir_rca_no') border-red-300 @enderror">
                            @error('pir_rca_no')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- RCA File -->
                        <div>
                            <label for="rca_file" class="block text-sm font-medium text-gray-700">
                                RCA File (PDF, DOC, DOCX)
                                @if(in_array($incident->severity, ['High', 'Critical']) && !$incident->hasRcaFile())
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            @if($incident->hasRcaFile())
                                <div class="mt-2 mb-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm text-green-700">
                                        <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Current RCA file: <a href="{{ route('incidents.download-rca', $incident) }}" class="text-blue-600 hover:underline font-medium">{{ basename($incident->rca_file_path) }}</a>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Upload a new file to replace the existing one.</p>
                                </div>
                            @endif
                            <input type="file" 
                                   name="rca_file" 
                                   id="rca_file" 
                                   accept=".pdf,.doc,.docx"
                                   class="mt-2 block w-full cursor-pointer rounded-2xl border border-dashed border-gray-300/50 bg-gray-50/80 backdrop-blur-sm p-4 text-sm file:mr-4 file:rounded-xl file:border-0 file:bg-gradient-to-r file:from-blue-50 file:to-blue-100 file:px-4 file:py-2 file:font-semibold file:text-blue-700 hover:file:from-blue-100 hover:file:to-blue-200 transition-all duration-300 @error('rca_file') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Max size: 10MB. Accepted formats: PDF, DOC, DOCX</p>
                            @if(in_array($incident->severity, ['High', 'Critical']) && !$incident->hasRcaFile())
                                <p class="mt-1 text-sm text-red-600 font-medium">⚠️ RCA file is required for {{ $incident->severity }} severity incidents.</p>
                            @endif
                            @error('rca_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Structured RCA Fields -->
                        <div class="border-t border-gray-200/50 pt-8" id="rca-fields" style="{{ in_array($incident->severity ?? '', ['High', 'Critical']) ? '' : 'display: none;' }}">
                            <h4 class="text-lg font-semibold text-gray-900 mb-6">Root Cause Analysis</h4>
                            
                            <!-- High Severity RCA Fields -->
                            <div id="high-severity-rca" style="{{ ($incident->severity ?? '') === 'High' ? '' : 'display: none;' }}">
                                <div class="space-y-6">
                                    <!-- Corrective Actions -->
                                    <div>
                                        <label for="corrective_actions" class="block text-sm font-medium text-gray-700">Corrective Actions *</label>
                                        <textarea name="corrective_actions" 
                                                  id="corrective_actions" 
                                                  rows="4"
                                                  placeholder="Describe the corrective actions taken to resolve the incident..."
                                                  class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white resize-y @error('corrective_actions') border-red-300 @enderror">{{ old('corrective_actions', $incident->corrective_actions) }}</textarea>
                                        @error('corrective_actions')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Workaround -->
                                    <div>
                                        <label for="workaround" class="block text-sm font-medium text-gray-700">Workaround *</label>
                                        <textarea name="workaround" 
                                                  id="workaround" 
                                                  rows="4"
                                                  placeholder="Describe any temporary workarounds implemented..."
                                                  class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white resize-y @error('workaround') border-red-300 @enderror">{{ old('workaround', $incident->workaround) }}</textarea>
                                        @error('workaround')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Solution -->
                                    <div>
                                        <label for="solution" class="block text-sm font-medium text-gray-700">Solution *</label>
                                        <textarea name="solution" 
                                                  id="solution" 
                                                  rows="4"
                                                  placeholder="Describe the permanent solution implemented..."
                                                  class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white resize-y @error('solution') border-red-300 @enderror">{{ old('solution', $incident->solution) }}</textarea>
                                        @error('solution')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Recommendation -->
                                    <div>
                                        <label for="recommendation" class="block text-sm font-medium text-gray-700">Recommendation *</label>
                                        <textarea name="recommendation" 
                                                  id="recommendation" 
                                                  rows="4"
                                                  placeholder="Provide recommendations to prevent similar incidents..."
                                                  class="mt-2 block w-full border border-gray-300/50 rounded-2xl shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 px-4 py-3 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white resize-y @error('recommendation') border-red-300 @enderror">{{ old('recommendation', $incident->recommendation) }}</textarea>
                                        @error('recommendation')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Incident Logs -->
                        <div class="border-t border-gray-200/50 pt-8">
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="text-lg font-semibold text-gray-900">Incident Logs</h4>
                                <button type="button" id="add-log-btn" 
                                    class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-all duration-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Log Entry
                                </button>
                            </div>
                            
                            <div id="logs-container" class="space-y-4">
                                <!-- Existing logs -->
                                @foreach($incident->logs as $index => $log)
                                    <div class="log-entry border border-gray-200 rounded-2xl p-4 bg-gray-50/50">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-1 grid grid-cols-1 gap-4 md:grid-cols-3">
                                                <div class="md:col-span-1">
                                                    <label class="block text-sm font-medium text-gray-700">Occurred At</label>
                                                    <input type="datetime-local" name="logs[{{ $index }}][occurred_at]" 
                                                        value="{{ old('logs.' . $index . '.occurred_at', $log->occurred_at->format('Y-m-d\TH:i')) }}"
                                                        class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700">Note</label>
                                                    <textarea name="logs[{{ $index }}][note]" rows="2" 
                                                        class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" 
                                                        placeholder="Enter log note...">{{ old('logs.' . $index . '.note', $log->note) }}</textarea>
                                                </div>
                                            </div>
                                            <button type="button" class="remove-log-btn flex-shrink-0 text-red-500 hover:text-red-700 mt-6">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div id="log-template" class="hidden">
                                <div class="log-entry border border-gray-200 rounded-2xl p-4 bg-gray-50/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 grid grid-cols-1 gap-4 md:grid-cols-3">
                                            <div class="md:col-span-1">
                                                <label class="block text-sm font-medium text-gray-700">Occurred At</label>
                                                <input type="datetime-local" name="logs[INDEX][occurred_at]" 
                                                    class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Note</label>
                                                <textarea name="logs[INDEX][note]" rows="2" 
                                                    class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" 
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

                        <!-- Action Points (Critical Incidents) -->
                        <div class="border-t border-gray-200/50 pt-8" id="action-points-section" style="{{ ($incident->severity ?? '') === 'Critical' ? '' : 'display: none;' }}">
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="text-lg font-semibold text-gray-900">Action Points</h4>
                                <button type="button" id="add-action-point-btn" 
                                    class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-all duration-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Action Point
                                </button>
                            </div>
                            
                            <div id="action-points-container" class="space-y-4">
                                <!-- Existing action points -->
                                @foreach($incident->actionPoints as $index => $actionPoint)
                                    <div class="action-point-entry border border-gray-200 rounded-2xl p-4 bg-gray-50/50">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-1 grid grid-cols-1 gap-4 md:grid-cols-3">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea name="action_points[{{ $index }}][description]" rows="2" 
                                                        class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" 
                                                        placeholder="Enter action point description...">{{ old('action_points.' . $index . '.description', $actionPoint->description) }}</textarea>
                                                </div>
                                                <div class="md:col-span-1 flex flex-col">
                                                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                                    <input type="date" name="action_points[{{ $index }}][due_date]" 
                                                        value="{{ old('action_points.' . $index . '.due_date', $actionPoint->due_date->format('Y-m-d')) }}"
                                                        class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                                    <div class="flex items-center mt-2">
                                                        <input type="checkbox" name="action_points[{{ $index }}][completed]" value="1" 
                                                            {{ $actionPoint->completed ? 'checked' : '' }}
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
                                @endforeach
                            </div>
                            
                            <div id="action-point-template" class="hidden">
                                <div class="action-point-entry border border-gray-200 rounded-2xl p-4 bg-gray-50/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 grid grid-cols-1 gap-4 md:grid-cols-3">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                                <textarea name="action_points[INDEX][description]" rows="2" 
                                                    class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" 
                                                    placeholder="Enter action point description..."></textarea>
                                            </div>
                                            <div class="md:col-span-1 flex flex-col">
                                                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                                <input type="date" name="action_points[INDEX][due_date]" 
                                                    class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                                <div class="flex items-center mt-2">
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

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-4 pt-4">
                            <a href="{{ route('incidents.index') }}" 
                               class="rounded-2xl bg-gradient-to-r from-gray-300 to-gray-400 px-6 py-3 font-medium text-gray-800 hover:from-gray-400 hover:to-gray-500 transition-all duration-300 transform hover:-translate-y-0.5">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-3 font-semibold text-white shadow-lg hover:from-blue-700 hover:to-blue-800 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                                Update Incident
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Log entries repeater functionality
        (function() {
            let logIndex = {{ $incident->logs->count() }}; // Start from existing log count
            const addLogBtn = document.getElementById('add-log-btn');
            const logsContainer = document.getElementById('logs-container');
            const logTemplate = document.getElementById('log-template');
            
            if (!addLogBtn || !logsContainer || !logTemplate) return;
            
            // Add remove functionality to existing logs
            document.querySelectorAll('.remove-log-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.log-entry').remove();
                });
            });
            
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
            const form = document.querySelector('form[action*="incidents"]');
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
                    // Set current time if no value exists
                    if (!resolvedAtInput.value) {
                        const now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        resolvedAtInput.value = now.toISOString().slice(0, 16);
                    }
                } else {
                    resolvedAtField.style.display = 'none';
                    resolvedAtInput.removeAttribute('required');
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

        // Action Points functionality
        (function() {
            let actionPointIndex = {{ $incident->actionPoints->count() }}; // Start from existing count
            const addActionPointBtn = document.getElementById('add-action-point-btn');
            const actionPointsContainer = document.getElementById('action-points-container');
            const actionPointTemplate = document.getElementById('action-point-template');
            
            if (!addActionPointBtn || !actionPointsContainer || !actionPointTemplate) return;
            
            // Add remove functionality to existing action points
            document.querySelectorAll('.remove-action-point-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.action-point-entry').remove();
                });
            });
            
            // Add new action point entry
            addActionPointBtn.addEventListener('click', function() {
                const templateClone = actionPointTemplate.cloneNode(true);
                templateClone.id = '';
                templateClone.classList.remove('hidden');
                
                // Replace INDEX placeholder with current index
                const html = templateClone.innerHTML.replace(/INDEX/g, actionPointIndex);
                templateClone.innerHTML = html;
                
                // Set default due date to 7 days from now
                const dueDateInput = templateClone.querySelector('input[type="date"]');
                if (dueDateInput) {
                    const futureDate = new Date();
                    futureDate.setDate(futureDate.getDate() + 7);
                    dueDateInput.value = futureDate.toISOString().split('T')[0];
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
                const descTextarea = templateClone.querySelector('textarea');
                if (descTextarea) {
                    setTimeout(() => descTextarea.focus(), 100);
                }
            });
        })();

        // Show/hide RCA and Action Points sections based on severity
        function toggleSeverityFields() {
            const severitySelect = document.getElementById('severity');
            const rcaFields = document.getElementById('rca-fields');
            const highSeverityRca = document.getElementById('high-severity-rca');
            const actionPointsSection = document.getElementById('action-points-section');
            
            if (severitySelect && rcaFields && highSeverityRca && actionPointsSection) {
                const severity = severitySelect.value;
                
                if (severity === 'High') {
                    rcaFields.style.display = 'block';
                    highSeverityRca.style.display = 'block';
                    actionPointsSection.style.display = 'none';
                } else if (severity === 'Critical') {
                    rcaFields.style.display = 'block';
                    highSeverityRca.style.display = 'none';
                    actionPointsSection.style.display = 'block';
                } else {
                    rcaFields.style.display = 'none';
                    highSeverityRca.style.display = 'none';
                    actionPointsSection.style.display = 'none';
                }
            }
        }

        // Listen for severity changes
        const severitySelect = document.getElementById('severity');
        if (severitySelect) {
            severitySelect.addEventListener('change', toggleSeverityFields);
            // Initialize on page load
            toggleSeverityFields();
        }
    </script>
@endsection