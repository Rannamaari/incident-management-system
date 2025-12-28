@extends('layouts.app')

@section('header')
    <!-- Hero -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-red-50/50 px-3 sm:px-4 lg:px-8 py-4 sm:py-6 lg:py-8 border-b border-gray-200/30">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-3 sm:gap-4 lg:gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg grid place-items-center transform hover:scale-105 transition-all duration-300">
                            <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-heading text-xl sm:text-2xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">Incident Dashboard</h1>
                            <p class="mt-1 sm:mt-2 text-xs sm:text-sm lg:text-lg text-gray-600 font-medium">Monitor, track, and resolve system incidents efficiently</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 w-full lg:w-auto lg:flex-row lg:items-center lg:gap-4">
                    <!-- Month Selector -->
                    <form method="GET" class="flex items-center gap-2 w-full lg:w-auto">
                        <label for="month" class="text-sm font-heading font-medium text-gray-700 whitespace-nowrap hidden sm:inline">View Month:</label>
                        <label for="month" class="text-sm font-heading font-medium text-gray-700 whitespace-nowrap sm:hidden">Month:</label>
                        <input type="month"
                               id="month"
                               name="month"
                               value="{{ $selectedMonth }}"
                               onchange="this.form.submit()"
                               class="flex-1 lg:flex-none rounded-xl border border-gray-300 px-3 py-2 text-sm font-medium bg-white shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all duration-200">
                    </form>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 w-full lg:w-auto">
                        @if(auth()->user()->canEditIncidents())
                            <!-- Import Excel Button -->
                            <a href="{{ route('incidents.import') }}"
                                class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 rounded-xl lg:rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-3 py-2 lg:px-6 lg:py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-blue-700 hover:to-blue-800 transform text-sm lg:text-base">
                                <svg class="h-4 w-4 lg:h-5 lg:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="hidden sm:inline">Import Excel</span>
                                <span class="sm:hidden">Import</span>
                            </a>
                            <!-- New Incident Button -->
                            <a href="{{ route('incidents.create') }}"
                                class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 rounded-xl lg:rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-3 py-2 lg:px-6 lg:py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-red-700 hover:to-red-800 transform text-sm lg:text-base">
                                <svg class="h-4 w-4 lg:h-5 lg:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="hidden sm:inline">New Incident</span>
                                <span class="sm:hidden">New</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-4 sm:py-6 lg:py-8">
        <div class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-8">

            <!-- KPI Cards -->
            <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-6 lg:grid-cols-4 lg:gap-8">
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
                        class="group relative rounded-2xl sm:rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm p-3 sm:p-6 lg:p-8 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:border-gray-200/70 hover:shadow-2xl hover:bg-white/90">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="mb-2 text-xs sm:text-sm lg:text-base font-heading font-medium text-gray-600">{{ $kpi['label'] }}</p>
                                <p class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight {{ $kpi['valueColor'] }}">
                                    {{ $kpi['value'] }}</p>
                                <p class="mt-1 sm:mt-2 text-xs lg:text-sm text-gray-500">{{ $kpi['hint'] }}</p>
                            </div>
                            <div class="flex-shrink-0 self-end sm:self-auto rounded-xl sm:rounded-2xl p-2 sm:p-4 lg:p-5 {{ $kpi['iconBg'] }} transition-colors">
                                <svg class="h-8 w-8 sm:h-8 sm:w-8 lg:h-10 lg:w-10 text-current" viewBox="0 0 24 24" fill="none"
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

            <!-- Per Page Selector -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <label for="per_page" class="text-sm font-heading font-medium text-gray-700">Show:</label>
                    <form method="GET" id="per-page-form" class="inline-block">
                        <!-- Preserve month filter -->
                        @if(request('month'))
                            <input type="hidden" name="month" value="{{ request('month') }}">
                        @endif

                        <select name="per_page" id="per_page" onchange="this.form.submit()"
                            class="rounded-xl border border-gray-300 px-3 py-2 text-sm font-medium bg-white shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all duration-200">
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="sticky top-0 z-10 bg-gradient-to-r from-gray-50 to-gray-100/80 backdrop-blur-sm text-xs uppercase tracking-wide text-gray-700 font-semibold">
                                <tr>
                                    <th class="font-heading px-3 xl:px-4 py-3 text-left">Incident</th>
                                    <th class="font-heading px-3 xl:px-4 py-3 text-left">Summary</th>
                                    <th class="font-heading px-3 xl:px-4 py-3 text-left">Priority</th>
                                    <th class="font-heading px-3 xl:px-4 py-3 text-left">Duration</th>
                                    <th class="font-heading px-3 xl:px-4 py-3 text-left">Status</th>
                                    <th class="font-heading px-3 xl:px-4 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($incidents as $incident)
                                    <tr onclick="window.location='{{ route('incidents.show', $incident) }}'"
                                        class="cursor-pointer transition-all duration-200 {{ $incident->isCurrentlySlaExceeded() ? 'bg-red-50/80 border-l-4 border-red-400 hover:bg-red-100/80' : 'hover:bg-gradient-to-r hover:from-gray-50/50 hover:to-red-50/30' }}">
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap">
                                                                <div class="flex items-center">
                                                                    <div class="relative">
                                                                        @if($incident->hasUnreadTimelineUpdates())
                                                                            <div class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full border-2 border-white animate-pulse shadow-lg" title="New timeline updates"></div>
                                                                        @endif
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
                                                                    </div>
                                                                    <div class="ml-3">
                                                                        <div class="font-heading font-medium text-gray-900">{{ $incident->incident_code }}</div>
                                                                        <div class="text-xs text-gray-500">{{ $incident->category }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4">
                                                                <div class="max-w-sm">
                                                                    <div class="font-heading font-medium text-gray-900 break-words leading-relaxed">{{ $incident->summary }}</div>
                                                                    <div class="text-xs text-gray-500 mt-1 break-words">{{ $incident->affected_services }}</div>
                                                                    <div class="mt-2 text-xs text-gray-400">
                                                                        {{ $incident->started_at->timezone('Indian/Maldives')->format('M d, H:i') }}</div>
                                                                </div>
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap">
                                                                <span
                                                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-heading font-medium
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
                                                                    <div class="text-xs text-gray-500 hidden xl:block">{{ $incident->resolved_at->timezone('Indian/Maldives')->format('M d, H:i') }}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap">
                                                                <div class="flex flex-col gap-1">
                                                                    <span class="inline-flex w-fit items-center rounded-full px-2 py-1 text-xs font-heading font-medium
                                                  @if($incident->status === 'Open') bg-red-100 text-red-800
                                                  @elseif($incident->status === 'In Progress') bg-yellow-100 text-yellow-800
                                                  @elseif($incident->status === 'Monitoring') bg-blue-100 text-blue-800
                                                  @else bg-green-100 text-green-800 @endif">
                                                                        {{ $incident->status }}
                                                                    </span>
                                                                    @if($incident->rca_required)
                                                                        <span
                                                                            class="inline-flex w-fit items-center rounded-full px-2 py-1 text-xs font-heading font-medium {{ $incident->getRcaColorClass() }}">
                                                                            {{ $incident->getRcaStatus() }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="px-3 xl:px-4 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                                                                <div class="flex items-center gap-1 xl:gap-2">
                                                                    <a href="{{ route('incidents.show', $incident) }}"
                                                                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200" title="View Details">
                                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                        </svg>
                                                                    </a>
                                                                    <!-- Copy Image Button -->
                                                                    <div x-data="{ copied: false }">
                                                                        <button @click.stop="async () => {
                                                                            try {
                                                                                await window.generateIncidentImage({{ $incident->id }});
                                                                                copied = true;
                                                                                setTimeout(() => copied = false, 2000);
                                                                            } catch (err) {
                                                                                console.error('Image generation error:', err);
                                                                            }
                                                                        }"
                                                                            :class="copied ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                                                                            class="p-2 rounded-lg transition-all duration-200" title="Copy Image">
                                                                            <svg x-show="!copied" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                            </svg>
                                                                            <svg x-show="copied" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                    @if(auth()->user()->canEditIncidents())
                                                                        <a href="{{ route('incidents.edit', $incident) }}"
                                                                            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200" title="Edit Incident">
                                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                            </svg>
                                                                        </a>
                                                                        @if($incident->status !== 'Closed')
                                                                            <button type="button" onclick="openCloseModal({{ $incident->id }}, '{{ $incident->incident_code }}', '{{ $incident->started_at->toISOString() }}', '{{ $incident->severity }}')"
                                                                                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200" title="Close Incident">
                                                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                </svg>
                                                                            </button>
                                                                        @endif
                                                                    @endif
                                                                    @if(auth()->user()->canDeleteIncidents())
                                                                        <form action="{{ route('incidents.destroy', $incident) }}" method="POST"
                                                                            class="inline-block" onsubmit="return confirm('Delete this incident?')">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit"
                                                                                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200" title="Delete Incident">
                                                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                                </svg>
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
                                                <p class="text-lg font-heading font-medium text-gray-600">No incidents found</p>
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
                        <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-center">
                            {{ $incidents->links() }}
                        </div>
                    @endif
                </div>

                <!-- Mobile list -->
                <div class="lg:hidden divide-y divide-gray-100/50">
                    @forelse($incidents as $incident)
                        <div onclick="window.location='{{ route('incidents.show', $incident) }}'"
                             class="cursor-pointer p-5 transition-all duration-300 {{ $incident->isCurrentlySlaExceeded() ? 'bg-red-50/80 border-l-4 border-red-400 hover:bg-red-100/80' : 'hover:bg-gradient-to-r hover:from-gray-50/30 hover:to-red-50/20' }}">
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
                                                <span>{{ $incident->started_at->timezone('Indian/Maldives')->format('M d, Y H:i') }}</span>
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
                                                <div class="flex gap-2 flex-wrap" onclick="event.stopPropagation()">
                                                    <a href="{{ route('incidents.show', $incident) }}"
                                                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200" title="View Details">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <!-- Copy Image Button (Mobile) -->
                                                    <div x-data="{ copied: false }">
                                                        <button @click.stop="async () => {
                                                            try {
                                                                await window.generateIncidentImage({{ $incident->id }});
                                                                copied = true;
                                                                setTimeout(() => copied = false, 2000);
                                                            } catch (err) {
                                                                console.error('Image generation error:', err);
                                                            }
                                                        }"
                                                            :class="copied ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                                                            class="p-2 rounded-lg transition-all duration-200" title="Copy Image">
                                                            <svg x-show="!copied" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            <svg x-show="copied" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    @if(auth()->user()->canEditIncidents())
                                                        <a href="{{ route('incidents.edit', $incident) }}"
                                                            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200" title="Edit Incident">
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </a>
                                                        @if($incident->status !== 'Closed')
                                                            <button type="button" onclick="openCloseModal({{ $incident->id }}, '{{ $incident->incident_code }}', '{{ $incident->started_at->toISOString() }}', '{{ $incident->severity }}')"
                                                                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-all duration-200" title="Close Incident">
                                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    @endif
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
                            <p class="text-lg font-heading font-medium text-gray-600">No incidents found</p>
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
                                <div class="flex justify-center">
                                    {{ $incidents->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Close Incident Modal -->
    <div id="closeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-heading font-bold text-gray-900">Close Incident</h3>
                    <button onclick="closeCloseModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="closeIncidentForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-4">Closing incident: <span id="modalIncidentCode" class="font-heading font-semibold text-gray-900"></span></p>
                    </div>

                    <div class="mb-4">
                        <label for="resolved_at" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                            Resolved Date and Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="resolved_at" name="resolved_at" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-600/20 bg-white transition-all duration-300">
                    </div>

                    <div class="mb-4">
                        <label for="root_cause" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                            Root Cause <span class="text-red-500">*</span>
                        </label>
                        <textarea id="root_cause" name="root_cause" rows="4" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-600/20 bg-white transition-all duration-300"
                            placeholder="Enter the root cause of this incident..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">Root cause is required when closing an incident</p>
                    </div>

                    <!-- Conditional Fields Container -->
                    <div id="conditionalFieldsContainer" class="space-y-4 mb-6">
                        <!-- Travel Time (for Medium/High/Critical) -->
                        <div id="travelTimeField" class="hidden">
                            <label for="travel_time" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                Travel Time (minutes) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="travel_time" name="travel_time" min="0"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-600/20 bg-white transition-all duration-300"
                                placeholder="Enter travel time in minutes">
                        </div>

                        <!-- Work Time (for Medium/High/Critical) -->
                        <div id="workTimeField" class="hidden">
                            <label for="work_time" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                Work Time (minutes) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="work_time" name="work_time" min="0"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-600/20 bg-white transition-all duration-300"
                                placeholder="Enter work time in minutes">
                        </div>

                        <!-- Delay Reason (for duration > 5 hours) -->
                        <div id="delayReasonField" class="hidden">
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-2 rounded-r-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            This incident has been open for more than 5 hours. Please explain the reason for the delay.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <label for="delay_reason" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                Reason for Delay <span class="text-red-500">*</span>
                            </label>
                            <textarea id="delay_reason" name="delay_reason" rows="4"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 shadow-sm focus:border-green-600 focus:ring-2 focus:ring-green-600/20 bg-white transition-all duration-300"
                                placeholder="Please provide a detailed explanation for why this incident took more than 5 hours to resolve..."></textarea>
                            <p class="mt-1 text-xs text-gray-500">This field is required for incidents with duration exceeding 5 hours.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeCloseModal()"
                            class="px-6 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-heading font-medium hover:bg-gray-200 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-green-700 text-white font-heading font-semibold shadow-lg transition-all duration-300 hover:from-green-700 hover:to-green-800 hover:shadow-xl">
                            Close Incident
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Store incident data for dynamic updates
        let currentIncidentStartedAt = null;
        let currentIncidentSeverity = null;

        function openCloseModal(incidentId, incidentCode, startedAt, severity) {
            const modal = document.getElementById('closeModal');
            const form = document.getElementById('closeIncidentForm');
            const codeSpan = document.getElementById('modalIncidentCode');
            const resolvedAtInput = document.getElementById('resolved_at');

            // Store incident data
            currentIncidentStartedAt = startedAt;
            currentIncidentSeverity = severity;

            // Set form action
            form.action = `/incidents/${incidentId}/close`;

            // Set incident code
            codeSpan.textContent = incidentCode;

            // Set default resolved time to now
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            resolvedAtInput.value = now.toISOString().slice(0, 16);

            // Update conditional fields
            updateConditionalFieldsIndex();

            // Show modal
            modal.classList.remove('hidden');
        }

        function updateConditionalFieldsIndex() {
            const resolvedAtInput = document.getElementById('resolved_at');

            // Calculate duration in hours
            const startDate = new Date(currentIncidentStartedAt);
            const resolvedDate = new Date(resolvedAtInput.value);
            const durationHours = (resolvedDate - startDate) / (1000 * 60 * 60);

            // Show/hide conditional fields based on duration and severity
            const delayReasonField = document.getElementById('delayReasonField');
            const delayReasonInput = document.getElementById('delay_reason');
            const travelTimeField = document.getElementById('travelTimeField');
            const travelTimeInput = document.getElementById('travel_time');
            const workTimeField = document.getElementById('workTimeField');
            const workTimeInput = document.getElementById('work_time');

            // Show delay reason if duration > 5 hours
            if (durationHours > 5) {
                delayReasonField.classList.remove('hidden');
                delayReasonInput.required = true;
            } else {
                delayReasonField.classList.add('hidden');
                delayReasonInput.required = false;
            }

            // Show travel/work time for Medium/High/Critical
            const requiresTravelWork = ['Medium', 'High', 'Critical'].includes(currentIncidentSeverity);
            if (requiresTravelWork) {
                travelTimeField.classList.remove('hidden');
                workTimeField.classList.remove('hidden');
                travelTimeInput.required = true;
                workTimeInput.required = true;
            } else {
                travelTimeField.classList.add('hidden');
                workTimeField.classList.add('hidden');
                travelTimeInput.required = false;
                workTimeInput.required = false;
            }
        }

        function closeCloseModal() {
            const modal = document.getElementById('closeModal');
            modal.classList.add('hidden');
        }

        // Update conditional fields when resolved_at changes
        document.addEventListener('DOMContentLoaded', function() {
            const resolvedAtInput = document.getElementById('resolved_at');
            if (resolvedAtInput) {
                resolvedAtInput.addEventListener('change', updateConditionalFieldsIndex);
            }
        });

        // Close modal when clicking outside
        document.getElementById('closeModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeCloseModal();
            }
        });
    </script>

@endsection

@push('scripts')
    <!-- HTML2Canvas Library for Image Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        window.generateIncidentImage = async function(incidentId) {
            try {
                // Fetch incident data
                const response = await fetch(`/incidents/${incidentId}/copy-text`);
                if (!response.ok) throw new Error('Failed to fetch incident data');
                const incidentText = await response.text();

                // Create a temporary container for the incident display
                const container = document.createElement('div');
                container.style.cssText = `
                    position: fixed;
                    left: -9999px;
                    top: 0;
                    width: 800px;
                    background: #cb2c30;
                    padding: 6px;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    border-radius: 12px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                `;

                // Create content div with white background
                const contentDiv = document.createElement('div');
                contentDiv.style.cssText = `
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    color: #1a202c;
                    line-height: 1.6;
                `;

                // Extract incident code from the first line
                const lines = incidentText.split('\n');
                const incidentCodeMatch = lines[0].match(/INCIDENT\s+(.+)/);
                const incidentCode = incidentCodeMatch ? incidentCodeMatch[1].trim() : '';

                // Remove the first line (incident code) from the content
                const contentWithoutCode = lines.slice(2).join('\n');

                // Create professional header
                const header = `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 3px solid #cb2c30;">
                        <div style="font-size: 24px; font-weight: 700; color: #1a202c; letter-spacing: 0.5px;">INCIDENT SUMMARY</div>
                        <div style="font-size: 14px; color: #718096; font-weight: 500;">#${incidentCode}</div>
                    </div>
                `;

                // Convert text to HTML with proper formatting
                const formattedHTML = header + contentWithoutCode
                    .replace(/\*([^*]+)\*/g, '<strong style="color: #2d3748;">$1</strong>')
                    .replace(/━━━━━━━━━━━━━━━━━━/g, '<hr style="border: none; border-top: 2px solid #e2e8f0; margin: 15px 0;">')
                    .split('\n')
                    .map(line => {
                        if (line.trim() === '') return '<div style="height: 10px;"></div>';
                        if (line.match(/^\d+\./)) {
                            return `<div style="margin-left: 20px; color: #4a5568;">${line}</div>`;
                        }
                        return `<div style="margin-bottom: 5px;">${line}</div>`;
                    })
                    .join('');

                // Get current date and time
                const now = new Date();
                const generatedTime = now.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });

                // Add footer
                const footer = `
                    <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #e2e8f0; text-align: center; color: #718096; font-size: 12px;">
                        <div style="font-weight: 600; margin-bottom: 4px;">Generated by Incident Management System</div>
                        <div>${generatedTime}</div>
                    </div>
                `;

                contentDiv.innerHTML = formattedHTML + footer;
                container.appendChild(contentDiv);
                document.body.appendChild(container);

                // Generate image using html2canvas
                const canvas = await html2canvas(container, {
                    backgroundColor: null,
                    scale: 2,
                    logging: false,
                    useCORS: true
                });

                // Remove temporary container
                document.body.removeChild(container);

                // Convert canvas to blob and copy to clipboard
                canvas.toBlob(async (blob) => {
                    try {
                        // Copy image to clipboard
                        const item = new ClipboardItem({ 'image/png': blob });
                        await navigator.clipboard.write([item]);
                    } catch (clipboardError) {
                        console.error('Clipboard error:', clipboardError);

                        // Fallback: Download the image if clipboard fails
                        try {
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `incident-${incidentId}.png`;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        } catch (downloadError) {
                            console.error('Download error:', downloadError);
                            throw new Error('Failed to copy to clipboard or download image');
                        }
                    }
                }, 'image/png');

            } catch (error) {
                console.error('Error generating image:', error);
                throw error;
            }
        };
    </script>
@endpush