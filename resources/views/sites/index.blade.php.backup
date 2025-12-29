@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Monitored Sites
            </h2>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base lg:text-lg text-gray-600 font-medium">Active and monitoring sites</p>
        </div>

        <div class="flex gap-2 sm:gap-3">
            @if(Auth::user()->canManageSites())
                <a href="{{ route('sites.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-5 py-2.5 sm:py-3 font-heading font-semibold text-sm sm:text-base text-white shadow-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:-translate-y-0.5 touch-manipulation">
                    <svg class="h-5 w-5 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New Site</span>
                </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="py-4 sm:py-6" x-data="{ showFilters: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="mb-4 sm:mb-6 rounded-2xl sm:rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <form method="GET" action="{{ route('sites.index') }}" class="p-4 sm:p-6 space-y-4">
                    <!-- Search Bar -->
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by Site Code, Name, or Region"
                            class="w-full rounded-xl sm:rounded-2xl border border-gray-300/50 pl-12 pr-4 py-3 sm:py-3.5 text-sm sm:text-base shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 touch-manipulation">
                    </div>

                    <!-- Quick Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <select name="region" class="rounded-xl border border-gray-300/50 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base touch-manipulation">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>
                                    {{ $region->code }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" class="rounded-xl border border-gray-300/50 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base touch-manipulation">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <select name="site_type" class="rounded-xl border border-gray-300/50 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base touch-manipulation">
                            <option value="">All Types</option>
                            <option value="hub" {{ request('site_type') == 'hub' ? 'selected' : '' }}>Hub Sites</option>
                            <option value="end" {{ request('site_type') == 'end' ? 'selected' : '' }}>End Sites</option>
                        </select>

                        <button type="button" @click="showFilters = !showFilters"
                            class="rounded-xl border-2 border-blue-600 bg-white px-4 py-2.5 sm:py-3 text-sm sm:text-base font-semibold text-blue-600 hover:bg-blue-50 transition-colors touch-manipulation flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <span x-text="showFilters ? 'Hide Filters' : 'More Filters'"></span>
                        </button>
                    </div>

                    <!-- Advanced Filters (Collapsible) -->
                    <div x-show="showFilters"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="pt-4 border-t space-y-4">

                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Advanced Filters</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Link Site Filter -->
                            <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 bg-blue-50/50">
                                <input type="checkbox" name="is_link_site" id="filter_link" value="1" {{ request('is_link_site') ? 'checked' : '' }}
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 touch-manipulation">
                                <label for="filter_link" class="flex-1 cursor-pointer text-sm font-medium text-gray-900">
                                    Link Sites Only
                                </label>
                            </div>

                            <!-- Temp Site Filter -->
                            <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 bg-orange-50/50">
                                <input type="checkbox" name="is_temp_site" id="filter_temp" value="1" {{ request('is_temp_site') ? 'checked' : '' }}
                                    class="h-5 w-5 rounded border-gray-300 text-orange-600 focus:ring-orange-500 touch-manipulation">
                                <label for="filter_temp" class="flex-1 cursor-pointer text-sm font-medium text-gray-900">
                                    Temporary Sites Only
                                </label>
                            </div>
                        </div>

                        <!-- Technologies Filter -->
                        <div class="p-4 rounded-xl border-2 border-gray-200 bg-gray-50/50">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Filter by Technologies:</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach(['2G', '3G', '4G', '5G', 'ILL', 'SIP', 'IPTV', 'NCIT'] as $tech)
                                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:bg-blue-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="technologies[]" value="{{ $tech }}"
                                            {{ in_array($tech, request('technologies', [])) ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 touch-manipulation">
                                        <span class="text-sm font-medium text-gray-900">{{ $tech }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 sm:flex-none rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 text-sm sm:text-base font-heading font-semibold text-white shadow-lg hover:from-blue-700 hover:to-blue-800 transition-all touch-manipulation">
                            Apply Filters
                        </button>
                        <a href="{{ route('sites.index') }}" class="flex-1 sm:flex-none rounded-xl border-2 border-gray-300 bg-white px-6 py-3 text-sm sm:text-base font-heading font-semibold text-gray-700 text-center hover:bg-gray-50 transition-colors touch-manipulation">
                            Clear All
                        </a>
                    </div>
                </form>
            </div>

            <!-- Mobile Card View (Hidden on Desktop) -->
            <div class="lg:hidden space-y-4">
                @forelse($sites as $site)
                    <div class="rounded-2xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg p-4 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <a href="{{ route('sites.show', $site) }}" class="text-lg font-heading font-bold text-blue-600 hover:text-blue-800">
                                    {{ $site->site_code }}
                                </a>
                                <p class="text-sm text-gray-600 mt-1">{{ $site->site_name ?: $site->display_name }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold
                                @if($site->site_type === 'Hub Site') bg-gradient-to-r from-blue-600 to-blue-700 text-white
                                @else bg-gray-200 text-gray-700
                                @endif">
                                @if($site->site_type === 'Hub Site')
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                @endif
                                {{ $site->site_type === 'Hub Site' ? 'Hub' : 'End' }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-3">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-gray-500">Region:</span>
                                <span class="font-medium text-gray-900">{{ $site->region->code }}</span>
                            </div>

                            <div class="flex flex-wrap gap-1.5">
                                @foreach($site->technologies->where('is_active', true) as $tech)
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ $tech->technology }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mb-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                @if($site->is_active) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $site->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($site->is_link_site)
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    Link Site
                                </span>
                            @endif
                            @if($site->is_temp_site)
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                    Temp
                                </span>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('sites.show', $site) }}" class="flex-1 text-center rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 px-4 py-2.5 text-sm font-semibold text-white hover:from-gray-700 hover:to-gray-800 transition-all touch-manipulation">
                                View
                            </a>
                            @if(Auth::user()->canManageSites())
                                <a href="{{ route('sites.edit', $site) }}" class="flex-1 text-center rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:from-blue-700 hover:to-blue-800 transition-all touch-manipulation">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 rounded-2xl border border-gray-200 bg-white/50">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="mt-4 text-base font-medium text-gray-900">No sites found</p>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View (Hidden on Mobile) -->
            <div class="hidden lg:block overflow-hidden rounded-2xl sm:rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/50">
                        <thead class="bg-gradient-to-r from-slate-50/80 to-white/60">
                            <tr>
                                <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Site Code</th>
                                <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Region</th>
                                <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Site Name</th>
                                <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Technologies</th>
                                <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Flags</th>
                                <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Type</th>
                                <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Status</th>
                                <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/30 bg-white/60">
                            @forelse($sites as $site)
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-4 py-4">
                                        <a href="{{ route('sites.show', $site) }}" class="text-blue-600 hover:text-blue-800 font-heading font-semibold hover:underline">
                                            {{ $site->site_code }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $site->region->code }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $site->site_name ?: $site->display_name }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($site->technologies->where('is_active', true) as $tech)
                                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800">
                                                    {{ $tech->technology }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @if($site->is_link_site)
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Link</span>
                                            @endif
                                            @if($site->is_temp_site)
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">Temp</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold
                                            @if($site->site_type === 'Hub Site') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            @if($site->site_type === 'Hub Site')
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            @endif
                                            {{ $site->site_type === 'Hub Site' ? 'Hub' : 'End' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                            @if($site->is_active) bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $site->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('sites.show', $site) }}" class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-gray-600 to-gray-700 px-3 py-1.5 text-xs font-semibold text-white hover:from-gray-700 hover:to-gray-800 transition-all">
                                                View
                                            </a>
                                            @if(Auth::user()->canManageSites())
                                                <a href="{{ route('sites.edit', $site) }}" class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-3 py-1.5 text-xs font-semibold text-white hover:from-blue-700 hover:to-blue-800 transition-all">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p class="mt-4 text-base font-medium text-gray-900">No sites found</p>
                                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($sites->hasPages())
                <div class="mt-6">{{ $sites->links() }}</div>
            @endif
        </div>
    </div>
@endsection
