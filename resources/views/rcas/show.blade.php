@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                    {{ $rca->rca_number }}
                </h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $rca->getStatusBadgeColorClass() }}">
                    {{ $rca->status }}
                </span>
            </div>
            <p class="text-lg text-gray-600 font-medium">{{ $rca->title }}</p>
            <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                <span>Incident: <a href="{{ route('incidents.show', $rca->incident) }}" class="text-blue-600 hover:text-blue-800 font-medium">{{ $rca->incident->incident_code }}</a></span>
                <span>•</span>
                <span>Created: {{ $rca->created_at->format('M j, Y') }}</span>
                @if($rca->creator)
                    <span>•</span>
                    <span>By: {{ $rca->creator->name }}</span>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 1200px;">

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 mb-6">
                <a href="{{ route('rcas.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-700 px-5 py-2.5 text-white shadow-sm hover:bg-gray-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>

                @if(auth()->user()->canEditIncidents())
                    <a href="{{ route('rcas.edit', $rca) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-white shadow-sm hover:bg-orange-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit RCA
                    </a>
                @endif
            </div>

            <!-- RCA Content -->
            <div class="space-y-6">

                <!-- 1. Problem Description -->
                @if($rca->problem_description)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 shadow-lg">
                    <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                        <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">1. Problem Description</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $rca->problem_description }}</p>
                    </div>
                </div>
                @endif

                <!-- Timeline of Events -->
                @if($rca->timeLogs->count() > 0)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 shadow-lg">
                    <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                        <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">Timeline of Events</h3>
                        <p class="text-sm text-gray-600">{{ $rca->timeLogs->count() }} events recorded</p>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($rca->timeLogs as $log)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 {{ $loop->last ? 'bg-green-200' : 'bg-gray-200' }}" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full {{ $loop->last ? 'bg-green-500' : 'bg-orange-500' }} flex items-center justify-center ring-8 ring-white">
                                                        @if($loop->last)
                                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        @else
                                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div class="flex-1">
                                                        <p class="text-sm {{ $loop->last ? 'text-green-900 font-medium' : 'text-gray-900' }}">
                                                            {{ $log->event_description }}
                                                        </p>
                                                        @if($loop->last)
                                                            <p class="mt-1 text-xs text-green-700 font-medium">
                                                                <svg class="inline h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Service Restored
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap {{ $loop->last ? 'text-green-600' : 'text-gray-500' }}">
                                                        <time datetime="{{ $log->occurred_at->toISOString() }}">
                                                            {{ $log->occurred_at->format('M j, Y g:i A') }}
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- 2. Problem Analysis -->
                @if($rca->problem_analysis)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 shadow-lg">
                    <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                        <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">2. Problem Analysis</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $rca->problem_analysis }}</p>
                    </div>
                </div>
                @endif

                <!-- 3. Root Cause -->
                @if($rca->root_cause)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 shadow-lg">
                    <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                        <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">3. Root Cause</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $rca->root_cause }}</p>
                    </div>
                </div>
                @endif

                <!-- 4. Corrective Actions -->
                @if($rca->workaround || $rca->solution || $rca->recommendation)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 shadow-lg">
                    <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                        <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">4. Corrective Actions</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        @if($rca->workaround)
                        <div>
                            <h4 class="font-heading text-sm font-heading font-semibold text-gray-700 mb-2">4.1 Workaround</h4>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $rca->workaround }}</p>
                        </div>
                        @endif

                        @if($rca->solution)
                        <div>
                            <h4 class="font-heading text-sm font-heading font-semibold text-gray-700 mb-2">4.2 Solution</h4>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $rca->solution }}</p>
                        </div>
                        @endif

                        @if($rca->recommendation)
                        <div>
                            <h4 class="font-heading text-sm font-heading font-semibold text-gray-700 mb-2">4.3 Recommendation</h4>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $rca->recommendation }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Action Points -->
                @if($rca->actionPoints->count() > 0)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white/80 shadow-lg">
                    <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-4">
                        <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">4.1 Action Points</h3>
                        <p class="text-sm text-gray-600">{{ $rca->actionPoints->count() }} action items</p>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action Item</th>
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Responsible Person</th>
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Due Date</th>
                                        <th class="font-heading px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($rca->actionPoints as $actionPoint)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $actionPoint->action_item }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $actionPoint->responsible_person }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                            {{ $actionPoint->due_date ? $actionPoint->due_date->format('M j, Y') : 'Not set' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-heading font-medium {{ $actionPoint->getStatusBadgeColorClass() }}">
                                                {{ $actionPoint->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
@endsection
