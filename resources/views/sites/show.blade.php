@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="font-heading text-2xl lg:text-3xl font-bold">{{ $site->site_code }}</h2>
                <span class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-bold
                    @if($site->site_type === 'Hub Site') bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg
                    @else bg-gray-200 text-gray-700 dark:text-gray-300
                    @endif">
                    @if($site->site_type === 'Hub Site')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    @endif
                    {{ $site->site_type }}
                </span>
            </div>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">{{ $site->display_name }}</p>
        </div>
        <div class="flex gap-3">
            @if(Auth::user()->canManageSites())
                <a href="{{ route('sites.edit', $site) }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-2.5 text-white shadow-lg hover:from-blue-700 hover:to-blue-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('sites.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white hover:bg-gray-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-4 sm:py-6 lg:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            <!-- Site Information Card -->
            <div class="overflow-hidden rounded-2xl sm:rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-4 sm:p-6 lg:p-8">
                <h3 class="text-base sm:text-lg font-heading font-bold text-gray-900 dark:text-gray-100 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Site Information
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Left Column -->
                    <div class="space-y-3 sm:space-y-4 lg:space-y-6">
                        <div class="p-3 sm:p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Site Code</dt>
                            <dd class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">{{ $site->site_code }}</dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Display Name</dt>
                            <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $site->display_name }}</dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Region</dt>
                            <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $site->region->name }} ({{ $site->region->code }})</dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Site Name</dt>
                            <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $site->site_name ?: 'N/A' }}</dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Transmission / Backhaul</dt>
                            <dd class="text-base text-gray-900 dark:text-gray-100">
                                {{ $site->transmission_backhaul ?: 'Not specified' }}
                            </dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Site Type</dt>
                            <dd>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                    @if($site->site_type === 'Hub Site') bg-blue-100 text-blue-800
                                    @else bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400
                                    @endif">
                                    {{ $site->site_type }}
                                </span>
                            </dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Link Site</dt>
                            <dd>
                                @if($site->is_link_site)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Yes
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400">No</span>
                                @endif
                            </dd>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Site Number</dt>
                            <dd class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $site->site_number }}</dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Available Technologies</dt>
                            <dd class="flex flex-wrap gap-2">
                                @foreach($site->technologies as $tech)
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                        @if($tech->is_active) bg-green-100 text-green-800
                                        @else bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 line-through
                                        @endif">
                                        {{ $tech->technology }}
                                        @if(!$tech->is_active) (Disabled) @endif
                                    </span>
                                @endforeach
                            </dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Status</dt>
                            <dd>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                    @if($site->is_active) bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $site->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Last Updated</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $site->updated_at->timezone('Indian/Maldives')->format('M d, Y - h:i A') }}</dd>
                        </div>
                    </div>
                </div>

                <!-- Remarks (Full Width) -->
                <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <dt class="text-xs font-medium text-amber-900 uppercase mb-2 flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        Remarks
                    </dt>
                    <dd class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                        {{ $site->remarks ?: 'No remarks added.' }}
                    </dd>
                </div>

                @if(Auth::user()->canManageSites())
                    <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t">
                        <form method="POST" action="{{ route('sites.destroy', $site) }}" onsubmit="return confirm('Are you sure you want to delete this site?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full sm:w-auto rounded-xl sm:rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 sm:py-3.5 text-sm sm:text-base font-semibold text-white hover:from-red-700 hover:to-red-800 transition-all touch-manipulation">
                                Delete Site
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Hub Site Connections -->
            @if($site->site_type === 'Hub Site' && $site->connectedSites->count() > 0)
            <div class="overflow-hidden rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-8">
                <h3 class="text-lg font-heading font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Sites Connected to This Hub
                    <span class="ml-auto text-xs font-normal text-gray-500 dark:text-gray-400">({{ $site->connectedSites->count() }} sites)</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($site->connectedSites as $connectedSite)
                        <a href="{{ route('sites.show', $connectedSite) }}"
                           class="block p-4 rounded-xl border-2 border-blue-200 dark:border-blue-700 bg-blue-50 hover:bg-blue-100 hover:border-blue-300 transition-colors">
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $connectedSite->site_code }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $connectedSite->display_name }}</p>
                            <div class="flex gap-2 mt-2">
                                @if($connectedSite->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @endif
                                @if($connectedSite->is_link_site)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Link Site
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($site->hubSites->count() > 0)
            <div class="overflow-hidden rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-8">
                <h3 class="text-lg font-heading font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Connected to Hub Sites
                </h3>

                <div class="flex flex-wrap gap-3">
                    @foreach($site->hubSites as $hubSite)
                        <a href="{{ route('sites.show', $hubSite) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ $hubSite->site_code }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Last Outages -->
            @php
                $lastOutages = $site->incidents()
                    ->orderBy('started_at', 'desc')
                    ->limit(10)
                    ->get();
            @endphp

            <div class="overflow-hidden rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-8">
                <h3 class="text-lg font-heading font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Recent Outages
                    @if($lastOutages->count() > 0)
                        <span class="ml-auto text-xs font-normal text-gray-500 dark:text-gray-400">(Last {{ $lastOutages->count() }} incidents)</span>
                    @endif
                </h3>

                @if($lastOutages->count() > 0)
                    <div class="space-y-3">
                        @foreach($lastOutages as $incident)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $incident->summary }}</p>
                                        <div class="flex flex-wrap gap-2 text-xs">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Started: {{ $incident->started_at ? $incident->started_at->timezone('Indian/Maldives')->format('M d, Y H:i') : 'N/A' }}
                                            </span>
                                            @if($incident->duration_hms)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-orange-100 text-orange-700 font-medium">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Duration: {{ $incident->duration_hms }}
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-100 text-blue-700 dark:text-blue-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                {{ $incident->incident_number }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="flex-shrink-0 px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $incident->status === 'Resolved' ? 'bg-green-100 text-green-800' :
                                           ($incident->status === 'Open' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $incident->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 text-base font-medium text-gray-900 dark:text-gray-100">No outages recorded</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This site has no incident history.</p>
                    </div>
                @endif
            </div>

            <!-- Maintenance Logs -->
            @php
                $maintenanceLogs = $site->maintenanceLogs()
                    ->orderBy('maintenance_date', 'desc')
                    ->limit(10)
                    ->get();
            @endphp

            <div class="overflow-hidden rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-8">
                <h3 class="text-lg font-heading font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Preventive Maintenance (PM) Logs
                    @if($maintenanceLogs->count() > 0)
                        <span class="ml-auto text-xs font-normal text-gray-500 dark:text-gray-400">(Last {{ $maintenanceLogs->count() }} entries)</span>
                    @endif
                </h3>

                @if($maintenanceLogs->count() > 0)
                    <div class="space-y-3">
                        @foreach($maintenanceLogs as $log)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:bg-blue-50 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $log->maintenance_type }}
                                            </span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $log->maintenance_date->timezone('Indian/Maldives')->format('M d, Y') }}
                                            </span>
                                        </div>
                                        @if($log->description)
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ $log->description }}</p>
                                        @endif
                                        <div class="flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-400">
                                            @if($log->performed_by)
                                                <span class="inline-flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ $log->performed_by }}
                                                </span>
                                            @endif
                                            @if($log->notes)
                                                <span class="inline-flex items-center italic text-gray-500 dark:text-gray-400">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                    {{ $log->notes }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <p class="mt-4 text-base font-medium text-gray-900 dark:text-gray-100">No maintenance records found</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No PM logs have been recorded for this site yet.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
