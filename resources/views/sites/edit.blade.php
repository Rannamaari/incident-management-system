@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold">Edit Site</h2>
            <p class="mt-2 text-lg text-gray-600">{{ $site->site_code }} - {{ $site->display_name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sites.show', $site) }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white">
                Cancel
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('sites.update', $site) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Site Information (Read-only) -->
                <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 mb-6">Site Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Site Code</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->site_code }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Site Number</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->site_number }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Display Name</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->display_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Region</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->region->name }} ({{ $site->region->code }})</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Location</label>
                            <p class="text-base font-semibold text-gray-900">{{ $site->location->location_name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Site Settings -->
                <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 mb-6">Site Settings</h3>

                    <div class="space-y-4">
                        <!-- Active Status -->
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-gray-50">
                            <input type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ $site->is_active ? 'checked' : '' }}
                                   class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_active" class="flex-1 cursor-pointer">
                                <span class="block text-sm font-medium text-gray-900">Active Site</span>
                                <span class="text-xs text-gray-500">Enable this site for monitoring and incident tracking. Inactive sites are hidden from incident creation.</span>
                            </label>
                        </div>

                        <!-- FBB (Supernet) -->
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-purple-50">
                            <input type="checkbox"
                                   name="has_fbb"
                                   id="has_fbb"
                                   value="1"
                                   {{ $site->has_fbb ? 'checked' : '' }}
                                   class="h-5 w-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <label for="has_fbb" class="flex-1 cursor-pointer">
                                <span class="block text-sm font-medium text-gray-900">FBB (Supernet)</span>
                                <span class="text-xs text-gray-500">This site has Fixed Broadband service</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Technologies -->
                <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 mb-6">Available Technologies</h3>

                    <div class="space-y-6">
                        <!-- Cellular Technologies -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Cellular Technologies</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($site->technologies->whereIn('technology', ['2G', '3G', '4G', '5G']) as $tech)
                                    <div class="flex items-center gap-2 p-3 rounded-lg border {{ $tech->is_active ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                                        <input type="checkbox"
                                               name="technologies[{{ $tech->id }}]"
                                               id="tech_{{ $tech->id }}"
                                               value="1"
                                               {{ $tech->is_active ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <label for="tech_{{ $tech->id }}" class="text-sm font-medium text-gray-900 cursor-pointer">
                                            {{ $tech->technology }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Other Services -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Other Services</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($site->technologies->whereIn('technology', ['ILL', 'SIP', 'IPTV']) as $tech)
                                    <div class="flex items-center gap-2 p-3 rounded-lg border {{ $tech->is_active ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                                        <input type="checkbox"
                                               name="technologies[{{ $tech->id }}]"
                                               id="tech_{{ $tech->id }}"
                                               value="1"
                                               {{ $tech->is_active ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <label for="tech_{{ $tech->id }}" class="text-sm font-medium text-gray-900 cursor-pointer">
                                            {{ $tech->technology }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-gray-500">
                        <strong>Note:</strong> Enabled technologies will be available for selection when creating incidents for this site.
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('sites.show', $site) }}"
                       class="inline-flex items-center gap-2 rounded-2xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-semibold text-white hover:from-blue-700 hover:to-blue-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
