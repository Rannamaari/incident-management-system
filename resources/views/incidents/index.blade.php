@extends('layouts.app')

@section('header')
    <!-- Hero -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-red-50/50 px-4 sm:px-6 lg:px-8 py-8 border-b border-gray-200/30">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg grid place-items-center transform hover:scale-105 transition-all duration-300">
                            <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">Incident Dashboard</h1>
                            <p class="mt-2 text-lg text-gray-600 font-medium">Monitor, track, and resolve system incidents efficiently</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Month Selector -->
                    <form method="GET" class="flex items-center gap-2">
                        <label for="month" class="text-sm font-medium text-gray-700 whitespace-nowrap">View Month:</label>
                        <input type="month" 
                               id="month" 
                               name="month" 
                               value="{{ $selectedMonth }}"
                               onchange="this.form.submit()"
                               class="rounded-xl border border-gray-300 px-3 py-2 text-sm font-medium bg-white shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all duration-200">
                    </form>

                    <div class="flex items-center gap-3">
                        @if(auth()->user()->canEditIncidents())
                            <a href="{{ route('incidents.import') }}"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-blue-700 hover:to-blue-800 transform">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Import Excel
                            </a>
                            <a href="{{ route('incidents.create') }}"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-red-700 hover:to-red-800 transform">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                New Incident
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 lg:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- KPI Cards -->
            <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
                <!-- Card component -->
                @php
                    $monthName = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y');
                    $kpis = [
                        [
                            'label' => 'Total Incidents',
                            'value' => $monthlyIncidents->count(),
                            'hint' => $monthName,
                            'iconBg' => 'bg-red-100 group-hover:bg-red-200',
                            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
                            'accent' => 'from-red-50',
                            'valueColor' => 'text-gray-900'
                        ],
                        [
                            'label' => 'Open',
                            'value' => $monthlyIncidents->whereIn('status', ['Open', 'In Progress'])->count(),
                            'hint' => 'Requires attention',
                            'iconBg' => 'bg-yellow-100 group-hover:bg-yellow-200',
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            'accent' => 'from-yellow-50',
                            'valueColor' => 'text-yellow-600'
                        ],
                        [
                            'label' => 'High Priority',
                            'value' => $monthlyIncidents->whereIn('severity', ['Critical', 'High'])->count(),
                            'hint' => 'Critical & High',
                            'iconBg' => 'bg-orange-100 group-hover:bg-orange-200',
                            'icon' => 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6',
                            'accent' => 'from-orange-50',
                            'valueColor' => 'text-orange-600'
                        ],
                        [
                            'label' => 'Resolved',
                            'value' => $monthlyIncidents->where('status', 'Closed')->count(),
                            'hint' => 'Successfully closed',
                            'iconBg' => 'bg-green-100 group-hover:bg-green-200',
                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'accent' => 'from-green-50',
                            'valueColor' => 'text-green-600'
                        ],
                    ];
                  @endphp

                @foreach($kpis as $kpi)
                    <div
                        class="group relative rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm p-6 lg:p-8 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:border-gray-200/70 hover:shadow-2xl hover:bg-white/90">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="mb-2 text-sm lg:text-base font-medium text-gray-600">{{ $kpi['label'] }}</p>
                                <p class="text-3xl lg:text-4xl xl:text-5xl font-bold tracking-tight {{ $kpi['valueColor'] }}">
                                    {{ $kpi['value'] }}</p>
                                <p class="mt-2 text-xs lg:text-sm text-gray-500">{{ $kpi['hint'] }}</p>
                            </div>
                            <div class="rounded-2xl p-4 lg:p-5 {{ $kpi['iconBg'] }} transition-colors">
                                <svg class="h-8 w-8 lg:h-10 lg:w-10 text-current" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $kpi['icon'] }}" />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition-all duration-300 group-hover:opacity-30 bg-gradient-to-br {{ $kpi['accent'] }} to-transparent">
                        </div>
                        <div class="absolute top-4 right-4 w-2 h-2 rounded-full bg-gradient-to-r {{ $kpi['accent'] }} opacity-60"></div>
                    </div>
                @endforeach
            </div>

            <!-- Filters (details/summary: no JS needed) -->
            <div class="mb-8 overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <details class="group" @if(request()->hasAny(['search', 'status', 'severity'])) open @endif>
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
                                <h3 class="text-lg font-semibold text-gray-900">Search & Filter</h3>
                                @if(request('search') || request('status') || request('severity'))
                                    <span
                                        class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Active</span>
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
                        <form method="GET" class="space-y-6 lg:space-y-0 lg:flex lg:items-end lg:gap-8">
                            <div class="flex-1">
                                <label for="search" class="mb-2 block text-sm font-semibold text-gray-700">Search
                                    Incidents</label>
                                <div class="relative">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 grid w-10 place-items-center text-gray-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <input id="search" name="search" value="{{ request('search') }}"
                                        placeholder="Search by code, summary, category, or services..."
                                        class="w-full rounded-2xl border border-gray-300/50 pl-10 pr-4 py-3.5 text-sm lg:text-base shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 placeholder-gray-400 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white" />
                                </div>
                            </div>

                            <div class="w-full sm:w-56">
                                <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">Status</label>
                                <select id="status" name="status"
                                    class="w-full rounded-2xl border border-gray-300/50 py-3.5 px-4 text-sm lg:text-base shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Models\Incident::STATUSES as $status)
                                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                            {{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full sm:w-56">
                                <label for="severity"
                                    class="mb-2 block text-sm font-semibold text-gray-700">Severity</label>
                                <select id="severity" name="severity"
                                    class="w-full rounded-2xl border border-gray-300/50 py-3.5 px-4 text-sm lg:text-base shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white">
                                    <option value="">All Severities</option>
                                    @foreach(\App\Models\Incident::SEVERITIES as $sev)
                                        <option value="{{ $sev }}" {{ request('severity') === $sev ? 'selected' : '' }}>{{ $sev }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3.5 font-semibold text-white shadow-lg transition-all duration-300 hover:from-red-700 hover:to-red-800 hover:shadow-xl transform hover:-translate-y-0.5">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Search
                                </button>
                                @if(request()->hasAny(['search', 'status', 'severity']))
                                    <a href="{{ route('incidents.index') }}"
                                        class="rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 px-5 py-3.5 font-medium text-gray-700 transition-all duration-300 hover:from-gray-200 hover:to-gray-300 transform hover:-translate-y-0.5">Clear</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </details>
            </div>

            <!-- Table/Card container -->
            <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <!-- Desktop table -->
                <div class="hidden lg:block">
                    <div class="max-h-[75vh] overflow-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                        <table class="min-w-full text-sm">
                            <thead class="sticky top-0 z-10 bg-gradient-to-r from-gray-50 to-gray-100/80 backdrop-blur-sm text-xs uppercase tracking-wide text-gray-700 font-semibold">
                                <tr>
                                    <th class="px-3 xl:px-4 py-3 text-left">Incident</th>
                                    <th class="px-3 xl:px-4 py-3 text-left">Summary</th>
                                    <th class="px-3 xl:px-4 py-3 text-left">Priority</th>
                                    <th class="px-3 xl:px-4 py-3 text-left">Duration</th>
                                    <th class="px-3 xl:px-4 py-3 text-left">Status</th>
                                    <th class="px-3 xl:px-4 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($incidents as $incident)
                                    <tr class="transition-all duration-200 {{ $incident->isCurrentlySlaExceeded() ? 'bg-red-50/80 border-l-4 border-red-400 hover:bg-red-100/80' : 'hover:bg-gradient-to-r hover:from-gray-50/50 hover:to-red-50/30' }}">
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap">
                                                                <div class="flex items-center">
                                                                    <div class="grid h-10 w-10 place-items-center rounded-lg
                                                  {{ $incident->severity === 'Critical' ? 'bg-red-100' :
                                    ($incident->severity === 'High' ? 'bg-orange-100' :
                                        ($incident->severity === 'Medium' ? 'bg-yellow-100' : 'bg-green-100')) }}">
                                                                        <svg class="h-5 w-5
                                                    {{ $incident->severity === 'Critical' ? 'text-red-600' :
                                    ($incident->severity === 'High' ? 'text-orange-600' :
                                        ($incident->severity === 'Medium' ? 'text-yellow-600' : 'text-green-600')) }}"
                                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="ml-3">
                                                                        <div class="font-medium text-gray-900">{{ $incident->incident_code }}</div>
                                                                        <div class="text-xs text-gray-500">{{ $incident->category }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4">
                                                                <div class="max-w-sm">
                                                                    <div class="font-medium text-gray-900 break-words leading-relaxed">{{ $incident->summary }}</div>
                                                                    <div class="text-xs text-gray-500 mt-1 break-words">{{ $incident->affected_services }}</div>
                                                                    <div class="mt-2 text-xs text-gray-400">
                                                                        {{ $incident->started_at->format('M d, H:i') }}</div>
                                                                </div>
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap">
                                                                <span
                                                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                                {{ $incident->severity === 'Critical' ? 'bg-red-100 text-red-800' :
                                    ($incident->severity === 'High' ? 'bg-orange-100 text-orange-800' :
                                        ($incident->severity === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                                                    {{ $incident->severity }}
                                                                </span>
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap">
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
                                                                    <div class="text-xs text-gray-500 hidden xl:block">{{ $incident->resolved_at->format('M d, H:i') }}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap">
                                                                <div class="flex flex-col gap-1">
                                                                    <span class="inline-flex w-fit items-center rounded-full px-2 py-1 text-xs font-medium
                                                  @if($incident->status === 'Open') bg-red-100 text-red-800
                                                  @elseif($incident->status === 'In Progress') bg-yellow-100 text-yellow-800
                                                  @elseif($incident->status === 'Monitoring') bg-blue-100 text-blue-800
                                                  @else bg-green-100 text-green-800 @endif">
                                                                        {{ $incident->status }}
                                                                    </span>
                                                                    @if($incident->rca_required)
                                                                        <span
                                                                            class="inline-flex w-fit items-center rounded-full px-2 py-1 text-xs font-medium {{ $incident->getRcaColorClass() }}">
                                                                            {{ $incident->getRcaStatus() }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                                    <!-- View Button -->
                                                                    <a href="{{ route('incidents.show', $incident) }}"
                                                                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-100 to-blue-200 px-2.5 py-1.5 text-blue-700 transition-all duration-300 hover:from-blue-200 hover:to-blue-300 transform hover:scale-105 text-xs">
                                                                        <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                        </svg>
                                                                        <span class="hidden xl:inline">View</span>
                                                                    </a>
                                                                    <!-- Edit Button -->
                                                                    @if(auth()->user()->canEditIncidents())
                                                                        <a href="{{ route('incidents.edit', $incident) }}"
                                                                            class="inline-flex items-center rounded-lg bg-gradient-to-r from-red-100 to-red-200 px-2.5 py-1.5 text-red-700 transition-all duration-300 hover:from-red-200 hover:to-red-300 transform hover:scale-105 text-xs">
                                                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                            </svg>
                                                                            <span class="hidden xl:inline">Edit</span>
                                                                        </a>
                                                                    @endif
                                                                    <!-- Delete Button -->
                                                                    @if(auth()->user()->canDeleteIncidents())
                                                                        <form action="{{ route('incidents.destroy', $incident) }}" method="POST"
                                                                            class="inline" onsubmit="return confirm('Delete this incident?')">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit"
                                                                                class="inline-flex items-center rounded-lg bg-gradient-to-r from-gray-100 to-gray-200 px-2.5 py-1.5 text-gray-700 transition-all duration-300 hover:from-gray-200 hover:to-gray-300 transform hover:scale-105 text-xs">
                                                                                <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16" />
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
                                                <p class="text-lg font-medium text-gray-600">No incidents found</p>
                                                <p class="mt-1 text-sm text-gray-400">Try adjusting your search filters</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination (desktop) -->
                    @if($incidents->hasPages())
                        <div class="hidden border-t border-gray-200 px-6 py-4 lg:flex items-center justify-between">
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
                        <div class="p-5 transition-all duration-300 {{ $incident->isCurrentlySlaExceeded() ? 'bg-red-50/80 border-l-4 border-red-400 hover:bg-red-100/80' : 'hover:bg-gradient-to-r hover:from-gray-50/30 hover:to-red-50/20' }}">
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
                                                <p class="text-sm font-medium text-gray-900">{{ $incident->incident_code }}</p>
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
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
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                      @if($incident->status === 'Open') bg-red-100 text-red-800
                                      @elseif($incident->status === 'In Progress') bg-yellow-100 text-yellow-800
                                      @elseif($incident->status === 'Monitoring') bg-blue-100 text-blue-800
                                      @else bg-green-100 text-green-800 @endif">
                                                        {{ $incident->status }}
                                                    </span>
                                                    @if($incident->rca_required)
                                                        <span
                                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $incident->getRcaColorClass() }}">
                                                            {{ $incident->getRcaStatus() }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('incidents.show', $incident) }}"
                                                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-100 to-blue-200 px-3 py-1.5 text-xs font-medium text-blue-700 transition-all duration-300 hover:from-blue-200 hover:to-blue-300 transform hover:scale-105">
                                                        <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View
                                                    </a>
                                                    <a href="{{ route('incidents.edit', $incident) }}"
                                                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-red-100 to-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition-all duration-300 hover:from-red-200 hover:to-red-300 transform hover:scale-105">
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
                            <p class="text-lg font-medium text-gray-600">No incidents found</p>
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