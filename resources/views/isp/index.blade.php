@extends('layouts.app')

@section('content')
<div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">ISP Links</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage ISP backhaul links and escalation contacts</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('isp.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Dashboard
            </a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('isp.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Link
                </a>
            @endif
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 p-4 sm:p-5 mb-6">
        <form method="GET" action="{{ route('isp.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Search
                    </label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="ISP name, circuit ID, locations..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status
                    </label>
                    <select name="status"
                            id="status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                        <option value="">All Statuses</option>
                        <option value="Up" {{ request('status') == 'Up' ? 'selected' : '' }}>Up</option>
                        <option value="Down" {{ request('status') == 'Down' ? 'selected' : '' }}>Down</option>
                        <option value="Degraded" {{ request('status') == 'Degraded' ? 'selected' : '' }}>Degraded</option>
                    </select>
                </div>

                {{-- Link Type Filter --}}
                <div>
                    <label for="link_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Link Type
                    </label>
                    <select name="link_type"
                            id="link_type"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                        <option value="">All Types</option>
                        <option value="Backhaul" {{ request('link_type') == 'Backhaul' ? 'selected' : '' }}>Backhaul</option>
                        <option value="Peering" {{ request('link_type') == 'Peering' ? 'selected' : '' }}>Peering</option>
                        <option value="Backup" {{ request('link_type') == 'Backup' ? 'selected' : '' }}>Backup</option>
                    </select>
                </div>
            </div>

            {{-- Filter Buttons --}}
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply Filters
                </button>
                <a href="{{ route('isp.index') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    {{-- ISP Links Table --}}
    @if($ispLinks->count() > 0)
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-slate-800">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Circuit ID</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ISP Name</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Locations</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total (Gbps)</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current (Gbps)</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lost (Gbps)</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Availability</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($ispLinks as $link)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors {{ $link->active_incidents_count > 0 ? 'bg-red-50/30 dark:bg-red-950/10' : '' }}">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('isp.show', $link) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline font-medium">
                                            {{ $link->circuit_id }}
                                        </a>
                                        @if($link->active_incidents_count > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300" title="Active outages">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $link->active_incidents_count }} {{ $link->active_incidents_count === 1 ? 'outage' : 'outages' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $link->isp_name }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $link->link_type }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center gap-1">
                                        <span class="truncate max-w-[150px]">{{ $link->location_a }}</span>
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                        <span class="truncate max-w-[150px]">{{ $link->location_b }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300 tabular-nums">
                                    {{ number_format($link->total_capacity_gbps, 2) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300 tabular-nums">
                                    {{ number_format($link->current_capacity_gbps, 2) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right tabular-nums">
                                    @php
                                        $activeCapacityLost = $link->getActiveIncidentsCapacityLost();
                                        $displayCapacityLost = $activeCapacityLost > 0 ? $activeCapacityLost : $link->lost_capacity_gbps;
                                    @endphp
                                    <span class="{{ $displayCapacityLost > 0 ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ number_format($displayCapacityLost, 2) }}
                                    </span>
                                    @if($activeCapacityLost > 0)
                                        <span class="block text-xs text-red-500 dark:text-red-400 mt-0.5">from incidents</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right tabular-nums
                                    @if($link->availability_percentage >= 95) text-green-600 dark:text-green-400
                                    @elseif($link->availability_percentage >= 90) text-amber-600 dark:text-amber-400
                                    @else text-red-600 dark:text-red-400
                                    @endif font-medium">
                                    {{ number_format($link->availability_percentage, 2) }}%
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if($link->hasActiveIncidents())
                                        <div class="flex flex-col gap-1">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 border border-red-300 dark:border-red-700 animate-pulse">
                                                ⚠️ Under Incident
                                            </span>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $link->status_color_class }} opacity-60">
                                                Base: {{ $link->status }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $link->status_color_class }}">
                                            {{ $link->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('isp.show', $link) }}"
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            View
                                        </a>
                                        @if(Auth::user()->canEditIncidents())
                                            <a href="{{ route('incidents.create', ['isp_link_id' => $link->id]) }}"
                                               class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                               title="Report outage for this link">
                                                Report
                                            </a>
                                        @endif
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('isp.edit', $link) }}"
                                               class="text-amber-600 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('isp.destroy', $link) }}" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this ISP link?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-gray-200 dark:border-white/10 px-4 py-3">
                {{ $ispLinks->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No ISP links found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if(request()->hasAny(['search', 'status', 'link_type']))
                    No ISP links match your current filters. Try adjusting your search criteria.
                @else
                    Get started by creating a new ISP link.
                @endif
            </p>
            @if(Auth::user()->isAdmin())
                <div class="mt-6">
                    <a href="{{ route('isp.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add New ISP Link
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
