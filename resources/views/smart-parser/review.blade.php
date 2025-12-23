@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-900 via-purple-800 to-purple-900 bg-clip-text text-transparent">
                Review Parsed Incident
            </h2>
            <p class="mt-2 text-lg text-gray-600 font-medium">Review and confirm the extracted details</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('smart-parser.index') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-2.5 text-white shadow-lg hover:from-gray-800 hover:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 1200px;">

            <!-- Success Message -->
            <div class="mb-6 rounded-2xl border border-green-100 bg-gradient-to-r from-green-50/50 to-emerald-50/50 backdrop-blur-sm p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-600 to-green-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-heading mb-2 text-sm font-heading font-semibold uppercase tracking-wide text-green-900">
                            Message Parsed Successfully!
                        </h4>
                        <p class="text-sm text-green-800">
                            The incident details have been automatically extracted. Please review the fields below and make any necessary adjustments before creating the incident.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Data Source Information -->
            @if(isset($parsedData['field_sources']))
            <div class="mb-6 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50/50 to-indigo-50/50 backdrop-blur-sm p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-heading mb-3 text-sm font-heading font-semibold uppercase tracking-wide text-blue-900">
                            Parsing Method Used
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                            @foreach($parsedData['field_sources'] as $field => $source)
                                <div class="flex items-center gap-2">
                                    @if($source === 'AI')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-purple-100 text-purple-800 font-medium text-xs">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 7H7v6h6V7z"></path>
                                                <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"></path>
                                            </svg>
                                            AI
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 text-gray-800 font-medium text-xs">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            Regex
                                        </span>
                                    @endif
                                    <span class="text-gray-600 text-xs">{{ ucwords(str_replace('_', ' ', $field)) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs text-blue-700">
                            <strong>AI</strong> = Intelligent natural language understanding | <strong>Regex</strong> = Pattern-based exact extraction
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Form -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm shadow-lg">
                <!-- Section Header -->
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-purple-50/80 to-white/60 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-600 to-purple-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Extracted Incident Details</h3>
                            <p class="text-sm text-gray-600">Review and edit as needed</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('smart-parser.store') }}" class="space-y-8" id="parser-form">
                        @csrf

                        <!-- Duplicate Warning (if applicable) -->
                        @if($errors->has('duplicate'))
                            <div class="rounded-xl border-2 border-orange-300 bg-orange-50 p-5 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <svg class="h-6 w-6 flex-shrink-0 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="font-heading text-sm font-semibold text-orange-900 mb-2">Possible Duplicate Incident Detected</h4>
                                        <p class="text-sm text-orange-800">{{ $errors->first('duplicate') }}</p>

                                        <div class="mt-4 flex gap-3">
                                            <button type="button" onclick="confirmDuplicate()"
                                                class="rounded-xl bg-orange-600 px-5 py-2.5 font-medium text-white shadow-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500/30 transition-all duration-300">
                                                Create Anyway
                                            </button>
                                            <a href="{{ route('smart-parser.index') }}"
                                                class="rounded-xl bg-gray-200 px-5 py-2.5 font-medium text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400/30 transition-all duration-300">
                                                Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Hidden field for duplicate confirmation -->
                        <input type="hidden" name="confirm_duplicate" id="confirm_duplicate" value="0">

                        <!-- Hidden field to preserve original message -->
                        <input type="hidden" name="original_message" value="{{ $message ?? '' }}">

                        <!-- Group 1: Basic Info -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Basic Info</h4>
                            <div class="space-y-4">

                                <!-- Summary -->
                                <div>
                                    <label for="summary" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                        Outage Details (Incident Summary) <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="summary" id="summary" rows="6" maxlength="1000"
                                        class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 resize-y @error('summary') border-red-300 @enderror"
                                    >{{ old('summary', $parsedData['summary']) }}</textarea>
                                    @error('summary')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- Outage Category -->
                                    <div>
                                        <label for="outage_category_id" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                            Outage Category <span class="text-red-500">*</span>
                                        </label>
                                        <select name="outage_category_id" id="outage_category_id"
                                            class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 @error('outage_category_id') border-red-300 @enderror">
                                            <option value="">Select Outage Category</option>
                                            @foreach($outageCategories as $outageCategory)
                                                <option value="{{ $outageCategory->id }}"
                                                    {{ (old('outage_category_id') == $outageCategory->id) || ($outageCategory->name == $parsedData['outage_category']) ? 'selected' : '' }}>
                                                    {{ $outageCategory->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('outage_category_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <label for="category_id" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                            Category <span class="text-red-500">*</span>
                                        </label>
                                        <select name="category_id" id="category_id"
                                            class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 @error('category_id') border-red-300 @enderror">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ (old('category_id') == $category->id) || ($category->name == $parsedData['category']) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Affected Services -->
                                <div>
                                    <label class="block text-sm font-heading font-medium text-gray-700 mb-3">
                                        Affected Systems/Services <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                                        @php
                                            $affectedServicesOptions = ['Cell', 'Single FBB', 'Single Site', 'Multiple Site', 'P2P', 'ILL', 'SIP', 'IPTV', 'Peering', 'Mobile Data', 'Others'];
                                            $selectedServices = old('affected_services', $parsedData['affected_services'] ?? []);
                                            if (is_string($selectedServices)) {
                                                $selectedServices = explode(',', $selectedServices);
                                            }
                                        @endphp
                                        @foreach($affectedServicesOptions as $option)
                                            <div class="flex items-center">
                                                <input type="checkbox"
                                                       name="affected_services[]"
                                                       id="affected_services_{{ str_replace(' ', '_', strtolower($option)) }}"
                                                       value="{{ $option }}"
                                                       {{ in_array($option, $selectedServices) ? 'checked' : '' }}
                                                       class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 focus:ring-2 transition-all duration-200">
                                                <label for="affected_services_{{ str_replace(' ', '_', strtolower($option)) }}"
                                                       class="ml-2 text-sm font-heading font-medium text-gray-700 cursor-pointer hover:text-purple-600 transition-colors">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('affected_services')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Site Impact Fields (Conditional) -->
                                <div x-data="siteImpactForm" x-init="initCheckboxListeners">
                                    <script>
                                        document.addEventListener('alpine:init', () => {
                                            Alpine.data('siteImpactForm', () => ({
                                                showSiteFields: {{ old('affected_services') ? (in_array('Single Site', old('affected_services', [])) || in_array('Multiple Site', old('affected_services', [])) ? 'true' : 'false') : (isset($parsedData['affected_services']) && (in_array('Single Site', $parsedData['affected_services']) || in_array('Multiple Site', $parsedData['affected_services'])) ? 'true' : 'false') }},
                                                showFbbField: {{ old('affected_services') ? (in_array('Single FBB', old('affected_services', [])) ? 'true' : 'false') : (isset($parsedData['affected_services']) && in_array('Single FBB', $parsedData['affected_services']) ? 'true' : 'false') }},

                                                checkServices() {
                                                    const checkboxes = document.querySelectorAll('input[name="affected_services[]"]:checked');
                                                    const values = Array.from(checkboxes).map(cb => cb.value);
                                                    console.log('Checked services:', values);
                                                    this.showSiteFields = values.includes('Single Site') || values.includes('Multiple Site');
                                                    this.showFbbField = values.includes('Single FBB');
                                                    console.log('Show Site Fields:', this.showSiteFields);
                                                    console.log('Show FBB Field:', this.showFbbField);
                                                },

                                                initCheckboxListeners() {
                                                    this.$nextTick(() => {
                                                        document.querySelectorAll('input[name="affected_services[]"]').forEach(checkbox => {
                                                            checkbox.addEventListener('change', () => this.checkServices());
                                                        });
                                                    });
                                                }
                                            }));
                                        });
                                    </script>

                                    <!-- Site Impact Section -->
                                    <div x-show="showSiteFields"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="bg-blue-50/50 border border-blue-200 rounded-xl p-6 mt-4">

                                        <h5 class="font-heading text-sm font-semibold text-blue-900 mb-4 flex items-center gap-2">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                            </svg>
                                            Site Impact Details
                                        </h5>
                                        <p class="text-sm text-blue-700 mb-4">Please specify how many sites of each type are impacted</p>

                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                            <!-- 2G Sites -->
                                            <div>
                                                <label for="sites_2g_impacted" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                                    2G Sites
                                                </label>
                                                <input type="number"
                                                       name="sites_2g_impacted"
                                                       id="sites_2g_impacted"
                                                       min="0"
                                                       value="{{ old('sites_2g_impacted', $parsedData['sites_2g_impacted'] ?? 0) }}"
                                                       class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('sites_2g_impacted') border-red-300 @enderror">
                                                @error('sites_2g_impacted')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- 3G Sites -->
                                            <div>
                                                <label for="sites_3g_impacted" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                                    3G Sites
                                                </label>
                                                <input type="number"
                                                       name="sites_3g_impacted"
                                                       id="sites_3g_impacted"
                                                       min="0"
                                                       value="{{ old('sites_3g_impacted', $parsedData['sites_3g_impacted'] ?? 0) }}"
                                                       class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('sites_3g_impacted') border-red-300 @enderror">
                                                @error('sites_3g_impacted')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- 4G Sites -->
                                            <div>
                                                <label for="sites_4g_impacted" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                                    4G Sites
                                                </label>
                                                <input type="number"
                                                       name="sites_4g_impacted"
                                                       id="sites_4g_impacted"
                                                       min="0"
                                                       value="{{ old('sites_4g_impacted', $parsedData['sites_4g_impacted'] ?? 0) }}"
                                                       class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('sites_4g_impacted') border-red-300 @enderror">
                                                @error('sites_4g_impacted')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- 5G Sites -->
                                            <div>
                                                <label for="sites_5g_impacted" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                                    5G Sites
                                                </label>
                                                <input type="number"
                                                       name="sites_5g_impacted"
                                                       id="sites_5g_impacted"
                                                       min="0"
                                                       value="{{ old('sites_5g_impacted', $parsedData['sites_5g_impacted'] ?? 0) }}"
                                                       class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('sites_5g_impacted') border-red-300 @enderror">
                                                @error('sites_5g_impacted')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FBB Impact Section -->
                                    <div x-show="showFbbField"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="bg-orange-50/50 border border-orange-200 rounded-xl p-6 mt-4">

                                        <h5 class="font-heading text-sm font-semibold text-orange-900 mb-4 flex items-center gap-2">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                            </svg>
                                            FBB Impact Details
                                        </h5>

                                        <div class="max-w-xs">
                                            <label for="fbb_impacted" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                                Number of FBB Impacted
                                            </label>
                                            <input type="number"
                                                   name="fbb_impacted"
                                                   id="fbb_impacted"
                                                   min="0"
                                                   value="{{ old('fbb_impacted', $parsedData['fbb_impacted'] ?? 0) }}"
                                                   class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white transition-all duration-300 @error('fbb_impacted') border-red-300 @enderror">
                                            @error('fbb_impacted')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                            Incident Status <span class="text-red-500">*</span>
                                        </label>
                                        <select name="status" id="status"
                                            class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 @error('status') border-red-300 @enderror">
                                            @foreach(\App\Models\Incident::STATUSES as $status)
                                                <option value="{{ $status }}" {{ old('status', $parsedData['status'] ?? 'Closed') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-purple-600">
                                            Auto-detected: {{ $parsedData['status'] ?? 'Unknown' }}
                                        </p>
                                    </div>

                                    <!-- Severity -->
                                    <div>
                                        <label for="severity" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                            Severity Level <span class="text-red-500">*</span>
                                        </label>
                                        <select name="severity" id="severity"
                                            class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 @error('severity') border-red-300 @enderror">
                                            @foreach(\App\Models\Incident::SEVERITIES as $severity)
                                                <option value="{{ $severity }}" {{ old('severity', $parsedData['severity'] ?? 'Low') === $severity ? 'selected' : '' }}>{{ $severity }}</option>
                                            @endforeach
                                        </select>
                                        @error('severity')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Group 2: Timing -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Timing</h4>
                            <div class="grid grid-cols-1 gap-4 {{ isset($parsedData['status']) && $parsedData['status'] === 'Closed' ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }}">
                                <!-- Outage Start -->
                                <div>
                                    <label for="started_at" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                        Outage Start <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" name="started_at" id="started_at"
                                        value="{{ old('started_at', !empty($parsedData['outage_start_datetime']) ? date('Y-m-d\TH:i', strtotime($parsedData['outage_start_datetime'])) : '') }}"
                                        class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 @error('started_at') border-red-300 @enderror">
                                    @error('started_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Restoration Time - Only for Closed incidents -->
                                @if(isset($parsedData['status']) && $parsedData['status'] === 'Closed')
                                <div>
                                    <label for="resolved_at" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                        Restoration Time <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" name="resolved_at" id="resolved_at"
                                        value="{{ old('resolved_at', !empty($parsedData['restoration_datetime']) ? date('Y-m-d\TH:i', strtotime($parsedData['restoration_datetime'])) : '') }}"
                                        class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 @error('resolved_at') border-red-300 @enderror">
                                    @error('resolved_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Duration -->
                                <div>
                                    <label for="duration_minutes" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                        Duration (minutes)
                                    </label>
                                    <input type="number" name="duration_minutes" id="duration_minutes" min="0"
                                        value="{{ old('duration_minutes', $parsedData['duration_minutes']) }}"
                                        class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 @error('duration_minutes') border-red-300 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">{{ $parsedData['duration'] }}</p>
                                    @error('duration_minutes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                @else
                                <!-- Open incident - no restoration time -->
                                <div class="col-span-1">
                                    <div class="rounded-xl bg-orange-50 border border-orange-200 p-4">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <div>
                                                <h5 class="font-semibold text-orange-900 text-sm">Ongoing Incident</h5>
                                                <p class="text-xs text-orange-700 mt-1">This incident is still open. Restoration time and duration will be added when the incident is resolved.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Group 3: Root Cause & Additional Info -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Root Cause & Additional Info</h4>
                            <div class="space-y-4">
                                <!-- Root Cause -->
                                <div>
                                    <label for="root_cause" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                        Root Cause <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="root_cause" id="root_cause" rows="3"
                                        class="w-full rounded-xl border border-purple-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-purple-50/30 transition-all duration-300 @error('root_cause') border-red-300 @enderror"
                                    >{{ old('root_cause', $parsedData['root_cause']) }}</textarea>
                                    @error('root_cause')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Delay Reason - Only if duration > 5 hours -->
                                @if(isset($parsedData['delay_reason_required']) && $parsedData['delay_reason_required'])
                                <div>
                                    <label for="delay_reason" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                        Reason for Delay <span class="text-red-500">*</span>
                                        <span class="text-xs text-orange-600">(Required: Duration > 5 hours)</span>
                                    </label>
                                    <textarea name="delay_reason" id="delay_reason" rows="3"
                                        placeholder="Explain why this incident took more than 5 hours to resolve..."
                                        class="w-full rounded-xl border border-orange-300 px-4 py-3 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20 bg-orange-50/30 transition-all duration-300 @error('delay_reason') border-red-300 @enderror"
                                    >{{ old('delay_reason') }}</textarea>
                                    @error('delay_reason')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-orange-600">
                                        ⚠️ This incident took {{ $parsedData['duration'] ?? 'more than 5 hours' }} to resolve. Please provide a reason for the delay.
                                    </p>
                                </div>
                                @endif

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- Fault Type -->
                                    <div>
                                        <label for="fault_type_id" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                            Fault/Issue Type
                                        </label>
                                        <select name="fault_type_id" id="fault_type_id"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-white transition-all duration-300 @error('fault_type_id') border-red-300 @enderror">
                                            <option value="">Select Fault Type</option>
                                            @foreach($faultTypes as $faultType)
                                                <option value="{{ $faultType->id }}" {{ old('fault_type_id') == $faultType->id ? 'selected' : '' }}>
                                                    {{ $faultType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('fault_type_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Resolution Team -->
                                    <div>
                                        <label for="resolution_team_id" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                            Resolution Team
                                        </label>
                                        <select name="resolution_team_id" id="resolution_team_id"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-white transition-all duration-300 @error('resolution_team_id') border-red-300 @enderror">
                                            <option value="">Select Resolution Team</option>
                                            @foreach($resolutionTeams as $resolutionTeam)
                                                <option value="{{ $resolutionTeam->id }}" {{ old('resolution_team_id') == $resolutionTeam->id ? 'selected' : '' }}>
                                                    {{ $resolutionTeam->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('resolution_team_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('smart-parser.index') }}"
                                class="rounded-xl bg-gray-200 px-6 py-3 font-medium text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400/30 transition-all duration-300">
                                Back to Parser
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-green-600 to-green-700 px-8 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:from-green-700 hover:to-green-800 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-green-400/30">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Create Incident
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Original Message Reference -->
            <div class="mt-6 rounded-2xl border border-gray-200 bg-gray-50/50 backdrop-blur-sm p-6">
                <h4 class="font-heading mb-3 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">
                    Original Message
                </h4>
                <pre class="text-sm text-gray-700 font-mono whitespace-pre-wrap bg-white rounded-xl p-4 border border-gray-200">{{ $message }}</pre>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // Handle duplicate confirmation
        function confirmDuplicate() {
            document.getElementById('confirm_duplicate').value = '1';
            document.getElementById('parser-form').submit();
        }
    </script>
@endsection
