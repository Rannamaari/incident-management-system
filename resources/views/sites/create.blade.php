@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-heading text-2xl lg:text-3xl font-bold">Create New Site</h2>
        <a href="{{ route('sites.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
        </a>
    </div>
@endsection

@section('content')
    <div class="py-4 sm:py-6 lg:py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl sm:rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-4 sm:p-6 lg:p-8">
                <form method="POST" action="{{ route('sites.store') }}"
                      x-data="{
                          selectedRegion: '{{ old('region_id') }}',
                          siteType: '{{ old('site_type', 'End Site') }}',
                          regions: {{ Js::from($regions) }},

                          get showHubSitesDropdown() {
                              return this.siteType === 'End Site';
                          },

                          get generatedSiteCodePreview() {
                              if (!this.selectedRegion) {
                                  return 'Select region to preview site code';
                              }

                              const region = this.regions.find(r => r.id == this.selectedRegion);

                              if (region) {
                                  return `${region.code}-XXX (e.g., ${region.code}-001, ${region.code}-002)`;
                              }

                              return 'Preview not available';
                          }
                      }"
                      class="space-y-6 sm:space-y-8">
                    @csrf

                    <!-- Site Code Preview -->
                    <div class="bg-blue-50 border border-blue-200 dark:border-blue-700 rounded-xl p-3 sm:p-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Site Code (Auto-generated)</p>
                                <p class="text-lg font-bold text-blue-900" x-text="generatedSiteCodePreview"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Region & Site Name -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Region <span class="text-red-500">*</span>
                            </label>
                            <select name="region_id"
                                    x-model="selectedRegion"
                                    required
                                    class="w-full rounded-xl sm:rounded-2xl border px-4 py-3 sm:py-3.5 text-base @error('region_id') border-red-300 dark:border-red-700 @else border-gray-300 dark:border-gray-600 @enderror touch-manipulation">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->code }}</option>
                                @endforeach
                            </select>
                            @error('region_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Site Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="site_name"
                                   value="{{ old('site_name') }}"
                                   required
                                   class="w-full rounded-xl sm:rounded-2xl border px-4 py-3 sm:py-3.5 text-base @error('site_name') border-red-300 dark:border-red-700 @else border-gray-300 dark:border-gray-600 @enderror touch-manipulation"
                                   placeholder="e.g., Bodufulhadhoo">
                            @error('site_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter the name of the site/island</p>
                        </div>
                    </div>

                    <!-- Display Name & Transmission/Backhaul -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Display Name <span class="text-gray-500 dark:text-gray-400">(Optional)</span>
                            </label>
                            <input type="text"
                                   name="display_name"
                                   value="{{ old('display_name') }}"
                                   class="w-full rounded-xl sm:rounded-2xl border border-gray-300 dark:border-gray-600 px-4 py-3 sm:py-3.5 text-base @error('display_name') border-red-300 dark:border-red-700 @enderror touch-manipulation"
                                   placeholder="Leave empty for auto-generated">
                            @error('display_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If left empty, site code will be used</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Transmission / Backhaul <span class="text-gray-500 dark:text-gray-400">(Optional)</span>
                            </label>
                            <input type="text"
                                   name="transmission_backhaul"
                                   value="{{ old('transmission_backhaul') }}"
                                   class="w-full rounded-xl sm:rounded-2xl border border-gray-300 dark:border-gray-600 px-4 py-3 sm:py-3.5 text-base @error('transmission_backhaul') border-red-300 dark:border-red-700 @enderror touch-manipulation"
                                   placeholder="e.g., Fiber, Microwave">
                            @error('transmission_backhaul')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Backhaul technology</p>
                        </div>
                    </div>

                    <!-- Site Settings Checkboxes -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Site Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                            <label class="flex items-start gap-3 p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-green-300 dark:border-green-700 hover:bg-green-50 cursor-pointer transition-colors active:scale-[0.98] touch-manipulation">
                                <input type="checkbox"
                                       name="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="mt-1 h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500 dark:focus:ring-green-400 touch-manipulation">
                                <div class="flex-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Active Site</span>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Site is operational. If unchecked, will be marked as temporary</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-colors active:scale-[0.98] touch-manipulation">
                                <input type="checkbox"
                                       name="is_link_site"
                                       {{ old('is_link_site') ? 'checked' : '' }}
                                       class="mt-1 h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400 touch-manipulation">
                                <div class="flex-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Link Site</span>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Site is used as a network link/relay point</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Site Type -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Site Type <span class="text-red-500">*</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
                                   :class="siteType === 'End Site' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:border-gray-600'">
                                <input type="radio"
                                       name="site_type"
                                       value="End Site"
                                       x-model="siteType"
                                       {{ old('site_type', 'End Site') === 'End Site' ? 'checked' : '' }}
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 dark:border-gray-600 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <div class="flex-1">
                                    <span class="text-base font-bold text-gray-900 dark:text-gray-100">End Site</span>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Standard site that connects to hub sites for backhaul</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
                                   :class="siteType === 'Hub Site' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:border-gray-600'">
                                <input type="radio"
                                       name="site_type"
                                       value="Hub Site"
                                       x-model="siteType"
                                       {{ old('site_type') === 'Hub Site' ? 'checked' : '' }}
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 dark:border-gray-600 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <div class="flex-1">
                                    <span class="text-base font-bold text-gray-900 dark:text-gray-100">Hub Site</span>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Central site that provides backhaul to multiple end sites</p>
                                </div>
                            </label>
                        </div>
                        @error('site_type')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Hub Sites Connection (Conditional) -->
                    <div x-show="showHubSitesDropdown"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-data="{
                             searchQuery: '',
                             selectedHubs: {{ json_encode(old('hub_sites', [])) }},
                             hubSites: {{ Js::from($hubSites->map(fn($h) => ['id' => $h->id, 'code' => $h->site_code, 'name' => $h->display_name])) }},

                             get filteredHubs() {
                                 if (!this.searchQuery) return this.hubSites;
                                 const query = this.searchQuery.toLowerCase();
                                 return this.hubSites.filter(hub =>
                                     hub.code.toLowerCase().includes(query) ||
                                     hub.name.toLowerCase().includes(query)
                                 );
                             },

                             get selectedHubsData() {
                                 return this.hubSites.filter(hub => this.selectedHubs.includes(hub.id));
                             },

                             toggleHub(hubId) {
                                 if (this.selectedHubs.includes(hubId)) {
                                     this.selectedHubs = this.selectedHubs.filter(id => id !== hubId);
                                 } else {
                                     this.selectedHubs.push(hubId);
                                 }
                             },

                             removeHub(hubId) {
                                 this.selectedHubs = this.selectedHubs.filter(id => id !== hubId);
                             }
                         }"
                         class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Connected to Hub Sites</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select which hub sites this end site connects to for backhaul</p>

                        @if($hubSites->count() > 0)
                            <!-- Selected Hub Sites (Top) -->
                            <div x-show="selectedHubs.length > 0" class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                        Selected Hub Sites (<span x-text="selectedHubs.length"></span>)
                                    </label>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="hub in selectedHubsData" :key="hub.id">
                                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold" x-text="hub.code"></span>
                                                <span class="text-xs opacity-90" x-text="hub.name"></span>
                                            </div>
                                            <button type="button" @click="removeHub(hub.id)"
                                                class="ml-1 p-0.5 hover:bg-blue-800 rounded-full transition-colors touch-manipulation">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <input type="hidden" name="hub_sites[]" :value="hub.id">
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Search Bar -->
                            <div class="mb-3">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input type="text"
                                        x-model="searchQuery"
                                        placeholder="Search hub sites by code or name..."
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 pl-10 pr-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400/20 touch-manipulation">
                                    <button type="button"
                                        x-show="searchQuery"
                                        @click="searchQuery = ''"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Available Hub Sites List -->
                            <div class="space-y-2 max-h-64 overflow-y-auto border rounded-xl p-3 bg-gray-50">
                                <template x-for="hub in filteredHubs" :key="hub.id">
                                    <label class="flex items-center gap-3 p-3 hover:bg-white dark:bg-gray-800 rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-200 dark:border-blue-700 active:scale-[0.98] touch-manipulation"
                                        :class="selectedHubs.includes(hub.id) ? 'bg-blue-50 border-blue-300' : 'bg-white dark:bg-gray-800'">
                                        <input type="checkbox"
                                               :checked="selectedHubs.includes(hub.id)"
                                               @change="toggleHub(hub.id)"
                                               class="h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400 touch-manipulation">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="hub.code"></span>
                                                <span x-show="selectedHubs.includes(hub.id)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-600 text-white">
                                                    Selected
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="hub.name"></p>
                                        </div>
                                    </label>
                                </template>
                                <div x-show="filteredHubs.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm">No hub sites found matching "<span x-text="searchQuery"></span>"</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No hub sites available yet.</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Create hub sites first to connect end sites to them.</p>
                            </div>
                        @endif
                        @error('hub_sites')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        @error('hub_sites.*')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Remarks <span class="text-gray-500 dark:text-gray-400">(Optional)</span>
                        </label>
                        <textarea name="remarks"
                                  rows="4"
                                  class="w-full rounded-xl sm:rounded-2xl border border-gray-300 dark:border-gray-600 px-4 py-3 sm:py-3.5 text-base @error('remarks') border-red-300 dark:border-red-700 @enderror touch-manipulation"
                                  placeholder="Add any additional notes or remarks about this site...">{{ old('remarks') }}</textarea>
                        @error('remarks')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-6 border-t">
                        <a href="{{ route('sites.index') }}"
                           class="order-2 sm:order-1 text-center rounded-xl border-2 border-gray-300 dark:border-gray-600 px-6 py-3.5 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors touch-manipulation">
                            Cancel
                        </a>
                        <button type="submit"
                                class="order-1 sm:order-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-3.5 text-base font-semibold text-white hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl touch-manipulation">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Site
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
