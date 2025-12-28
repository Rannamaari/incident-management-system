@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="font-heading text-2xl lg:text-3xl font-bold bg-gradient-to-r from-purple-900 via-purple-800 to-purple-900 bg-clip-text text-transparent">
                    {{ $fbbIsland->full_name }}
                </h2>
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold
                    @if($fbbIsland->technology === 'FTTH') bg-blue-100 text-blue-800
                    @elseif($fbbIsland->technology === 'IPOE') bg-green-100 text-green-800
                    @elseif(str_contains($fbbIsland->technology, 'FTTx')) bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $fbbIsland->technology }}
                </span>
            </div>
            <p class="mt-2 text-base text-gray-600">Fixed Broadband Island</p>
        </div>
        <div class="flex gap-3">
            @if(Auth::user()->canManageSites())
                <a href="{{ route('fbb-islands.edit', $fbbIsland) }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-purple-600 to-purple-700 px-5 py-2.5 text-white shadow-lg hover:from-purple-700 hover:to-purple-800 transition-all">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('fbb-islands.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white hover:bg-gray-800 transition-colors">
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

            <!-- FBB Island Information Card -->
            <div class="overflow-hidden rounded-2xl sm:rounded-3xl border bg-white shadow-xl p-4 sm:p-6 lg:p-8">
                <h3 class="text-base sm:text-lg font-heading font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    FBB Island Information
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Left Column -->
                    <div class="space-y-3 sm:space-y-4 lg:space-y-6">
                        <div class="p-3 sm:p-4 rounded-xl bg-purple-50 border border-purple-100">
                            <dt class="text-xs font-medium text-purple-900 uppercase mb-1">Island Name</dt>
                            <dd class="text-base sm:text-lg font-bold text-purple-900">{{ $fbbIsland->island_name }}</dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Region</dt>
                            <dd class="text-base font-semibold text-gray-900">{{ $fbbIsland->region->name }} ({{ $fbbIsland->region->code }})</dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-2">Technology</dt>
                            <dd>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                    @if($fbbIsland->technology === 'FTTH') bg-blue-100 text-blue-800
                                    @elseif($fbbIsland->technology === 'IPOE') bg-green-100 text-green-800
                                    @elseif(str_contains($fbbIsland->technology, 'FTTx')) bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $fbbIsland->technology }}
                                </span>
                            </dd>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-2">Status</dt>
                            <dd>
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                    @if($fbbIsland->is_active) bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $fbbIsland->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $fbbIsland->created_at->timezone('Indian/Maldives')->format('M d, Y - h:i A') }}</dd>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50">
                            <dt class="text-xs font-medium text-gray-500 uppercase mb-1">Last Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $fbbIsland->updated_at->timezone('Indian/Maldives')->format('M d, Y - h:i A') }}</dd>
                        </div>
                    </div>
                </div>

                <!-- Remarks (Full Width) -->
                @if($fbbIsland->remarks)
                    <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200">
                        <dt class="text-xs font-medium text-amber-900 uppercase mb-2 flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            Remarks
                        </dt>
                        <dd class="text-sm text-gray-700 whitespace-pre-line">{{ $fbbIsland->remarks }}</dd>
                    </div>
                @endif
            </div>

            <!-- Related Incidents -->
            @php
                $relatedIncidents = $fbbIsland->incidents()
                    ->orderBy('started_at', 'desc')
                    ->limit(10)
                    ->get();
            @endphp

            <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                <h3 class="text-lg font-heading font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Related Outages
                    @if($relatedIncidents->count() > 0)
                        <span class="ml-auto text-xs font-normal text-gray-500">(Last {{ $relatedIncidents->count() }} incidents)</span>
                    @endif
                </h3>

                @if($relatedIncidents->count() > 0)
                    <div class="space-y-3">
                        @foreach($relatedIncidents as $incident)
                            <a href="{{ route('incidents.show', $incident) }}" class="block border border-gray-200 rounded-xl p-4 hover:bg-purple-50 hover:border-purple-300 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 mb-2">{{ $incident->summary }}</p>
                                        <div class="flex flex-wrap gap-2 text-xs">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700">
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
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-100 text-blue-700">
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
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 text-base font-medium text-gray-900">No outages recorded</p>
                        <p class="mt-1 text-sm text-gray-500">This FBB island has no incident history.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
