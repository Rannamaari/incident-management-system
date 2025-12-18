@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold">{{ $site->site_id }}</h2>
            <p class="mt-2 text-lg text-gray-600">{{ $site->site_name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sites.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white">Back</a>
            @if(Auth::user()->canManageSites())
                <a href="{{ route('sites.edit', $site) }}" class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-5 py-2.5 text-white">Edit</a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Site ID</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->site_id }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Atoll Code</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->atoll_code }}</dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Site Name</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->site_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Coverage</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->coverage }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Operational Date</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->operational_date->format('d M Y') }}</dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Transmission / Backhaul</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $site->transmission_or_backhaul }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                @if($site->status === 'Active') bg-green-100 text-green-800
                                @elseif($site->status === 'Monitoring') bg-blue-100 text-blue-800
                                @elseif($site->status === 'Maintenance') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $site->status }}
                            </span>
                        </dd>
                    </div>

                    @if($site->review_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Review Date</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->review_date->format('d M Y') }}</dd>
                        </div>
                    @endif

                    @if($site->remarks)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Remarks</dt>
                            <dd class="mt-2 text-sm text-gray-900 bg-gray-50 rounded-xl p-4">{{ $site->remarks }}</dd>
                        </div>
                    @endif

                    @if($site->creator)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $site->creator->name }}</dd>
                            <dd class="text-xs text-gray-500">{{ $site->created_at->format('d M Y, h:i A') }}</dd>
                        </div>
                    @endif

                    @if($site->updater)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $site->updater->name }}</dd>
                            <dd class="text-xs text-gray-500">{{ $site->updated_at->format('d M Y, h:i A') }}</dd>
                        </div>
                    @endif
                </dl>

                @if(Auth::user()->canManageSites())
                    <div class="mt-8 pt-6 border-t">
                        <form method="POST" action="{{ route('sites.destroy', $site) }}" onsubmit="return confirm('Are you sure you want to delete this site?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 font-semibold text-white">
                                Delete Site
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
