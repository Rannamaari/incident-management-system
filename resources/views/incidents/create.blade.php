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
                        class="space-y-10" id="incident-form">
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
                                            <a href="{{ route('incidents.index') }}"
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

                        <!-- Group 1: Basics -->
                        <div>
                            <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Step 1: Select Affected Systems</h4>
                            <div class="space-y-6">

                                <!-- Affected Services - MOVED TO TOP -->
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

                                <!-- Site Impact Fields (Conditional) -->
                                <div x-data="siteImpactForm" x-init="initCheckboxListeners">
                                    <script>
                                        document.addEventListener('alpine:init', () => {
                                            Alpine.data('siteImpactForm', () => ({
                                                showSiteFields: {{ old('affected_services') ? (in_array('Single Site', old('affected_services', [])) || in_array('Multiple Site', old('affected_services', [])) ? 'true' : 'false') : 'false' }},
                                                showFbbField: {{ old('affected_services') ? (in_array('Single FBB', old('affected_services', [])) ? 'true' : 'false') : 'false' }},

                                                // Site selection data
                                                selectedSites: {},
                                                searchSite: '',
                                                selectedRegion: '',
                                                sites: {{ Js::from($sites ?? []) }},
                                                selectedServices: [], // Track which services are selected

                                                // FBB Islands selection data
                                                selectedFbbIslands: [],
                                                searchFbbIsland: '',
                                                selectedFbbRegion: '',
                                                fbbIslands: {{ Js::from($fbbIslands ?? []) }},

                                                init() {
                                                    console.log('Sites data:', this.sites);
                                                    console.log('FBB Islands data:', this.fbbIslands);
                                                },

                                                checkServices() {
                                                    const checkboxes = document.querySelectorAll('input[name="affected_services[]"]:checked');
                                                    const values = Array.from(checkboxes).map(cb => cb.value);

                                                    // Check if we're switching modes or disabling site selection
                                                    // Only FBB (Supernet) triggers FBB mode for sites, Single FBB is handled via FBB Islands
                                                    const wasFbbMode = this.selectedServices.includes('FBB');
                                                    const isFbbMode = values.includes('FBB');
                                                    const hadSiteFields = this.selectedServices.includes('Single Site') || this.selectedServices.includes('Multiple Site');
                                                    const hasSiteFields = values.includes('Single Site') || values.includes('Multiple Site');

                                                    this.selectedServices = values; // Store selected services
                                                    console.log('Checked services:', values);
                                                    this.showSiteFields = hasSiteFields;
                                                    this.showFbbField = values.includes('Single FBB');
                                                    console.log('Show Site Fields:', this.showSiteFields);
                                                    console.log('Show FBB Field:', this.showFbbField);

                                                    // If all site-related services are unchecked, clear selected sites
                                                    if (hadSiteFields && !hasSiteFields && Object.keys(this.selectedSites).length > 0) {
                                                        console.log('Clearing all selected sites - no site-related services selected');
                                                        this.selectedSites = {};
                                                        this.updateCountInputs();
                                                    }

                                                    // If Single FBB is unchecked, clear selected FBB islands
                                                    const hadSingleFbb = this.selectedServices.includes('Single FBB');
                                                    const hasSingleFbb = values.includes('Single FBB');
                                                    if (hadSingleFbb && !hasSingleFbb && this.selectedFbbIslands.length > 0) {
                                                        console.log('Clearing all selected FBB islands - Single FBB unchecked');
                                                        this.selectedFbbIslands = [];
                                                        document.getElementById('fbb_impacted').value = 0;
                                                        this.updateSummary();
                                                    }

                                                    // If switching modes, update selected sites
                                                    if (wasFbbMode !== isFbbMode && Object.keys(this.selectedSites).length > 0) {
                                                        // Clear sites when entering FBB mode (keep sites with FBB tech available)
                                                        if (isFbbMode) {
                                                            for (const siteId in this.selectedSites) {
                                                                const site = this.sites.find(s => s.id == siteId);
                                                                if (site && site.technologies) {
                                                                    const hasFbbTech = site.technologies.some(t => t.technology === 'FBB' && t.is_active);
                                                                    if (!hasFbbTech) {
                                                                        delete this.selectedSites[siteId];
                                                                    } else {
                                                                        // Update to FBB technology
                                                                        this.selectedSites[siteId] = ['FBB'];
                                                                    }
                                                                } else {
                                                                    delete this.selectedSites[siteId];
                                                                }
                                                            }
                                                        } else {
                                                            // Update to cellular technologies when leaving FBB mode
                                                            for (const siteId in this.selectedSites) {
                                                                const site = this.sites.find(s => s.id == siteId);
                                                                if (site && site.technologies) {
                                                                    const activeCellular = site.technologies
                                                                        .filter(t => ['2G', '3G', '4G', '5G'].includes(t.technology) && t.is_active)
                                                                        .map(t => t.technology);
                                                                    this.selectedSites[siteId] = activeCellular.length > 0 ? activeCellular : ['2G', '3G', '4G', '5G'];
                                                                }
                                                            }
                                                        }
                                                        this.updateCountInputs();
                                                    }
                                                },

                                                initCheckboxListeners() {
                                                    this.$nextTick(() => {
                                                        document.querySelectorAll('input[name="affected_services[]"]').forEach(checkbox => {
                                                            checkbox.addEventListener('change', () => this.checkServices());
                                                        });
                                                    });
                                                },

                                                get filteredSites() {
                                                    if (!this.sites || !Array.isArray(this.sites)) return [];

                                                    const filtered = this.sites.filter(site => {
                                                        if (!site) return false;

                                                        const matchesSearch = !this.searchSite ||
                                                            (site.site_code && site.site_code.toLowerCase().includes(this.searchSite.toLowerCase())) ||
                                                            (site.display_name && site.display_name.toLowerCase().includes(this.searchSite.toLowerCase()));
                                                        const matchesRegion = !this.selectedRegion || site.region_id == this.selectedRegion;
                                                        return matchesSearch && matchesRegion;
                                                    });

                                                    console.log('Filtered sites count:', filtered.length);
                                                    return filtered;
                                                },

                                                get filteredFbbIslands() {
                                                    if (!this.fbbIslands || !Array.isArray(this.fbbIslands)) return [];

                                                    const filtered = this.fbbIslands.filter(island => {
                                                        if (!island) return false;

                                                        const matchesSearch = !this.searchFbbIsland ||
                                                            (island.island_name && island.island_name.toLowerCase().includes(this.searchFbbIsland.toLowerCase())) ||
                                                            (island.full_name && island.full_name.toLowerCase().includes(this.searchFbbIsland.toLowerCase()));
                                                        const matchesRegion = !this.selectedFbbRegion || island.region_id == this.selectedFbbRegion;
                                                        return matchesSearch && matchesRegion;
                                                    });

                                                    console.log('Filtered FBB islands count:', filtered.length);
                                                    return filtered;
                                                },

                                                get isFbbOnlyMode() {
                                                    // Only FBB (Supernet) triggers FBB mode for sites
                                                    // Single FBB is handled separately via FBB Islands
                                                    return this.selectedServices.includes('FBB');
                                                },

                                                get techCounts() {
                                                    const counts = { '2G': 0, '3G': 0, '4G': 0, '5G': 0, 'FBB': 0, 'ILL': 0, 'SIP': 0, 'IPTV': 0 };
                                                    Object.values(this.selectedSites).forEach(techs => {
                                                        if (Array.isArray(techs)) {
                                                            techs.forEach(tech => {
                                                                if (counts[tech] !== undefined) counts[tech]++;
                                                            });
                                                        }
                                                    });
                                                    return counts;
                                                },

                                                updateCountInputs() {
                                                    const counts = this.techCounts;
                                                    document.getElementById('sites_2g_impacted').value = counts['2G'];
                                                    document.getElementById('sites_3g_impacted').value = counts['3G'];
                                                    document.getElementById('sites_4g_impacted').value = counts['4G'];
                                                    document.getElementById('sites_5g_impacted').value = counts['5G'];
                                                    document.getElementById('fbb_impacted').value = counts['FBB'];
                                                    this.updateSummary();
                                                    this.updateAffectedServices();
                                                },

                                                updateAffectedServices() {
                                                    const selectedCount = Object.keys(this.selectedSites).length;
                                                    const affectedServices = [];

                                                    // Auto-select Single Site or Multiple Site based on selection count
                                                    if (selectedCount === 1) {
                                                        affectedServices.push('Single Site');
                                                    } else if (selectedCount > 1) {
                                                        affectedServices.push('Multiple Site');
                                                    }

                                                    // Check if any selected site has FBB enabled
                                                    let hasFBB = false;
                                                    for (const siteId of Object.keys(this.selectedSites)) {
                                                        const site = this.sites.find(s => s.id == siteId);
                                                        if (site && site.has_fbb) {
                                                            hasFBB = true;
                                                            break;
                                                        }
                                                    }

                                                    if (hasFBB) {
                                                        affectedServices.push('FBB');
                                                    }

                                                    // Update affected services checkboxes
                                                    const checkboxes = document.querySelectorAll('input[name="affected_services[]"]');
                                                    checkboxes.forEach(checkbox => {
                                                        const isAutoService = checkbox.value === 'Single Site' || checkbox.value === 'Multiple Site' || checkbox.value === 'FBB';

                                                        if (affectedServices.includes(checkbox.value)) {
                                                            checkbox.checked = true;
                                                        } else if (selectedCount > 0 && isAutoService) {
                                                            checkbox.checked = false;
                                                        }
                                                    });
                                                },

                                                updateSummary() {
                                                    console.log('updateSummary called');
                                                    console.log('selectedSites:', this.selectedSites);
                                                    console.log('selectedFbbIslands:', this.selectedFbbIslands);

                                                    const summaryField = document.getElementById('summary');
                                                    const selectedSitesCount = Object.keys(this.selectedSites).length;
                                                    const selectedFbbCount = this.selectedFbbIslands.length;

                                                    console.log('Sites count:', selectedSitesCount, 'FBB count:', selectedFbbCount);

                                                    if (selectedSitesCount === 0 && selectedFbbCount === 0) {
                                                        summaryField.readOnly = false;
                                                        summaryField.classList.remove('bg-gray-50', 'cursor-not-allowed');
                                                        summaryField.classList.add('bg-white');
                                                        return;
                                                    }

                                                    // Make field readonly
                                                    summaryField.readOnly = true;
                                                    summaryField.classList.add('bg-gray-50', 'cursor-not-allowed');
                                                    summaryField.classList.remove('bg-white');

                                                    // Generate summary - sites and FBB islands, separated by commas
                                                    let summaryLines = [];

                                                    // Add sites with technologies
                                                    for (const [siteId, techs] of Object.entries(this.selectedSites)) {
                                                        const site = this.sites.find(s => s.id == siteId);
                                                        if (site && Array.isArray(techs) && techs.length > 0) {
                                                            const techStr = techs.sort().join('/');
                                                            summaryLines.push(`${site.site_code} ${techStr}`);
                                                        }
                                                    }

                                                    console.log('After sites, summaryLines:', summaryLines);

                                                    // Add FBB islands
                                                    for (const islandId of this.selectedFbbIslands) {
                                                        const island = this.fbbIslands.find(i => i.id == islandId);
                                                        console.log('Processing FBB island ID:', islandId, 'Found:', island);
                                                        if (island) {
                                                            summaryLines.push(`${island.full_name} FBB`);
                                                        }
                                                    }

                                                    console.log('Final summaryLines:', summaryLines);
                                                    summaryField.value = summaryLines.join(', ');
                                                },

                                                toggleSite(siteId) {
                                                    if (this.selectedSites[siteId]) {
                                                        delete this.selectedSites[siteId];
                                                    } else {
                                                        // Auto-select appropriate technologies based on mode
                                                        if (this.isFbbOnlyMode) {
                                                            this.selectedSites[siteId] = ['FBB'];
                                                        } else {
                                                            // Auto-select all active cellular technologies
                                                            const site = this.sites.find(s => s.id == siteId);
                                                            if (site && site.technologies) {
                                                                const activeCellular = site.technologies
                                                                    .filter(t => ['2G', '3G', '4G', '5G'].includes(t.technology) && t.is_active)
                                                                    .map(t => t.technology);
                                                                this.selectedSites[siteId] = activeCellular.length > 0 ? activeCellular : ['2G', '3G', '4G', '5G'];
                                                            } else {
                                                                this.selectedSites[siteId] = ['2G', '3G', '4G', '5G'];
                                                            }
                                                        }
                                                    }
                                                    this.updateCountInputs();
                                                },

                                                toggleTechnology(siteId, tech) {
                                                    if (!this.selectedSites[siteId]) {
                                                        // Site not selected, add it with this technology
                                                        this.selectedSites = { ...this.selectedSites, [siteId]: [tech] };
                                                    } else {
                                                        const currentTechs = this.selectedSites[siteId];
                                                        const index = currentTechs.indexOf(tech);

                                                        if (index > -1) {
                                                            // Technology is selected, remove it
                                                            const newTechs = currentTechs.filter(t => t !== tech);
                                                            if (newTechs.length === 0) {
                                                                // No technologies left, remove site entirely
                                                                const { [siteId]: removed, ...rest } = this.selectedSites;
                                                                this.selectedSites = rest;
                                                            } else {
                                                                // Update with remaining technologies
                                                                this.selectedSites = { ...this.selectedSites, [siteId]: newTechs };
                                                            }
                                                        } else {
                                                            // Technology not selected, add it
                                                            this.selectedSites = { ...this.selectedSites, [siteId]: [...currentTechs, tech] };
                                                        }
                                                    }
                                                    this.updateCountInputs();
                                                },

                                                removeSite(siteId) {
                                                    delete this.selectedSites[siteId];
                                                    this.updateCountInputs();
                                                },

                                                toggleFbbIsland(islandId) {
                                                    const index = this.selectedFbbIslands.indexOf(islandId);
                                                    if (index > -1) {
                                                        this.selectedFbbIslands.splice(index, 1);
                                                    } else {
                                                        this.selectedFbbIslands.push(islandId);
                                                    }
                                                    // Update FBB count
                                                    document.getElementById('fbb_impacted').value = this.selectedFbbIslands.length;
                                                    // Update summary to include FBB islands
                                                    this.updateSummary();
                                                },

                                                removeFbbIsland(islandId) {
                                                    const index = this.selectedFbbIslands.indexOf(islandId);
                                                    if (index > -1) {
                                                        this.selectedFbbIslands.splice(index, 1);
                                                    }
                                                    // Update FBB count
                                                    document.getElementById('fbb_impacted').value = this.selectedFbbIslands.length;
                                                    // Update summary to include FBB islands
                                                    this.updateSummary();
                                                }
                                            }));
                                        });
                                    </script>

                                    <!-- Hidden Site Impact Fields (Auto-calculated in background) -->
                                    <div x-show="false">
                                        <input type="hidden" name="sites_2g_impacted" id="sites_2g_impacted" value="{{ old('sites_2g_impacted', 0) }}">
                                        <input type="hidden" name="sites_3g_impacted" id="sites_3g_impacted" value="{{ old('sites_3g_impacted', 0) }}">
                                        <input type="hidden" name="sites_4g_impacted" id="sites_4g_impacted" value="{{ old('sites_4g_impacted', 0) }}">
                                        <input type="hidden" name="sites_5g_impacted" id="sites_5g_impacted" value="{{ old('sites_5g_impacted', 0) }}">
                                        <input type="hidden" name="fbb_impacted" id="fbb_impacted" value="{{ old('fbb_impacted', 0) }}">
                                    </div>

                                    <!-- Site Selection Section (for Site Outages)-->
                                    <div x-show="showSiteFields"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="bg-green-50/50 border border-green-200 rounded-xl p-6 mt-4">

                                        <h5 class="font-heading text-sm font-semibold text-green-900 mb-4 flex items-center gap-2">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Select Affected Sites
                                        </h5>
                                        <p class="text-sm text-green-700 mb-4">Search and select specific sites affected by this outage</p>

                                        <!-- Selected Sites Summary (Above List) -->
                                        <div x-show="Object.keys(selectedSites).length > 0"
                                             x-transition
                                             class="mb-4 p-4 bg-green-50 border border-green-300 rounded-xl">
                                            <div class="flex items-center justify-between mb-3">
                                                <p class="text-sm font-semibold text-green-900">
                                                    <span x-text="Object.keys(selectedSites).length"></span> site(s) selected
                                                </p>
                                                <button type="button"
                                                        @click="selectedSites = {}; updateCountInputs();"
                                                        class="text-xs text-red-600 hover:text-red-800 font-medium transition-colors">
                                                    Clear All
                                                </button>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="(techs, siteId) in selectedSites" :key="siteId">
                                                    <div class="inline-flex items-center gap-2 bg-white border border-green-300 rounded-lg px-3 py-1.5 shadow-sm">
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-900" x-text="sites.find(s => s.id == siteId)?.site_code || 'Unknown'"></span>
                                                            <span class="text-gray-500 mx-1">•</span>
                                                            <span class="text-green-700 font-medium" x-text="Array.isArray(techs) ? techs.join('/') : ''"></span>
                                                        </div>
                                                        <button type="button"
                                                                @click="removeSite(siteId)"
                                                                class="text-gray-400 hover:text-red-600 transition-colors flex-shrink-0"
                                                                title="Remove this site">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Search and Filters -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Sites</label>
                                                <input type="text"
                                                       x-model="searchSite"
                                                       placeholder="Search by code or name..."
                                                       class="w-full rounded-xl border border-gray-300 px-4 py-2 shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-600/20 bg-white transition-all duration-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Region</label>
                                                <select x-model="selectedRegion"
                                                        class="w-full rounded-xl border border-gray-300 px-4 py-2 shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-600/20 bg-white transition-all duration-300">
                                                    <option value="">All Regions</option>
                                                    @foreach($regions as $region)
                                                        <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->code }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Sites List (2 Column Layout) -->
                                        <div class="max-h-96 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-2 border border-green-200 rounded-lg p-4 bg-white">
                                            <template x-for="site in filteredSites" :key="site.id">
                                                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors h-fit">
                                                    <div class="flex items-start gap-3">
                                                        <input type="checkbox"
                                                               :checked="selectedSites[site.id]"
                                                               @change="toggleSite(site.id)"
                                                               class="mt-1 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900" x-text="site.display_name"></div>
                                                            <div class="text-sm text-gray-500">
                                                                <span x-text="site.region?.code || 'N/A'"></span> -
                                                                <span x-text="site.location?.location_name || 'N/A'"></span>
                                                            </div>

                                                            <!-- Technology Selection -->
                                                            <div x-show="selectedSites[site.id]"
                                                                 class="mt-2 space-y-2"
                                                                 x-transition>
                                                                <!-- FBB Only Mode -->
                                                                <template x-if="isFbbOnlyMode">
                                                                    <div>
                                                                        <div class="text-xs font-medium text-gray-600 mb-1">FBB Service:</div>
                                                                        <div class="flex gap-2 flex-wrap">
                                                                            <button type="button"
                                                                                    @click="toggleTechnology(site.id, 'FBB')"
                                                                                    :class="selectedSites[site.id] && selectedSites[site.id].includes('FBB') ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600'"
                                                                                    class="px-3 py-1 rounded-lg text-xs font-medium hover:opacity-80 transition-all">
                                                                                FBB (Supernet)
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </template>

                                                                <!-- Normal Mode (Cellular + Other Services) -->
                                                                <template x-if="!isFbbOnlyMode">
                                                                    <div>
                                                                        <div class="text-xs font-medium text-gray-600 mb-1">Cellular Technologies:</div>
                                                                        <div class="flex gap-2 flex-wrap">
                                                                            <template x-for="tech in site.technologies?.filter(t => ['2G', '3G', '4G', '5G'].includes(t.technology) && t.is_active) || []" :key="tech.technology">
                                                                                <button type="button"
                                                                                        @click="toggleTechnology(site.id, tech.technology)"
                                                                                        :class="selectedSites[site.id] && selectedSites[site.id].includes(tech.technology) ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600'"
                                                                                        class="px-3 py-1 rounded-lg text-xs font-medium hover:opacity-80 transition-all"
                                                                                        x-text="tech.technology">
                                                                                </button>
                                                                            </template>
                                                                        </div>

                                                                        <template x-if="site.technologies?.some(t => ['ILL', 'SIP', 'IPTV'].includes(t.technology) && t.is_active)">
                                                                            <div>
                                                                                <div class="text-xs font-medium text-gray-600 mb-1 mt-2">Other Services:</div>
                                                                                <div class="flex gap-2 flex-wrap">
                                                                                    <template x-for="tech in site.technologies?.filter(t => ['ILL', 'SIP', 'IPTV'].includes(t.technology) && t.is_active) || []" :key="tech.technology">
                                                                                        <button type="button"
                                                                                                @click="toggleTechnology(site.id, tech.technology)"
                                                                                                :class="selectedSites[site.id] && selectedSites[site.id].includes(tech.technology) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                                                                                                class="px-3 py-1 rounded-lg text-xs font-medium hover:opacity-80 transition-all"
                                                                                                x-text="tech.technology">
                                                                                        </button>
                                                                                    </template>
                                                                                </div>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Hidden input for form submission -->
                                                    <template x-if="selectedSites[site.id]">
                                                        <div>
                                                            <input type="hidden"
                                                                   :name="'selected_sites[' + site.id + ']'"
                                                                   :value="selectedSites[site.id] ? JSON.stringify(selectedSites[site.id]) : ''">
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            <!-- No results message -->
                                            <div x-show="filteredSites.length === 0" class="text-center py-8 text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="mt-2">No sites found</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FBB Islands Selection Section -->
                                    <div x-show="showFbbField"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="bg-purple-50/50 border border-purple-200 rounded-xl p-6 mt-4">

                                        <h5 class="font-heading text-sm font-semibold text-purple-900 mb-4 flex items-center gap-2">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Select Affected FBB Islands
                                        </h5>
                                        <p class="text-sm text-purple-700 mb-4">Search and select FBB islands affected by this outage</p>

                                        <!-- Selected FBB Islands Summary -->
                                        <div x-show="selectedFbbIslands.length > 0"
                                             x-transition
                                             class="mb-4 p-4 bg-purple-50 border border-purple-300 rounded-xl">
                                            <div class="flex items-center justify-between mb-3">
                                                <p class="text-sm font-semibold text-purple-900">
                                                    <span x-text="selectedFbbIslands.length"></span> FBB island(s) selected
                                                </p>
                                                <button type="button"
                                                        @click="selectedFbbIslands = []; document.getElementById('fbb_impacted').value = 0; updateSummary();"
                                                        class="text-xs text-red-600 hover:text-red-800 font-medium transition-colors">
                                                    Clear All
                                                </button>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="islandId in selectedFbbIslands" :key="islandId">
                                                    <div class="inline-flex items-center gap-2 bg-white border border-purple-300 rounded-lg px-3 py-1.5 shadow-sm">
                                                        <div class="text-xs">
                                                            <span class="font-semibold text-gray-900" x-text="fbbIslands.find(i => i.id == islandId)?.full_name || 'Unknown'"></span>
                                                        </div>
                                                        <button type="button"
                                                                @click="removeFbbIsland(islandId)"
                                                                class="text-gray-400 hover:text-red-600 transition-colors flex-shrink-0"
                                                                title="Remove this island">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Search and Filters -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Islands</label>
                                                <input type="text"
                                                       x-model="searchFbbIsland"
                                                       placeholder="Search by island name..."
                                                       class="w-full rounded-xl border border-gray-300 px-4 py-2 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-white transition-all duration-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Region</label>
                                                <select x-model="selectedFbbRegion"
                                                        class="w-full rounded-xl border border-gray-300 px-4 py-2 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 bg-white transition-all duration-300">
                                                    <option value="">All Regions</option>
                                                    @foreach($regions as $region)
                                                        <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->code }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- FBB Islands List -->
                                        <div class="max-h-96 overflow-y-auto grid grid-cols-1 md:grid-cols-3 gap-2 border border-purple-200 rounded-lg p-4 bg-white">
                                            <template x-for="island in filteredFbbIslands" :key="island.id">
                                                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                                    <label class="flex items-start gap-3 cursor-pointer">
                                                        <input type="checkbox"
                                                               :checked="selectedFbbIslands.includes(island.id)"
                                                               @change="toggleFbbIsland(island.id)"
                                                               class="mt-1 h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900" x-text="island.island_name"></div>
                                                            <div class="text-xs text-gray-500">
                                                                <span x-text="island.region?.code || 'N/A'"></span>
                                                            </div>
                                                            <div class="mt-1">
                                                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800" x-text="island.technology"></span>
                                                            </div>
                                                        </div>
                                                    </label>

                                                    <!-- Hidden input for form submission -->
                                                    <template x-if="selectedFbbIslands.includes(island.id)">
                                                        <div>
                                                            <input type="hidden"
                                                                   name="selected_fbb_islands[]"
                                                                   :value="island.id">
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            <!-- No results message -->
                                            <div x-show="filteredFbbIslands.length === 0" class="col-span-full text-center py-8 text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="mt-2">No FBB islands found</p>
                                            </div>
                                        </div>
                                    </div>

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

                                <!-- Step 2: Categorization & Summary (Two Column Layout) -->
                                <div class="mt-8">
                                    <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700">Step 2: Categorize & Describe</h4>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <!-- Left Column: Categories -->
                                        <div class="space-y-4">
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

                                        <!-- Right Column: Summary -->
                                        <div>
                                            <label for="summary" class="block text-sm font-heading font-medium text-gray-700 mb-2">Outage
                                                Details (Incident Summary) <span class="text-red-500">*</span></label>
                                            <p class="text-xs text-gray-500 mb-2">Auto-fills when sites or FBB islands are selected, or enter manually</p>
                                            <textarea name="summary" id="summary" rows="8" maxlength="1000"
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

        // Handle duplicate confirmation
        function confirmDuplicate() {
            document.getElementById('confirm_duplicate').value = '1';
            document.getElementById('incident-form').submit();
        }
    </script>
@endsection
