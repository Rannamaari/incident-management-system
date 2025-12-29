@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-900 via-purple-800 to-purple-900 bg-clip-text text-transparent">
                FBB/Supernet Islands
            </h2>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-400 font-medium">Fixed Broadband service locations</p>
        </div>

        <div class="flex gap-2 sm:gap-3">
            @if(Auth::user()->canManageSites())
                <a href="{{ route('fbb-islands.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-purple-600 to-purple-700 px-4 sm:px-5 py-2.5 sm:py-3 font-heading font-semibold text-sm sm:text-base text-white shadow-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 touch-manipulation">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New FBB Island</span>
                </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="py-4 sm:py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="mb-4 sm:mb-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg p-4 sm:p-6">
                <form method="GET" action="{{ route('fbb-islands.index') }}" class="space-y-4">
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by island name, region, or technology"
                            class="w-full rounded-xl sm:rounded-2xl border border-gray-300 dark:border-gray-600/50 pl-12 pr-4 py-3 sm:py-3.5 text-sm sm:text-base shadow-sm focus:border-purple-600 dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-600/20 dark:focus:ring-purple-400/20 touch-manipulation">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <select name="region" class="rounded-xl border border-gray-300 dark:border-gray-600/50 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base touch-manipulation">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>
                                    {{ $region->code }}
                                </option>
                            @endforeach
                        </select>

                        <select name="technology" class="rounded-xl border border-gray-300 dark:border-gray-600/50 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base touch-manipulation">
                            <option value="">All Technologies</option>
                            @foreach($technologies as $tech)
                                <option value="{{ $tech }}" {{ request('technology') == $tech ? 'selected' : '' }}>{{ $tech }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="rounded-xl border border-gray-300 dark:border-gray-600/50 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base touch-manipulation">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 sm:flex-none rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-3 text-sm sm:text-base font-heading font-semibold text-white shadow-lg hover:from-purple-700 hover:to-purple-800 transition-all touch-manipulation">
                            Apply Filters
                        </button>
                        <a href="{{ route('fbb-islands.index') }}" class="flex-1 sm:flex-none rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-6 py-3 text-sm sm:text-base font-heading font-semibold text-gray-700 dark:text-gray-300 text-center hover:bg-gray-50 transition-colors touch-manipulation">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden space-y-4">
                @forelse($fbbIslands as $island)
                    <div class="rounded-2xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg p-4 hover:shadow-xl transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <a href="{{ route('fbb-islands.show', $island) }}" class="text-lg font-heading font-bold text-purple-600 hover:text-purple-800">
                                    {{ $island->full_name }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $island->technology }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                @if($island->is_active) bg-green-100 text-green-800
                                @else bg-gray-100 dark:bg-gray-900 text-gray-800
                                @endif">
                                {{ $island->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('fbb-islands.show', $island) }}" class="flex-1 text-center rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 px-4 py-2.5 text-sm font-semibold text-white hover:from-gray-700 hover:to-gray-800 transition-all touch-manipulation">
                                View
                            </a>
                            @if(Auth::user()->canManageSites())
                                <a href="{{ route('fbb-islands.edit', $island) }}" class="flex-1 text-center rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-2.5 text-sm font-semibold text-white hover:from-purple-700 hover:to-purple-800 transition-all touch-manipulation">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white/50">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 text-base font-medium text-gray-900 dark:text-gray-100">No FBB islands found</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-hidden rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-xl">
                <table class="min-w-full divide-y divide-gray-200/50">
                    <thead class="bg-gradient-to-r from-slate-50/80 to-white/60">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase">Region</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase">Island Name</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase">Technology</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/30 bg-white/60">
                        @forelse($fbbIslands as $island)
                            <tr class="hover:bg-purple-50/30 transition-colors">
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $island->region->code }}</td>
                                <td class="px-4 py-4">
                                    <a href="{{ route('fbb-islands.show', $island) }}" class="text-purple-600 hover:text-purple-800 font-heading font-semibold hover:underline">
                                        {{ $island->island_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ $island->technology }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                        @if($island->is_active) bg-green-100 text-green-800
                                        @else bg-gray-100 dark:bg-gray-900 text-gray-800
                                        @endif">
                                        {{ $island->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('fbb-islands.show', $island) }}" class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-gray-600 to-gray-700 px-3 py-1.5 text-xs font-semibold text-white hover:from-gray-700 hover:to-gray-800 transition-all">
                                            View
                                        </a>
                                        @if(Auth::user()->canManageSites())
                                            <a href="{{ route('fbb-islands.edit', $island) }}" class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-purple-600 to-purple-700 px-3 py-1.5 text-xs font-semibold text-white hover:from-purple-700 hover:to-purple-800 transition-all">
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">No FBB islands found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fbbIslands->hasPages())
                <div class="mt-6">{{ $fbbIslands->links() }}</div>
            @endif
        </div>
    </div>
@endsection
