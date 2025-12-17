@extends('layouts.app')

@section('header')
    <!-- Hero -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-blue-50/50 px-4 sm:px-6 lg:px-8 py-8 border-b border-gray-200/30">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg grid place-items-center transform hover:scale-105 transition-all duration-300">
                            <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-heading text-3xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">Logs</h1>
                            <p class="mt-2 text-lg text-gray-600 font-medium">Complete historical record of all incidents</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden lg:flex items-center gap-2 text-sm text-gray-600">
                        <span>Total records:</span>
                        <span class="font-heading font-medium text-gray-900">{{ $incidents->total() }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('logs.export', request()->query()) }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-medium text-black shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7,10 12,15 17,10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            @if(request()->hasAny(['search', 'status', 'severity', 'date_from', 'date_to']))
                                Export Filtered Logs
                            @else
                                Export All Logs
                            @endif
                        </a>
                        
                        <a href="{{ route('incidents.create') }}"
                            class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-red-700 hover:to-red-800 transform">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Incident
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 lg:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Filters (details/summary: no JS needed) -->
            <div class="mb-8 overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <details class="group" @if(request()->hasAny(['search', 'status', 'severity', 'date_from', 'date_to', 'rca_required', 'sla_breached'])) open @endif>
                    <summary class="cursor-pointer list-none px-6 py-5 border-b border-gray-200/50 hover:bg-gray-50/50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="grid h-8 w-8 place-items-center rounded-xl bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="h-4 w-4 text-gray-600" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                                    </svg>
                                </div>
                                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Search & Filter Logs</h3>
                                @if(request('search') || request('status') || request('severity') || request('date_from') || request('date_to') || request('rca_required') || request('sla_breached'))
                                    <span
                                        class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-heading font-medium text-blue-800">Active</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 transition group-open:rotate-180">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </summary>

                    <div class="px-6 py-8 bg-gradient-to-br from-gray-50/30 to-white/50">
                        <form method="GET" class="space-y-6">
                            <!-- Search Row -->
                            <div class="flex flex-col lg:flex-row lg:items-end gap-6">
                                <div class="flex-1">
                                    <label for="search" class="mb-2 block text-sm font-heading font-semibold text-gray-700">Search Logs</label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-gray-400">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </span>
                                        <input id="search" name="search" value="{{ request('search') }}"
                                            placeholder="Search by code, summary, category, or services..."
                                            class="w-full rounded-2xl border border-gray-300/50 pl-10 pr-4 py-3.5 text-sm lg:text-base shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 placeholder-gray-400 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white" />
                                    </div>
                                </div>

                                <div class="w-full sm:w-56">
                                    <label for="status" class="mb-2 block text-sm font-heading font-semibold text-gray-700">Status</label>
                                    <select id="status" name="status"
                                        class="w-full rounded-2xl border border-gray-300/50 py-3.5 px-4 text-sm lg:text-base shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                                        <option value="">All Statuses</option>
                                        @foreach(\App\Models\Incident::STATUSES as $status)
                                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                                {{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="w-full sm:w-56">
                                    <label for="severity" class="mb-2 block text-sm font-heading font-semibold text-gray-700">Severity</label>
                                    <select id="severity" name="severity"
                                        class="w-full rounded-2xl border border-gray-300/50 py-3.5 px-4 text-sm lg:text-base shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                                        <option value="">All Severities</option>
                                        @foreach(\App\Models\Incident::SEVERITIES as $sev)
                                            <option value="{{ $sev }}" {{ request('severity') === $sev ? 'selected' : '' }}>{{ $sev }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Date Range Row -->
                            <div class="flex flex-col lg:flex-row lg:items-end gap-6">
                                <div class="w-full sm:w-56">
                                    <label for="date_from" class="mb-2 block text-sm font-heading font-semibold text-gray-700">From Date</label>
                                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                        class="w-full rounded-2xl border border-gray-300/50 py-3.5 px-4 text-sm lg:text-base shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                                </div>

                                <div class="w-full sm:w-56">
                                    <label for="date_to" class="mb-2 block text-sm font-heading font-semibold text-gray-700">To Date</label>
                                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                        class="w-full rounded-2xl border border-gray-300/50 py-3.5 px-4 text-sm lg:text-base shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                                </div>

                                <div class="flex-1"></div>
                            </div>

                            <!-- Advanced Filters Row -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-2 border-t border-gray-200">
                                <label class="text-sm font-heading font-semibold text-gray-700">Advanced Filters:</label>

                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="rca_required" value="1" {{ request('rca_required') ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <span class="ml-2 text-sm font-heading font-medium text-gray-700">RCA Required</span>
                                </label>

                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="sla_breached" value="1" {{ request('sla_breached') ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <span class="ml-2 text-sm font-heading font-medium text-gray-700">SLA Breached</span>
                                </label>

                                <div class="flex items-center gap-3 ml-auto">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3.5 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:from-blue-700 hover:to-blue-800 hover:shadow-xl transform hover:-translate-y-0.5">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        Filter & Search
                                    </button>
                                    @if(request()->hasAny(['search', 'status', 'severity', 'date_from', 'date_to', 'rca_required', 'sla_breached']))
                                        <a href="{{ route('logs.index') }}"
                                            class="rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 px-5 py-3.5 font-heading font-medium text-gray-700 transition-all duration-300 hover:from-gray-200 hover:to-gray-300 transform hover:-translate-y-0.5">Clear All</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </details>
            </div>

            <!-- Per Page Selector -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <label for="per_page" class="text-sm font-heading font-medium text-gray-700">Show:</label>
                    <form method="GET" id="per-page-form" class="inline-block">
                        <!-- Preserve existing filters -->
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if(request('severity'))
                            <input type="hidden" name="severity" value="{{ request('severity') }}">
                        @endif
                        @if(request('date_from'))
                            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                        @endif
                        @if(request('date_to'))
                            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                        @endif
                        @if(request('rca_required'))
                            <input type="hidden" name="rca_required" value="{{ request('rca_required') }}">
                        @endif
                        @if(request('sla_breached'))
                            <input type="hidden" name="sla_breached" value="{{ request('sla_breached') }}">
                        @endif

                        <select name="per_page" id="per_page" onchange="this.form.submit()"
                            class="rounded-xl border border-gray-300 px-3 py-2 text-sm font-medium bg-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 incidents</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 incidents</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 incidents</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 incidents</option>
                        </select>
                    </form>
                </div>
                <div class="text-sm text-gray-600">
                    Showing <span class="font-medium">{{ $incidents->firstItem() ?? 0 }}</span>–<span class="font-medium">{{ $incidents->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $incidents->total() }}</span>
                </div>
            </div>

            <!-- Table/Card container -->
            <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <!-- Desktop table -->
                <div class="hidden lg:block">
                    <table class="min-w-full text-sm">
                            <thead class="sticky top-0 z-10 bg-gradient-to-r from-gray-50 to-gray-100/80 backdrop-blur-sm text-xs uppercase tracking-wide text-gray-700 font-semibold">
                                <tr>
                                    <th class="font-heading px-2 xl:px-4 py-3 text-left">Incident</th>
                                    <th class="font-heading px-2 xl:px-4 py-3 text-left">Summary</th>
                                    <th class="font-heading px-2 xl:px-4 py-3 text-left">Priority</th>
                                    <th class="font-heading px-2 xl:px-4 py-3 text-left">Duration</th>
                                    <th class="font-heading px-2 xl:px-4 py-3 text-left">Status</th>
                                    <th class="font-heading px-2 xl:px-4 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($incidents as $incident)
                                    <tr onclick="window.location='{{ route('incidents.show', $incident) }}'"
                                        class="cursor-pointer transition-all duration-200 {{ $incident->isCurrentlySlaExceeded() ? 'bg-red-50/80 border-l-4 border-red-400 hover:bg-red-100/80' : 'hover:bg-gradient-to-r hover:from-gray-50/50 hover:to-blue-50/30' }}">
                                        <td class="px-2 xl:px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="grid h-8 xl:h-10 w-8 xl:w-10 place-items-center rounded-lg
                                                    {{ $incident->severity === 'Critical' ? 'bg-red-100' :
                                                        ($incident->severity === 'High' ? 'bg-orange-100' :
                                                            ($incident->severity === 'Medium' ? 'bg-yellow-100' : 'bg-green-100')) }}">
                                                    <svg class="h-4 xl:h-5 w-4 xl:w-5
                                                        {{ $incident->severity === 'Critical' ? 'text-red-600' :
                                                            ($incident->severity === 'High' ? 'text-orange-600' :
                                                                ($incident->severity === 'Medium' ? 'text-yellow-600' : 'text-green-600')) }}"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-2 xl:ml-3">
                                                    <div class="font-heading font-medium text-gray-900 text-xs xl:text-sm">{{ $incident->incident_code }}</div>
                                                    <div class="text-xs text-gray-500 hidden xl:block">{{ $incident->category }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 xl:px-4 py-4">
                                            <div class="max-w-xs xl:max-w-sm">
                                                <div class="font-heading font-medium text-gray-900 break-words leading-relaxed text-xs xl:text-sm">{{ $incident->summary }}</div>
                                                <div class="text-xs text-gray-500 mt-1 break-words hidden xl:block">{{ $incident->affected_services }}</div>
                                                <div class="mt-1 xl:mt-2 text-xs text-gray-400">
                                                    {{ $incident->started_at->format('M d, H:i') }}</div>
                                            </div>
                                        </td>
                                        <td class="px-2 xl:px-4 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center rounded-full px-1.5 xl:px-2 py-1 text-xs font-heading font-medium
                                                    {{ $incident->severity === 'Critical' ? 'bg-red-100 text-red-800' :
                                                        ($incident->severity === 'High' ? 'bg-orange-100 text-orange-800' :
                                                            ($incident->severity === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                                {{ $incident->severity }}
                                            </span>
                                        </td>
                                        <td class="px-2 xl:px-4 py-4 whitespace-nowrap">
                                            <div class="text-gray-900 text-xs xl:text-sm">
                                                @if($incident->duration_hms)
                                                    {{ $incident->duration_hms }}
                                                @elseif($incident->status === 'Closed')
                                                    -
                                                @else
                                                    <span class="text-blue-600">Ongoing</span>
                                                @endif
                                            </div>
                                            @if($incident->resolved_at)
                                                <div class="text-xs text-gray-500 hidden xl:block">{{ $incident->resolved_at->format('M d, H:i') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-2 xl:px-4 py-4 whitespace-nowrap">
                                            <div class="flex flex-col gap-1">
                                                <span class="inline-flex w-fit items-center rounded-full px-1.5 xl:px-2 py-1 text-xs font-heading font-medium
                                                    @if($incident->status === 'Open') bg-red-100 text-red-800
                                                    @elseif($incident->status === 'In Progress') bg-yellow-100 text-yellow-800
                                                    @elseif($incident->status === 'Monitoring') bg-blue-100 text-blue-800
                                                    @else bg-green-100 text-green-800 @endif">
                                                    {{ $incident->status }}
                                                </span>
                                                @if($incident->rca_required)
                                                    <span
                                                        class="inline-flex w-fit items-center rounded-full px-1.5 xl:px-2 py-1 text-xs font-heading font-medium {{ $incident->getRcaColorClass() }} hidden xl:inline-flex">
                                                        {{ $incident->getRcaStatus() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-2 xl:px-4 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                                            <div class="flex items-center gap-1 xl:gap-2">
                                                <a href="{{ route('incidents.show', $incident) }}"
                                                    class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-100 to-blue-200 px-2.5 py-1.5 text-blue-700 transition-all duration-300 hover:from-blue-200 hover:to-blue-300 transform hover:scale-105 text-xs">
                                                    <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span class="hidden xl:inline">View</span>
                                                </a>
                                                <a href="{{ route('incidents.edit', $incident) }}"
                                                    class="inline-flex items-center rounded-lg bg-gradient-to-r from-red-100 to-red-200 px-2.5 py-1.5 text-red-700 transition-all duration-300 hover:from-red-200 hover:to-red-300 transform hover:scale-105 text-xs">
                                                    <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span class="hidden xl:inline">Edit</span>
                                                </a>
                                                @if(auth()->user()->canDeleteIncidents())
                                                    <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete incident {{ $incident->incident_code }}? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center rounded-lg bg-gradient-to-r from-gray-100 to-gray-200 px-2.5 py-1.5 text-gray-700 transition-all duration-300 hover:from-red-200 hover:to-red-300 hover:text-red-800 transform hover:scale-105 text-xs">
                                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            <span class="hidden xl:inline">Delete</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-12 text-center">
                                            <div class="mx-auto max-w-sm">
                                                <svg class="mx-auto mb-4 h-12 w-12 text-gray-400" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-lg font-heading font-medium text-gray-600">No incident logs found</p>
                                                <p class="mt-1 text-sm text-gray-400">Try adjusting your search filters</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    <!-- Pagination (desktop) -->
                    @if($incidents->hasPages())
                        <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-between">
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $incidents->firstItem() }}</span>–<span
                                    class="font-medium">{{ $incidents->lastItem() }}</span>
                                of <span class="font-medium">{{ $incidents->total() }}</span>
                            </p>
                            {{ $incidents->links() }}
                        </div>
                    @endif
                </div>

                <!-- Mobile list -->
                <div class="lg:hidden divide-y divide-gray-100/50">
                    @forelse($incidents as $incident)
                        <div onclick="window.location='{{ route('incidents.show', $incident) }}'"
                             class="cursor-pointer p-4 sm:p-5 transition-all duration-300 {{ $incident->isCurrentlySlaExceeded() ? 'bg-red-50/80 border-l-4 border-red-400 hover:bg-red-100/80' : 'hover:bg-gradient-to-r hover:from-gray-50/30 hover:to-blue-50/20' }}">
                            <div class="flex items-start gap-3">
                                <div class="grid h-10 w-10 place-items-center rounded-lg
                                    {{ $incident->severity === 'Critical' ? 'bg-red-100' :
                                        ($incident->severity === 'High' ? 'bg-orange-100' :
                                            ($incident->severity === 'Medium' ? 'bg-yellow-100' : 'bg-green-100')) }}">
                                    <svg class="h-5 w-5
                                        {{ $incident->severity === 'Critical' ? 'text-red-600' :
                                            ($incident->severity === 'High' ? 'text-orange-600' :
                                                ($incident->severity === 'Medium' ? 'text-yellow-600' : 'text-green-600')) }}" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="mb-2 flex items-center justify-between">
                                        <p class="text-sm font-heading font-medium text-gray-900">{{ $incident->incident_code }}</p>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-heading font-medium
                                                {{ $incident->severity === 'Critical' ? 'bg-red-100 text-red-800' :
                                                    ($incident->severity === 'High' ? 'bg-orange-100 text-orange-800' :
                                                        ($incident->severity === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                            {{ $incident->severity }}
                                        </span>
                                    </div>
                                    <p class="mb-2 text-sm text-gray-900 break-words leading-relaxed">{{ $incident->summary }}</p>
                                    <div class="mb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-0 text-xs text-gray-500">
                                        <span>{{ $incident->started_at->format('M d, Y H:i') }}</span>
                                        <span>{{ $incident->category }}</span>
                                    </div>
                                    <div class="mb-3 flex items-center justify-between text-xs text-gray-600">
                                        <span class="font-medium">Duration:</span>
                                        <span>
                                            @if($incident->duration_hms)
                                                {{ $incident->duration_hms }}
                                            @elseif($incident->status === 'Closed')
                                                -
                                            @else
                                                <span class="text-blue-600">Ongoing</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-heading font-medium
                                                @if($incident->status === 'Open') bg-red-100 text-red-800
                                                @elseif($incident->status === 'In Progress') bg-yellow-100 text-yellow-800
                                                @elseif($incident->status === 'Monitoring') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ $incident->status }}
                                            </span>
                                            @if($incident->rca_required)
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-heading font-medium {{ $incident->getRcaColorClass() }}">
                                                    {{ $incident->getRcaStatus() }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex gap-2 flex-shrink-0" onclick="event.stopPropagation()">
                                            <a href="{{ route('incidents.show', $incident) }}"
                                                class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-100 to-blue-200 px-3 py-1.5 text-xs font-heading font-medium text-blue-700 transition-all duration-300 hover:from-blue-200 hover:to-blue-300 transform hover:scale-105">
                                                <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </a>
                                            <a href="{{ route('incidents.edit', $incident) }}"
                                                class="inline-flex items-center rounded-lg bg-gradient-to-r from-red-100 to-red-200 px-3 py-1.5 text-xs font-heading font-medium text-red-700 transition-all duration-300 hover:from-red-200 hover:to-red-300 transform hover:scale-105">
                                                <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="mx-auto mb-4 h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-heading font-medium text-gray-600">No incident logs found</p>
                            <p class="mt-1 text-sm text-gray-400">Try adjusting your search filters</p>
                        </div>
                    @endforelse

                    @if($incidents->hasPages())
                        <div class="border-t border-gray-200 px-4 py-3 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-1 justify-between sm:hidden">
                                    @if ($incidents->onFirstPage())
                                        <span
                                            class="inline-flex cursor-default items-center rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-500">Previous</span>
                                    @else
                                        <a href="{{ $incidents->previousPageUrl() }}"
                                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Previous</a>
                                    @endif

                                    @if ($incidents->hasMorePages())
                                        <a href="{{ $incidents->nextPageUrl() }}"
                                            class="ml-3 inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Next</a>
                                    @else
                                        <span
                                            class="ml-3 inline-flex cursor-default items-center rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-500">Next</span>
                                    @endif
                                </div>
                                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">{{ $incidents->firstItem() }}</span>–<span
                                            class="font-medium">{{ $incidents->lastItem() }}</span>
                                        of <span class="font-medium">{{ $incidents->total() }}</span>
                                    </p>
                                    {{ $incidents->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection