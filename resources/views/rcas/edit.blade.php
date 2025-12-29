@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Edit RCA: {{ $rca->rca_number }}
            </h2>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 font-medium">{{ $rca->title }}</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('rcas.index') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-gray-700 px-5 py-2.5 text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to RCA List
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 1200px;">

            <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg">
                <div class="border-b border-gray-200 dark:border-gray-700/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-600 to-orange-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100">RCA Information</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Fill in the details below</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('rcas.update', $rca) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">Basic Information</h4>
                            <div class="space-y-4">
                                <!-- Incident Number Input -->
                                <div>
                                    <label for="incident_code_input" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Incident Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="incident_code_input"
                                        value="{{ old('incident_code', $rca->incident->incident_code) }}"
                                        placeholder="Enter incident code (e.g., INC-2024-0001)"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('incident_id') border-red-300 dark:border-red-700 @enderror">
                                    <input type="hidden" name="incident_id" id="incident_id" value="{{ old('incident_id', $rca->incident_id) }}">
                                    @error('incident_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Start typing the incident code to search</p>
                                </div>

                                <!-- Incident Details Display (Success State) -->
                                <div id="incident-details" class="p-4 bg-green-50 border border-green-200 dark:border-green-700 rounded-xl {{ old('incident_id', $rca->incident_id) ? '' : 'hidden' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-heading text-sm font-heading font-semibold text-green-900 mb-2">Incident Found</h5>
                                            <dl class="grid grid-cols-1 gap-2 sm:grid-cols-3 text-sm">
                                                <div>
                                                    <dt class="text-green-700 dark:text-green-400 font-medium">Incident Code:</dt>
                                                    <dd class="text-green-900" id="detail-code">{{ $rca->incident->incident_code }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-green-700 dark:text-green-400 font-medium">Severity:</dt>
                                                    <dd class="text-green-900" id="detail-severity">{{ $rca->incident->severity }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-green-700 dark:text-green-400 font-medium">Started:</dt>
                                                    <dd class="text-green-900" id="detail-start-date">{{ $rca->incident->started_at->format('M j, Y g:i A') }}</dd>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <dt class="text-green-700 dark:text-green-400 font-medium">Summary:</dt>
                                                    <dd class="text-green-900" id="detail-summary">{{ $rca->incident->summary }}</dd>
                                                </div>
                                            </dl>
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading State -->
                                <div id="incident-loading" class="hidden p-4 bg-blue-50 border border-blue-200 dark:border-blue-700 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-sm text-blue-900">Searching for incident...</span>
                                    </div>
                                </div>

                                <!-- Error State -->
                                <div id="incident-error" class="hidden p-4 bg-red-50 border border-red-200 rounded-xl">
                                    <div class="flex items-start gap-3">
                                        <svg class="h-5 w-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm text-red-900">Incident not found. Please check the code and try again.</span>
                                    </div>
                                </div>

                                <!-- RCA Title -->
                                <div>
                                    <label for="title" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        RCA Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="title" id="title" required
                                        value="{{ old('title', $rca->title) }}"
                                        placeholder="e.g., Network Outage Analysis - Core Router Failure"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('title') border-red-300 dark:border-red-700 @enderror">
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        RCA Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" required
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('status') border-red-300 dark:border-red-700 @enderror">
                                        <option value="Draft" {{ old('status', $rca->status) === 'Draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="In Review" {{ old('status', $rca->status) === 'In Review' ? 'selected' : '' }}>In Review</option>
                                        <option value="Approved" {{ old('status', $rca->status) === 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Closed" {{ old('status', $rca->status) === 'Closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 1. Problem Description -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">1. Problem Description</h4>
                            <div>
                                <textarea name="problem_description" id="problem_description" rows="6"
                                    placeholder="Describe the problem in detail. What happened? When did it happen? What was the impact?"
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('problem_description') border-red-300 dark:border-red-700 @enderror">{{ old('problem_description', $rca->problem_description) }}</textarea>
                                @error('problem_description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Time Logs -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-heading text-sm font-heading font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">Timeline of Events</h4>
                                <button type="button" id="add-time-log-btn"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-orange-600 hover:text-orange-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Event
                                </button>
                            </div>

                            <div id="time-logs-container" class="space-y-3">
                                <!-- Time logs will be added here -->
                            </div>

                            <div id="time-log-template" class="hidden">
                                <div class="time-log-entry border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50/50" data-entry-type="event">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div>
                                                <label class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">Date & Time</label>
                                                <input type="datetime-local" name="time_logs[INDEX][occurred_at]"
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">Event Description</label>
                                                <textarea name="time_logs[INDEX][event_description]" rows="2"
                                                    placeholder="What happened at this time?"
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"></textarea>
                                                <div class="flex items-center mt-2">
                                                    <input type="checkbox" id="is_restoration_INDEX" class="is-restoration-checkbox rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500 dark:focus:ring-green-400" onchange="updateTimeLogStyle(this)">
                                                    <label for="is_restoration_INDEX" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                                        <span class="font-medium text-green-700 dark:text-green-400">✓ Service Restored</span> - Mark this as the restoration/resolution event
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="remove-time-log-btn flex-shrink-0 text-red-500 hover:text-red-700 mt-6">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Problem Analysis -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">2. Problem Analysis</h4>
                            <div>
                                <textarea name="problem_analysis" id="problem_analysis" rows="6"
                                    placeholder="Analyze the problem. What data was collected? What tests were performed? What patterns were identified?"
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('problem_analysis') border-red-300 dark:border-red-700 @enderror">{{ old('problem_analysis', $rca->problem_analysis) }}</textarea>
                                @error('problem_analysis')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 3. Root Cause -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">3. Root Cause</h4>
                            <div>
                                <textarea name="root_cause" id="root_cause" rows="6"
                                    placeholder="Identify the root cause. What was the underlying reason for the problem?"
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('root_cause') border-red-300 dark:border-red-700 @enderror">{{ old('root_cause', $rca->root_cause) }}</textarea>
                                @error('root_cause')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 4. Corrective Actions -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">4. Corrective Actions</h4>
                            <div class="space-y-4">
                                <!-- 4.1 Workaround -->
                                <div>
                                    <label for="workaround" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">4.1 Workaround</label>
                                    <textarea name="workaround" id="workaround" rows="4"
                                        placeholder="What temporary solution was implemented to mitigate the issue?"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('workaround') border-red-300 dark:border-red-700 @enderror">{{ old('workaround', $rca->workaround) }}</textarea>
                                    @error('workaround')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- 4.2 Solution -->
                                <div>
                                    <label for="solution" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">4.2 Solution</label>
                                    <textarea name="solution" id="solution" rows="4"
                                        placeholder="What permanent solution was or will be implemented to fix the root cause?"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('solution') border-red-300 dark:border-red-700 @enderror">{{ old('solution', $rca->solution) }}</textarea>
                                    @error('solution')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- 4.3 Recommendation -->
                                <div>
                                    <label for="recommendation" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">4.3 Recommendation</label>
                                    <textarea name="recommendation" id="recommendation" rows="4"
                                        placeholder="What recommendations do you have to prevent similar issues in the future?"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('recommendation') border-red-300 dark:border-red-700 @enderror">{{ old('recommendation', $rca->recommendation) }}</textarea>
                                    @error('recommendation')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 4.1 Action Points -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-heading text-sm font-heading font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">4.1 Action Points</h4>
                                <button type="button" id="add-action-point-btn"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-orange-600 hover:text-orange-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Action Point
                                </button>
                            </div>

                            <div id="action-points-container" class="space-y-3">
                                <!-- Action points will be added here -->
                            </div>

                            <div id="action-point-template" class="hidden">
                                <div class="action-point-entry border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 grid grid-cols-1 gap-4 sm:grid-cols-4">
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">Action Item</label>
                                                <textarea name="action_points[INDEX][action_item]" rows="2"
                                                    placeholder="Describe the action to be taken..."
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">Responsible Person</label>
                                                <input type="text" name="action_points[INDEX][responsible_person]"
                                                    placeholder="Name or team"
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date</label>
                                                <input type="date" name="action_points[INDEX][due_date]"
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                                <div class="flex items-center mt-2">
                                                    <select name="action_points[INDEX][status]"
                                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                                        <option value="Pending">Pending</option>
                                                        <option value="In Progress">In Progress</option>
                                                        <option value="Completed">Completed</option>
                                                        <option value="Cancelled">Cancelled</option>
                                                    </select>
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

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('rcas.index') }}"
                                class="rounded-xl bg-gray-200 px-6 py-3 font-medium text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400/30 transition-all duration-300">
                                Cancel
                            </a>
                            <button type="submit"
                                class="rounded-xl bg-gradient-to-r from-orange-600 to-orange-700 px-8 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-700 hover:to-orange-800 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-orange-400/30">
                                Update RCA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Incident search with AJAX
        (function() {
            const incidentCodeInput = document.getElementById('incident_code_input');
            const incidentIdInput = document.getElementById('incident_id');
            const detailsDiv = document.getElementById('incident-details');
            const loadingDiv = document.getElementById('incident-loading');
            const errorDiv = document.getElementById('incident-error');

            let searchTimeout;

            if (!incidentCodeInput) return;

            incidentCodeInput.addEventListener('input', function(e) {
                const incidentCode = e.target.value.trim();

                // Clear previous timeout
                clearTimeout(searchTimeout);

                // Hide all status divs
                detailsDiv.classList.add('hidden');
                errorDiv.classList.add('hidden');
                loadingDiv.classList.add('hidden');

                // Clear incident_id
                incidentIdInput.value = '';

                // Don't search if input is empty
                if (!incidentCode) {
                    return;
                }

                // Show loading state
                loadingDiv.classList.remove('hidden');

                // Debounce the search
                searchTimeout = setTimeout(() => {
                    fetch(`/incidents?search=${encodeURIComponent(incidentCode)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        loadingDiv.classList.add('hidden');

                        if (data.data && data.data.length > 0) {
                            // Find exact match or use first result
                            const incident = data.data.find(inc =>
                                inc.incident_code.toLowerCase() === incidentCode.toLowerCase()
                            ) || data.data[0];

                            // Populate incident details
                            document.getElementById('detail-code').textContent = incident.incident_code;
                            document.getElementById('detail-severity').textContent = incident.severity;
                            document.getElementById('detail-start-date').textContent = new Date(incident.started_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            document.getElementById('detail-summary').textContent = incident.summary;

                            // Set incident_id
                            incidentIdInput.value = incident.id;

                            // Show details
                            detailsDiv.classList.remove('hidden');
                        } else {
                            // Show error
                            errorDiv.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching incident:', error);
                        loadingDiv.classList.add('hidden');
                        errorDiv.classList.remove('hidden');
                    });
                }, 500); // 500ms debounce
            });
        })();

        // Function to update time log style when marked as restoration
        function updateTimeLogStyle(checkbox) {
            const entry = checkbox.closest('.time-log-entry');
            if (!entry) return;

            if (checkbox.checked) {
                // Uncheck all other restoration checkboxes
                document.querySelectorAll('.is-restoration-checkbox').forEach(cb => {
                    if (cb !== checkbox) {
                        cb.checked = false;
                        const otherEntry = cb.closest('.time-log-entry');
                        if (otherEntry) {
                            otherEntry.classList.remove('border-green-300 dark:border-green-700', 'bg-green-50/50');
                            otherEntry.classList.add('border-gray-200 dark:border-gray-700', 'bg-gray-50/50');
                            otherEntry.setAttribute('data-entry-type', 'event');
                        }
                    }
                });

                // Style this entry as restoration
                entry.classList.remove('border-gray-200 dark:border-gray-700', 'bg-gray-50/50');
                entry.classList.add('border-green-300 dark:border-green-700', 'bg-green-50/50');
                entry.setAttribute('data-entry-type', 'restoration');
            } else {
                // Revert to normal style
                entry.classList.remove('border-green-300 dark:border-green-700', 'bg-green-50/50');
                entry.classList.add('border-gray-200 dark:border-gray-700', 'bg-gray-50/50');
                entry.setAttribute('data-entry-type', 'event');
            }
        }

        // Time logs repeater
        (function() {
            let timeLogIndex = 0;
            const addBtn = document.getElementById('add-time-log-btn');
            const container = document.getElementById('time-logs-container');
            const template = document.getElementById('time-log-template');

            if (!addBtn || !container || !template) return;

            function addTimeLog(occurredAt = null, eventDescription = '') {
                const clone = template.cloneNode(true);
                clone.id = '';
                clone.classList.remove('hidden');
                const html = clone.innerHTML.replace(/INDEX/g, timeLogIndex);
                clone.innerHTML = html;

                // Set time value
                const timeInput = clone.querySelector('input[type="datetime-local"]');
                if (timeInput) {
                    if (occurredAt) {
                        // Convert to local datetime format for input
                        const date = new Date(occurredAt);
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');
                        timeInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                    } else {
                        // Set default time to now
                        const now = new Date();
                        const year = now.getFullYear();
                        const month = String(now.getMonth() + 1).padStart(2, '0');
                        const day = String(now.getDate()).padStart(2, '0');
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        timeInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                    }
                }

                // Set event description
                const textarea = clone.querySelector('textarea');
                if (textarea && eventDescription) {
                    textarea.value = eventDescription;
                }

                const removeBtn = clone.querySelector('.remove-time-log-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        clone.remove();
                    });
                }

                container.appendChild(clone);
                timeLogIndex++;

                // Focus on textarea if new (no description)
                if (textarea && !eventDescription) {
                    setTimeout(() => textarea.focus(), 100);
                }

                return clone;
            }

            addBtn.addEventListener('click', function() {
                addTimeLog();
            });

            // Load existing time logs
            @if($rca->timeLogs->count() > 0)
                @foreach($rca->timeLogs as $timeLog)
                    addTimeLog('{{ $timeLog->occurred_at->toISOString() }}', {!! json_encode($timeLog->event_description) !!});
                @endforeach
            @endif
        })();

        // Action points repeater
        (function() {
            let actionPointIndex = 0;
            const addBtn = document.getElementById('add-action-point-btn');
            const container = document.getElementById('action-points-container');
            const template = document.getElementById('action-point-template');

            if (!addBtn || !container || !template) return;

            function addActionPoint(actionItem = '', responsiblePerson = '', dueDate = null, status = 'Pending') {
                const clone = template.cloneNode(true);
                clone.id = '';
                clone.classList.remove('hidden');
                const html = clone.innerHTML.replace(/INDEX/g, actionPointIndex);
                clone.innerHTML = html;

                // Set action item
                const actionItemTextarea = clone.querySelector('textarea[name^="action_points"]');
                if (actionItemTextarea && actionItem) {
                    actionItemTextarea.value = actionItem;
                }

                // Set responsible person
                const responsiblePersonInput = clone.querySelector('input[name*="responsible_person"]');
                if (responsiblePersonInput && responsiblePerson) {
                    responsiblePersonInput.value = responsiblePerson;
                }

                // Set due date
                const dateInput = clone.querySelector('input[type="date"]');
                if (dateInput) {
                    if (dueDate) {
                        const date = new Date(dueDate);
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        dateInput.value = `${year}-${month}-${day}`;
                    } else {
                        // Set default due date to tomorrow
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        const year = tomorrow.getFullYear();
                        const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
                        const day = String(tomorrow.getDate()).padStart(2, '0');
                        dateInput.value = `${year}-${month}-${day}`;
                    }
                }

                // Set status
                const statusSelect = clone.querySelector('select[name*="status"]');
                if (statusSelect && status) {
                    statusSelect.value = status;
                }

                const removeBtn = clone.querySelector('.remove-action-point-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        clone.remove();
                    });
                }

                container.appendChild(clone);
                actionPointIndex++;

                // Focus on textarea if new (no action item)
                if (actionItemTextarea && !actionItem) {
                    setTimeout(() => actionItemTextarea.focus(), 100);
                }

                return clone;
            }

            addBtn.addEventListener('click', function() {
                addActionPoint();
            });

            // Load existing action points
            @if($rca->actionPoints->count() > 0)
                @foreach($rca->actionPoints as $actionPoint)
                    addActionPoint(
                        {!! json_encode($actionPoint->action_item) !!},
                        {!! json_encode($actionPoint->responsible_person) !!},
                        @if($actionPoint->due_date) '{{ $actionPoint->due_date->toDateString() }}' @else null @endif,
                        '{{ $actionPoint->status }}'
                    );
                @endforeach
            @endif
        })();
    </script>
@endsection
