@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Create New RCA
            </h2>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 font-medium">Root Cause Analysis Document</p>
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
                    <form method="POST" action="{{ route('rcas.store') }}" class="space-y-8">
                        @csrf

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
                                        placeholder="Enter incident number (e.g., INC-2025-0001)"
                                        value="{{ old('incident_code', $selectedIncident->incident_code ?? '') }}"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('incident_id') border-red-300 dark:border-red-700 @enderror">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the incident number to load incident details</p>

                                    <!-- Hidden field for incident_id -->
                                    <input type="hidden" name="incident_id" id="incident_id" value="{{ old('incident_id', $selectedIncident->id ?? '') }}" required>

                                    @error('incident_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Incident Details Display -->
                                <div id="incident-details" class="hidden p-4 bg-blue-50 border border-blue-200 dark:border-blue-700 rounded-xl">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h5 class="font-heading text-sm font-heading font-semibold text-blue-900 mb-2">Incident Details</h5>
                                            <dl class="grid grid-cols-1 gap-2 sm:grid-cols-3 text-sm">
                                                <div>
                                                    <dt class="text-blue-700 dark:text-blue-400 font-medium">Incident Code:</dt>
                                                    <dd class="text-blue-900" id="detail-code">-</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-blue-700 dark:text-blue-400 font-medium">Severity:</dt>
                                                    <dd class="text-blue-900" id="detail-severity">-</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-blue-700 dark:text-blue-400 font-medium">Started:</dt>
                                                    <dd class="text-blue-900" id="detail-started">-</dd>
                                                </div>
                                                <div class="sm:col-span-3">
                                                    <dt class="text-blue-700 dark:text-blue-400 font-medium">Summary:</dt>
                                                    <dd class="text-blue-900" id="detail-summary">-</dd>
                                                </div>
                                            </dl>
                                        </div>
                                        <svg class="h-5 w-5 text-green-600 flex-shrink-0 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Loading/Error Messages -->
                                <div id="incident-loading" class="hidden p-4 bg-gray-50 border border-gray-200 dark:border-gray-700 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <svg class="animate-spin h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">Searching for incident...</p>
                                    </div>
                                </div>

                                <div id="incident-error" class="hidden p-4 bg-red-50 border border-red-200 rounded-xl">
                                    <div class="flex items-start gap-3">
                                        <svg class="h-5 w-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm text-red-800" id="error-message">Incident not found. Please check the incident number.</p>
                                    </div>
                                </div>

                                <!-- RCA Title -->
                                <div>
                                    <label for="title" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        RCA Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="title" id="title" required
                                        value="{{ old('title') }}"
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
                                        <option value="Draft" {{ old('status', 'Draft') === 'Draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="In Review" {{ old('status') === 'In Review' ? 'selected' : '' }}>In Review</option>
                                        <option value="Approved" {{ old('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Closed" {{ old('status') === 'Closed' ? 'selected' : '' }}>Closed</option>
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
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('problem_description') border-red-300 dark:border-red-700 @enderror">{{ old('problem_description') }}</textarea>
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
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('problem_analysis') border-red-300 dark:border-red-700 @enderror">{{ old('problem_analysis') }}</textarea>
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
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('root_cause') border-red-300 dark:border-red-700 @enderror">{{ old('root_cause') }}</textarea>
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
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('workaround') border-red-300 dark:border-red-700 @enderror">{{ old('workaround') }}</textarea>
                                    @error('workaround')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- 4.2 Solution -->
                                <div>
                                    <label for="solution" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">4.2 Solution</label>
                                    <textarea name="solution" id="solution" rows="4"
                                        placeholder="What permanent solution was or will be implemented to fix the root cause?"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('solution') border-red-300 dark:border-red-700 @enderror">{{ old('solution') }}</textarea>
                                    @error('solution')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- 4.3 Recommendation -->
                                <div>
                                    <label for="recommendation" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">4.3 Recommendation</label>
                                    <textarea name="recommendation" id="recommendation" rows="4"
                                        placeholder="What recommendations do you have to prevent similar issues in the future?"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 @error('recommendation') border-red-300 dark:border-red-700 @enderror">{{ old('recommendation') }}</textarea>
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
                                Create RCA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fetch incident details by incident code
        let searchTimeout;
        const incidentCodeInput = document.getElementById('incident_code_input');
        const incidentIdInput = document.getElementById('incident_id');
        const detailsDiv = document.getElementById('incident-details');
        const loadingDiv = document.getElementById('incident-loading');
        const errorDiv = document.getElementById('incident-error');

        incidentCodeInput.addEventListener('input', function(e) {
            const incidentCode = e.target.value.trim();

            // Clear previous timeout
            clearTimeout(searchTimeout);

            // Hide all status divs
            detailsDiv.classList.add('hidden');
            loadingDiv.classList.add('hidden');
            errorDiv.classList.add('hidden');
            incidentIdInput.value = '';

            if (incidentCode.length < 3) {
                return;
            }

            // Show loading after a short delay (debounce)
            searchTimeout = setTimeout(() => {
                loadingDiv.classList.remove('hidden');

                // Fetch incident details
                fetch(`/incidents?search=${encodeURIComponent(incidentCode)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loadingDiv.classList.add('hidden');

                    // Check if we got results
                    if (data.data && data.data.length > 0) {
                        // Find exact match first, otherwise use first result
                        let incident = data.data.find(inc =>
                            inc.incident_code.toLowerCase() === incidentCode.toLowerCase()
                        ) || data.data[0];

                        // Populate incident details
                        document.getElementById('detail-code').textContent = incident.incident_code;
                        document.getElementById('detail-severity').textContent = incident.severity;
                        document.getElementById('detail-started').textContent = new Date(incident.started_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        document.getElementById('detail-summary').textContent = incident.summary;

                        // Set the hidden incident_id
                        incidentIdInput.value = incident.id;

                        // Show details
                        detailsDiv.classList.remove('hidden');
                    } else {
                        // No incident found
                        errorDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    loadingDiv.classList.add('hidden');
                    errorDiv.classList.remove('hidden');
                    console.error('Error fetching incident:', error);
                });
            }, 500); // 500ms debounce
        });

        // Initialize on page load if incident code is pre-filled
        document.addEventListener('DOMContentLoaded', function() {
            if (incidentCodeInput.value.trim().length >= 3) {
                incidentCodeInput.dispatchEvent(new Event('input'));
            }
        });

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

            addBtn.addEventListener('click', function() {
                const clone = template.cloneNode(true);
                clone.id = '';
                clone.classList.remove('hidden');
                const html = clone.innerHTML.replace(/INDEX/g, timeLogIndex);
                clone.innerHTML = html;

                // Set default time to now
                const timeInput = clone.querySelector('input[type="datetime-local"]');
                if (timeInput) {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    timeInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                }

                const removeBtn = clone.querySelector('.remove-time-log-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        clone.remove();
                    });
                }

                container.appendChild(clone);
                timeLogIndex++;

                // Focus on textarea
                const textarea = clone.querySelector('textarea');
                if (textarea) {
                    setTimeout(() => textarea.focus(), 100);
                }
            });
        })();

        // Action points repeater
        (function() {
            let actionPointIndex = 0;
            const addBtn = document.getElementById('add-action-point-btn');
            const container = document.getElementById('action-points-container');
            const template = document.getElementById('action-point-template');

            if (!addBtn || !container || !template) return;

            addBtn.addEventListener('click', function() {
                const clone = template.cloneNode(true);
                clone.id = '';
                clone.classList.remove('hidden');
                const html = clone.innerHTML.replace(/INDEX/g, actionPointIndex);
                clone.innerHTML = html;

                // Set default due date to tomorrow
                const dateInput = clone.querySelector('input[type="date"]');
                if (dateInput) {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    const year = tomorrow.getFullYear();
                    const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
                    const day = String(tomorrow.getDate()).padStart(2, '0');
                    dateInput.value = `${year}-${month}-${day}`;
                }

                const removeBtn = clone.querySelector('.remove-action-point-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        clone.remove();
                    });
                }

                container.appendChild(clone);
                actionPointIndex++;

                // Focus on textarea
                const textarea = clone.querySelector('textarea');
                if (textarea) {
                    setTimeout(() => textarea.focus(), 100);
                }
            });
        })();
    </script>
@endsection
