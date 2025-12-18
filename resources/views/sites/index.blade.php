@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Monitored Sites
            </h2>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base lg:text-lg text-gray-600 font-medium">Active and monitoring sites</p>
        </div>

        <div class="flex gap-2 sm:gap-3">
            @if(Auth::user()->canManageSites())
                <a href="{{ route('sites.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-3 sm:px-5 py-2 sm:py-2.5 font-heading font-semibold text-xs sm:text-sm text-white shadow-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">New Site</span>
                    <span class="sm:hidden">New</span>
                </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="py-4 sm:py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="mb-4 sm:mb-6 rounded-2xl sm:rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg p-4 sm:p-6">
                <form method="GET" action="{{ route('sites.index') }}" class="space-y-3 sm:space-y-4">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by Site ID, Name, Atoll"
                            class="w-full rounded-xl sm:rounded-2xl border border-gray-300/50 px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    </div>

                    <div class="grid grid-cols-2 sm:flex gap-2 sm:gap-3">
                        <select name="atoll" class="rounded-xl border border-gray-300/50 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm">
                            <option value="">All Atolls</option>
                            @foreach($atolls as $atoll)
                                <option value="{{ $atoll }}" {{ request('atoll') == $atoll ? 'selected' : '' }}>{{ $atoll }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="rounded-xl border border-gray-300/50 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>

                        <div class="col-span-2 sm:col-span-1 flex gap-2">
                            <button type="submit" class="flex-1 sm:flex-none rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-1.5 sm:py-2 text-xs sm:text-sm font-heading font-semibold text-white">
                                Apply
                            </button>
                            <a href="{{ route('sites.index') }}" class="flex-1 sm:flex-none rounded-xl border border-gray-300 bg-white px-4 sm:px-6 py-1.5 sm:py-2 text-xs sm:text-sm font-heading font-semibold text-gray-700 text-center">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sites Table -->
            <div class="overflow-hidden rounded-2xl sm:rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                <table class="min-w-full divide-y divide-gray-200/50">
                    <thead class="bg-gradient-to-r from-slate-50/80 to-white/60">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Site ID</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Atoll</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Site Name</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Coverage</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/30 bg-white/60">
                        @forelse($sites as $site)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-4">
                                    <a href="{{ route('sites.show', $site) }}" class="text-blue-600 hover:text-blue-800 font-heading font-semibold hover:underline">
                                        {{ $site->site_id }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">{{ $site->atoll_code }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900">{{ $site->site_name }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900">{{ $site->coverage }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                        @if($site->status === 'Active') bg-green-100 text-green-800
                                        @elseif($site->status === 'Monitoring') bg-blue-100 text-blue-800
                                        @elseif($site->status === 'Maintenance') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $site->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('sites.show', $site) }}" class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-gray-600 to-gray-700 px-3 py-1.5 text-xs font-semibold text-white">
                                            View
                                        </a>
                                        @if(Auth::user()->canManageSites())
                                            <a href="{{ route('sites.edit', $site) }}" class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-3 py-1.5 text-xs font-semibold text-white">
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500">No sites found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sites->hasPages())
                <div class="mt-6">{{ $sites->links() }}</div>
            @endif
        </div>
    </div>
@endsection
