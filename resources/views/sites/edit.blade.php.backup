@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold">Edit Site</h2>
            <p class="mt-2 text-lg text-gray-600">{{ $site->site_code }} - {{ $site->display_name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sites.show', $site) }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white">
                Cancel
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('sites.update', $site) }}"
                  x-data="{
                      siteType: '{{ old('site_type', $site->site_type) }}'
                  }"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Site Information (Read-only) -->
                <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 mb-6">Site Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Site Code</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->site_code }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Site Number</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->site_number }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Display Name</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->display_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Region</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->region->name }} ({{ $site->region->code }})</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Site Name</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->site_name ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Site Settings -->
                <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 mb-6">Site Settings</h3>

                    <div class="space-y-4">
                        <!-- Active Status -->
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-gray-50">
                            <input type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ $site->is_active ? 'checked' : '' }}
                                   class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_active" class="flex-1 cursor-pointer">
                                <span class="block text-sm font-medium text-gray-900">Active Site</span>
                                <span class="text-xs text-gray-500">Enable this site for monitoring and incident tracking. Inactive sites are hidden from incident creation.</span>
                            </label>
                        </div>

                        <!-- Link Site -->
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-50">
                            <input type="checkbox"
                                   name="is_link_site"
                                   id="is_link_site"
                                   value="1"
                                   {{ $site->is_link_site ? 'checked' : '' }}
                                   class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_link_site" class="flex-1 cursor-pointer">
                                <span class="block text-sm font-medium text-gray-900">Link Site</span>
                                <span class="text-xs text-gray-500">This site is used as a network link/relay point</span>
                            </label>
                        </div>

                        <!-- Transmission / Backhaul -->
                        <div class="p-4 rounded-xl bg-gray-50">
                            <label for="transmission_backhaul" class="block text-sm font-medium text-gray-900 mb-2">
                                Transmission / Backhaul
                            </label>
                            <input type="text"
                                   name="transmission_backhaul"
                                   id="transmission_backhaul"
                                   value="{{ old('transmission_backhaul', $site->transmission_backhaul) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., Fiber, Microwave, Satellite">
                            <p class="mt-1 text-xs text-gray-500">Specify the backhaul technology for this site</p>
                        </div>

                        <!-- Remarks -->
                        <div class="p-4 rounded-xl bg-gray-50">
                            <label for="remarks" class="block text-sm font-medium text-gray-900 mb-2">
                                Remarks
                            </label>
                            <textarea name="remarks"
                                      id="remarks"
                                      rows="3"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Additional notes or remarks about this site">{{ old('remarks', $site->remarks) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Any additional information or special notes about this site</p>
                        </div>
                    </div>
                </div>

                <!-- Site Type & Hub Connections -->
                <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 mb-6">Site Type & Hub Connections</h3>

                    <!-- Site Type Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-900 mb-3">
                            Site Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
                                   :class="siteType === 'End Site' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio"
                                       name="site_type"
                                       value="End Site"
                                       x-model="siteType"
                                       {{ old('site_type', $site->site_type) === 'End Site' ? 'checked' : '' }}
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="text-sm font-bold text-gray-900">End Site</span>
                                    <p class="text-xs text-gray-600 mt-1">Standard site that connects to hub sites for backhaul</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
                                   :class="siteType === 'Hub Site' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio"
                                       name="site_type"
                                       value="Hub Site"
                                       x-model="siteType"
                                       {{ old('site_type', $site->site_type) === 'Hub Site' ? 'checked' : '' }}
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="text-sm font-bold text-gray-900">Hub Site</span>
                                    <p class="text-xs text-gray-600 mt-1">Central site that provides backhaul to multiple end sites</p>
                                </div>
                            </label>
                        </div>
                        @error('site_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Hub Sites Multi-Select (show for End Sites) -->
                    <div x-show="siteType === 'End Site'"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-data="{
                             searchQuery: '',
                             selectedHubs: {{ json_encode(old('hub_sites', $site->hubSites->pluck('id')->toArray())) }},
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
                         }">
                        <label class="block text-sm font-medium text-gray-900 mb-3">
                            Connected to Hub Sites
                        </label>
                        <p class="text-xs text-gray-500 mb-3">
                            Select which hub sites this end site connects to for backhaul
                        </p>

                        @if($hubSites->count() > 0)
                            <!-- Selected Hub Sites (Top) -->
                            <div x-show="selectedHubs.length > 0" class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
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
                                        class="w-full rounded-xl border border-gray-300 pl-10 pr-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 touch-manipulation">
                                    <button type="button"
                                        x-show="searchQuery"
                                        @click="searchQuery = ''"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Available Hub Sites List -->
                            <div class="space-y-2 max-h-64 overflow-y-auto border rounded-xl p-3 bg-gray-50">
                                <template x-for="hub in filteredHubs" :key="hub.id">
                                    <label class="flex items-center gap-3 p-3 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-200 active:scale-[0.98] touch-manipulation"
                                        :class="selectedHubs.includes(hub.id) ? 'bg-blue-50 border-blue-300' : 'bg-white'">
                                        <input type="checkbox"
                                               :checked="selectedHubs.includes(hub.id)"
                                               @change="toggleHub(hub.id)"
                                               class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 touch-manipulation">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-bold text-gray-900" x-text="hub.code"></span>
                                                <span x-show="selectedHubs.includes(hub.id)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-600 text-white">
                                                    Selected
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600" x-text="hub.name"></p>
                                        </div>
                                    </label>
                                </template>
                                <div x-show="filteredHubs.length === 0" class="text-center py-8 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm">No hub sites found matching "<span x-text="searchQuery"></span>"</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <p class="text-sm text-gray-600">No hub sites available</p>
                            </div>
                        @endif
                        @error('hub_sites')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Show Connected Sites (if this is a Hub Site) -->
                    <div x-show="siteType === 'Hub Site'"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100">
                        <label class="block text-sm font-medium text-gray-900 mb-3">
                            Sites Connected to This Hub
                        </label>
                        @if($site->connectedSites->count() > 0)
                            <div class="space-y-2">
                                @foreach($site->connectedSites as $connectedSite)
                                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">{{ $connectedSite->site_code }}</p>
                                            <p class="text-xs text-gray-600">{{ $connectedSite->display_name }}</p>
                                        </div>
                                        <a href="{{ route('sites.show', $connectedSite) }}"
                                           class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            View →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <p class="text-sm text-gray-600">No sites connected to this hub yet</p>
                                <p class="text-xs text-gray-500 mt-1">End sites can select this hub from their connection settings</p>
                            </div>
                        @endif
                        <p class="mt-2 text-xs text-gray-500">
                            <strong>Note:</strong> End sites manage their hub connections from their own edit pages.
                        </p>
                    </div>
                </div>

                <!-- Technologies -->
                <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 mb-6">Available Technologies</h3>

                    <div class="space-y-6">
                        <!-- Cellular Technologies -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Cellular Technologies</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($site->technologies->whereIn('technology', ['2G', '3G', '4G', '5G']) as $tech)
                                    <div class="flex items-center gap-2 p-3 rounded-lg border {{ $tech->is_active ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                                        <input type="checkbox"
                                               name="technologies[{{ $tech->id }}]"
                                               id="tech_{{ $tech->id }}"
                                               value="1"
                                               {{ $tech->is_active ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <label for="tech_{{ $tech->id }}" class="text-sm font-medium text-gray-900 cursor-pointer">
                                            {{ $tech->technology }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Other Services -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Other Services</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($site->technologies->whereIn('technology', ['ILL', 'SIP', 'IPTV', 'NCIT']) as $tech)
                                    <div class="flex items-center gap-2 p-3 rounded-lg border {{ $tech->is_active ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                                        <input type="checkbox"
                                               name="technologies[{{ $tech->id }}]"
                                               id="tech_{{ $tech->id }}"
                                               value="1"
                                               {{ $tech->is_active ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <label for="tech_{{ $tech->id }}" class="text-sm font-medium text-gray-900 cursor-pointer">
                                            {{ $tech->technology }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-gray-500">
                        <strong>Note:</strong> Enabled technologies will be available for selection when creating incidents for this site.
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('sites.show', $site) }}"
                       class="inline-flex items-center gap-2 rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-semibold text-white hover:from-blue-700 hover:to-blue-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
