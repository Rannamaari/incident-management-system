@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                {{ $temporarySite->temp_site_id }}
            </h2>
            <p class="mt-2 text-lg text-gray-600 font-medium">{{ $temporarySite->site_name }}</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('temporary-sites.index') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-2.5 text-white shadow-lg hover:from-gray-800 hover:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
            @if(Auth::user()->canManageTemporarySites())
                <a href="{{ route('temporary-sites.edit', $temporarySite) }}"
                    class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-2.5 text-white shadow-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Site Details -->
                    <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                        <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 shadow-md">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-heading text-lg font-semibold text-gray-900">Site Information</h3>
                                    <p class="text-sm text-gray-600">Complete details about this temporary site</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 sm:p-8">
                            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-heading font-medium text-gray-500">Temp Site ID</dt>
                                    <dd class="mt-1 text-lg font-heading font-semibold text-gray-900">{{ $temporarySite->temp_site_id }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-heading font-medium text-gray-500">Atoll Code</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $temporarySite->atoll_code }}</dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-heading font-medium text-gray-500">Site Name</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $temporarySite->site_name }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-heading font-medium text-gray-500">Coverage</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $temporarySite->coverage }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-heading font-medium text-gray-500">Added Date</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $temporarySite->added_date->format('d M Y') }}</dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-heading font-medium text-gray-500">Transmission / Backhaul</dt>
                                    <dd class="mt-1 text-base text-gray-900">{{ $temporarySite->transmission_or_backhaul }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-heading font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                            @if($temporarySite->status === 'Temporary') bg-yellow-100 text-yellow-800
                                            @elseif($temporarySite->status === 'Resolved') bg-green-100 text-green-800
                                            @elseif($temporarySite->status === 'Monitoring') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $temporarySite->status }}
                                        </span>
                                    </dd>
                                </div>

                                @if($temporarySite->review_date)
                                    <div>
                                        <dt class="text-sm font-heading font-medium text-gray-500">Review Date</dt>
                                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $temporarySite->review_date->format('d M Y') }}</dd>
                                    </div>
                                @endif

                                @if($temporarySite->remarks)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-heading font-medium text-gray-500">Remarks</dt>
                                        <dd class="mt-2 text-sm text-gray-900 leading-relaxed bg-gray-50 rounded-xl p-4">
                                            {{ $temporarySite->remarks }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Audit History -->
                    <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                        <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-600 to-purple-700 shadow-md">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-heading text-lg font-semibold text-gray-900">Change History</h3>
                                    <p class="text-sm text-gray-600">Audit trail of all modifications</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            @if($temporarySite->audits->count() > 0)
                                <div class="space-y-4">
                                    @foreach($temporarySite->audits as $audit)
                                        <div class="relative pl-8 pb-4 border-l-2 border-gray-200 last:border-l-0 last:pb-0">
                                            <div class="absolute -left-2 top-0 h-4 w-4 rounded-full
                                                @if($audit->action === 'created') bg-green-500
                                                @elseif($audit->action === 'updated') bg-blue-500
                                                @elseif($audit->action === 'deleted') bg-red-500
                                                @elseif($audit->action === 'imported') bg-purple-500
                                                @else bg-gray-500
                                                @endif"></div>

                                            <div class="bg-gray-50/50 rounded-xl p-4">
                                                <div class="flex items-start justify-between mb-2">
                                                    <div>
                                                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold
                                                            @if($audit->action === 'created') text-green-700
                                                            @elseif($audit->action === 'updated') text-blue-700
                                                            @elseif($audit->action === 'deleted') text-red-700
                                                            @elseif($audit->action === 'imported') text-purple-700
                                                            @else text-gray-700
                                                            @endif">
                                                            @if($audit->action === 'created')
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                </svg>
                                                            @elseif($audit->action === 'updated')
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            @elseif($audit->action === 'deleted')
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            @else
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                                </svg>
                                                            @endif
                                                            {{ ucfirst($audit->action) }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-500">{{ $audit->created_at->diffForHumans() }}</span>
                                                </div>

                                                <div class="text-sm text-gray-600 mb-1">
                                                    @if($audit->user)
                                                        <span class="font-medium">{{ $audit->user->name }}</span>
                                                    @else
                                                        <span class="font-medium text-gray-400">System</span>
                                                    @endif
                                                    <span class="text-gray-400">•</span>
                                                    <span>{{ $audit->created_at->format('d M Y, h:i A') }}</span>
                                                </div>

                                                @if($audit->action === 'updated' && $audit->new_values)
                                                    <div class="mt-3 space-y-2">
                                                        @foreach($audit->new_values as $field => $newValue)
                                                            @if(!in_array($field, ['updated_at', 'updated_by']) && isset($audit->old_values[$field]))
                                                                <div class="text-xs">
                                                                    <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                                    <div class="ml-4 mt-1">
                                                                        <div class="line-through text-red-600">{{ $audit->old_values[$field] ?? 'N/A' }}</div>
                                                                        <div class="text-green-600">{{ $newValue }}</div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-4 text-sm text-gray-500">No audit history available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Metadata -->
                    <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                        <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-600 to-gray-700 shadow-md">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-heading text-lg font-semibold text-gray-900">Metadata</h3>
                                    <p class="text-sm text-gray-600">Record information</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <dl class="space-y-4">
                                @if($temporarySite->creator)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Created By</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $temporarySite->creator->name }}</dd>
                                        <dd class="text-xs text-gray-500">{{ $temporarySite->created_at->format('d M Y, h:i A') }}</dd>
                                    </div>
                                @endif

                                @if($temporarySite->updater)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Updated By</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $temporarySite->updater->name }}</dd>
                                        <dd class="text-xs text-gray-500">{{ $temporarySite->updated_at->format('d M Y, h:i A') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if(Auth::user()->canManageTemporarySites())
                        <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                            <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 shadow-md">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-heading text-lg font-semibold text-gray-900">Quick Actions</h3>
                                        <p class="text-sm text-gray-600">Manage this site</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 space-y-3">
                                <a href="{{ route('temporary-sites.edit', $temporarySite) }}"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 font-heading font-semibold text-white shadow-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:-translate-y-0.5">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Site
                                </a>

                                <form method="POST" action="{{ route('temporary-sites.destroy', $temporarySite) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this temporary site? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-4 py-3 font-heading font-semibold text-white shadow-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:-translate-y-0.5">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete Site
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
