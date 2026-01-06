@extends('layouts.app')

@section('header')
    <!-- Hero -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-amber-50 via-white to-orange-50/50 px-4 sm:px-6 lg:px-8 py-8 border-b border-gray-200/30">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 shadow-lg grid place-items-center">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-orange-900 via-orange-800 to-orange-900 bg-clip-text text-transparent">All Incidents for Site</h1>
                        <p class="mt-1 text-sm text-gray-600 font-medium">{{ $incidents->total() }} occurrences found</p>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="rounded-2xl border border-orange-200 bg-orange-50/50 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-orange-900 mb-1">Site/Incident Summary</h3>
                            <p class="text-sm text-gray-700">{{ $summary }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('logs.recurring-incidents') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-200 hover:bg-gray-300 px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Recurring Incidents
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 lg:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Incidents Table -->
            <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Incident Code</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Started At</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Resolved At</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Duration</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Severity</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Status</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Category</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-heading font-semibold uppercase tracking-wider text-gray-700">Root Cause</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($incidents as $incident)
                                <tr class="hover:bg-orange-50/30 transition-colors duration-200 cursor-pointer"
                                    onclick="window.location='{{ route('incidents.show', $incident) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $incident->incident_code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $incident->started_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $incident->resolved_at ? $incident->resolved_at->format('M d, Y H:i') : 'Ongoing' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                        {{ $incident->duration_hms ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            @if($incident->severity === 'Critical') bg-purple-100 text-purple-800
                                            @elseif($incident->severity === 'High') bg-red-100 text-red-800
                                            @elseif($incident->severity === 'Medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ $incident->severity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            @if($incident->status === 'Closed') bg-gray-100 text-gray-800
                                            @elseif($incident->status === 'In Progress') bg-blue-100 text-blue-800
                                            @else bg-orange-100 text-orange-800
                                            @endif">
                                            {{ $incident->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $incident->category ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $incident->root_cause ? Str::limit($incident->root_cause, 80) : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm text-gray-600">No incidents found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($incidents->hasPages())
                    <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                        {{ $incidents->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
