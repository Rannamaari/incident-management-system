@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold">{{ $site->site_code }}</h2>
            <p class="mt-2 text-lg text-gray-600">{{ $site->display_name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sites.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Site Code</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->site_code }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Site Number</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->site_number }}</dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Display Name</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->display_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Region</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $site->region->name }} ({{ $site->region->code }})
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $site->location->location_name }}</dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Available Technologies</dt>
                        <dd class="mt-2 flex gap-2">
                            @foreach($site->technologies as $tech)
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                    @if($tech->is_active) bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-500 line-through
                                    @endif">
                                    {{ $tech->technology }}
                                    @if(!$tech->is_active)
                                        (Disabled)
                                    @endif
                                </span>
                            @endforeach
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">FBB (Supernet)</dt>
                        <dd class="mt-1">
                            @if($site->has_fbb)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold bg-gray-100 text-gray-600">
                                    Disabled
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                @if($site->is_active) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $site->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $site->created_at->timezone('Indian/Maldives')->format('d M Y, h:i A') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $site->updated_at->timezone('Indian/Maldives')->format('d M Y, h:i A') }}</dd>
                    </div>
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
