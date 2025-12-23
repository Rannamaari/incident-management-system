@extends('layouts.app')

@section('header')
    <!-- Hero -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-amber-50 via-white to-orange-50/50 px-4 sm:px-6 lg:px-8 py-8 border-b border-gray-200/30">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 shadow-lg grid place-items-center transform hover:scale-105 transition-all duration-300">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-heading text-3xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-orange-900 via-orange-800 to-orange-900 bg-clip-text text-transparent">Recurring Incidents</h1>
                            <p class="mt-2 text-lg text-gray-600 font-medium">Identify sites with frequent outages</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden lg:flex items-center gap-2 text-sm text-gray-600">
                        <span>Recurring sites:</span>
                        <span class="font-heading font-medium text-orange-900">{{ $recurringIncidents->count() }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('logs.index') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-200 hover:bg-gray-300 px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors duration-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Logs
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

            <!-- Filters -->
            <div class="mb-8 overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <div class="px-6 py-5 border-b border-gray-200/50">
                    <div class="flex items-center gap-3">
                        <div class="grid h-8 w-8 place-items-center rounded-xl bg-gradient-to-br from-gray-100 to-gray-200">
                            <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                        </div>
                        <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Filters</h3>
                    </div>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ route('logs.recurring-incidents') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <!-- Month Selection -->
                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                            <input type="month" id="month" name="month" value="{{ $selectedMonth }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all duration-300">
                        </div>

                        <!-- Minimum Occurrences -->
                        <div>
                            <label for="min_occurrences" class="block text-sm font-medium text-gray-700 mb-2">Minimum Occurrences</label>
                            <select id="min_occurrences" name="min_occurrences"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all duration-300">
                                <option value="2" {{ $minOccurrences == 2 ? 'selected' : '' }}>2 or more</option>
                                <option value="3" {{ $minOccurrences == 3 ? 'selected' : '' }}>3 or more</option>
                                <option value="4" {{ $minOccurrences == 4 ? 'selected' : '' }}>4 or more</option>
                                <option value="5" {{ $minOccurrences == 5 ? 'selected' : '' }}>5 or more</option>
                                <option value="10" {{ $minOccurrences == 10 ? 'selected' : '' }}>10 or more</option>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500/30 transition-all duration-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recurring Incidents Table -->
            <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Site/Summary</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Occurrences</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">First Occurrence</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Last Occurrence</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Categories</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($recurringIncidents as $incident)
                                <tr class="hover:bg-orange-50/30 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($incident->summary, 80) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
                                            @if($incident->occurrence_count >= 10) bg-red-100 text-red-800
                                            @elseif($incident->occurrence_count >= 5) bg-orange-100 text-orange-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ $incident->occurrence_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($incident->first_occurrence)->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($incident->last_occurrence)->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">{{ $incident->categories ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('logs.incidents-by-summary', ['summary' => $incident->summary]) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-orange-100 px-3 py-1.5 text-sm font-medium text-orange-700 hover:bg-orange-200 transition-colors duration-200">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View All
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm text-gray-600">No recurring incidents found for the selected criteria.</p>
                                            <p class="text-xs text-gray-500">Try adjusting the filters above.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
