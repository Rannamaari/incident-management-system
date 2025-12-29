@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                RCA Management
            </h2>
            <p class="mt-2 text-lg text-gray-600 font-medium">Root Cause Analysis documents and tracking</p>
        </div>

        @if(auth()->user()->canEditIncidents())
            <div class="flex gap-3">
                <a href="{{ route('rcas.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500/30 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New RCA
                </a>
            </div>
        @endif
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 1400px;">

            <!-- Incidents Requiring RCA -->
            @if($incidentsRequiringRca->count() > 0)
            <div class="mb-8">
                <div class="overflow-hidden rounded-2xl border border-red-100 bg-red-50/50 backdrop-blur-sm shadow-lg">
                    <div class="border-b border-red-200/50 bg-gradient-to-r from-red-50/80 to-orange-50/60 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-red-600 to-red-700 shadow-md">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Incidents Requiring RCA</h3>
                                <p class="text-sm text-gray-600">{{ $incidentsRequiringRca->count() }} High/Critical incidents need RCA documents</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Incident</th>
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Summary</th>
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Severity</th>
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Started At</th>
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($incidentsRequiringRca as $incident)
                                    <tr onclick="window.location='{{ route('incidents.show', $incident) }}'"
                                        class="cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="{{ route('incidents.show', $incident) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                {{ $incident->incident_code }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-900 max-w-md truncate">{{ $incident->summary }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-heading font-medium
                                                @if($incident->severity === 'Critical') bg-red-100 text-red-800
                                                @elseif($incident->severity === 'High') bg-orange-100 text-orange-800
                                                @endif">
                                                {{ $incident->severity }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                            {{ $incident->started_at->format('M j, Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap" onclick="event.stopPropagation()">
                                            @if(auth()->user()->canEditIncidents())
                                                <a href="{{ route('rcas.create', ['incident_id' => $incident->id]) }}"
                                                    class="inline-flex items-center gap-1 text-sm font-medium text-orange-600 hover:text-orange-800">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Create RCA
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="mb-6">
                <form method="GET" action="{{ route('rcas.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by title, RCA number, or incident code..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20">
                    </div>
                    <div>
                        <select name="status"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm focus:border-orange-600 focus:ring-2 focus:ring-orange-600/20">
                            <option value="">All Statuses</option>
                            <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="In Review" {{ request('status') === 'In Review' ? 'selected' : '' }}>In Review</option>
                            <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-white shadow-sm hover:bg-orange-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                        @if(request('search') || request('status'))
                            <a href="{{ route('rcas.index') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-gray-200 px-5 py-2.5 text-gray-700 hover:bg-gray-300">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- RCA List -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm shadow-lg">
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-600 to-orange-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">All RCA Documents</h3>
                            <p class="text-sm text-gray-600">{{ $rcas->total() }} total RCAs</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    @if($rcas->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="font-heading px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">RCA Number</th>
                                    <th class="font-heading px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Incident</th>
                                    <th class="font-heading px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Title</th>
                                    <th class="font-heading px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    <th class="font-heading px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Created By</th>
                                    <th class="font-heading px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Created</th>
                                    <th class="font-heading px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($rcas as $rca)
                                <tr onclick="window.location='{{ route('rcas.show', $rca) }}'"
                                    class="cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('rcas.show', $rca) }}" class="text-sm font-medium text-orange-600 hover:text-orange-800">
                                            {{ $rca->rca_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('incidents.show', $rca->incident) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                            {{ $rca->incident->incident_code }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-md truncate">{{ $rca->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-heading font-medium {{ $rca->getStatusBadgeColorClass() }}">
                                            {{ $rca->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $rca->creator->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $rca->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" onclick="event.stopPropagation()">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('rcas.show', $rca) }}" class="text-blue-600 hover:text-blue-800">
                                                View
                                            </a>
                                            @if(auth()->user()->canEditIncidents())
                                                <a href="{{ route('rcas.edit', $rca) }}" class="text-orange-600 hover:text-orange-800">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="font-heading mt-2 text-sm font-heading font-medium text-gray-900">No RCA documents found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new RCA document.</p>
                            @if(auth()->user()->canEditIncidents())
                                <div class="mt-6">
                                    <a href="{{ route('rcas.create') }}"
                                        class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-white shadow-sm hover:bg-orange-700">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Create New RCA
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($rcas->hasPages())
                    <div class="border-t border-gray-200 bg-gray-50/50 px-6 py-4">
                        {{ $rcas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
