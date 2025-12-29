@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Temporary Sites
            </h2>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-400 font-medium">Master list of temporary sites and their status</p>
        </div>

        <div class="flex gap-2 sm:gap-3">
            @if(Auth::user()->canManageTemporarySites())
                <a href="{{ route('temporary-sites.import') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-green-600 to-green-700 px-3 sm:px-5 py-2 sm:py-2.5 font-heading font-semibold text-xs sm:text-sm text-white shadow-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span class="hidden sm:inline">Import</span>
                    <span class="sm:hidden">Import</span>
                </a>
                <a href="{{ route('temporary-sites.create') }}"
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
            <div class="mb-4 sm:mb-6 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg p-4 sm:p-6">
                <form method="GET" action="{{ route('temporary-sites.index') }}" class="space-y-3 sm:space-y-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-xs sm:text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-1.5 sm:mb-2">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by Temp ID, Site Name, Atoll, or Backhaul"
                            class="w-full rounded-xl sm:rounded-2xl border border-gray-300 dark:border-gray-600/50 px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300">
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-2 sm:flex gap-2 sm:gap-3">
                        <div class="flex-1">
                            <label for="atoll" class="block text-xs font-heading font-medium text-gray-700 dark:text-gray-300 mb-1">Atoll</label>
                            <select name="atoll" id="atoll"
                                class="w-full rounded-xl border border-gray-300 dark:border-gray-600/50 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300">
                                <option value="">All Atolls</option>
                                @foreach($atolls as $atoll)
                                    <option value="{{ $atoll }}" {{ request('atoll') == $atoll ? 'selected' : '' }}>{{ $atoll }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex-1">
                            <label for="coverage" class="block text-xs font-heading font-medium text-gray-700 dark:text-gray-300 mb-1">Coverage</label>
                            <select name="coverage" id="coverage"
                                class="w-full rounded-xl border border-gray-300 dark:border-gray-600/50 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300">
                                <option value="">All Coverage</option>
                                @foreach($coverages as $coverage)
                                    <option value="{{ $coverage }}" {{ request('coverage') == $coverage ? 'selected' : '' }}>{{ $coverage }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex-1">
                            <label for="status" class="block text-xs font-heading font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" id="status"
                                class="w-full rounded-xl border border-gray-300 dark:border-gray-600/50 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300">
                                <option value="">All Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2 sm:col-span-1 flex gap-2">
                            <button type="submit"
                                class="flex-1 sm:flex-none rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-1.5 sm:py-2 text-xs sm:text-sm font-heading font-semibold text-white shadow-sm hover:from-blue-700 hover:to-blue-800 transition-all duration-300">
                                Apply
                            </button>
                            <a href="{{ route('temporary-sites.index') }}"
                                class="flex-1 sm:flex-none rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 sm:px-6 py-1.5 sm:py-2 text-xs sm:text-sm font-heading font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 transition-all duration-300 text-center">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bulk Delete Form -->
            @if(Auth::user()->canManageTemporarySites())
                <form method="POST" action="{{ route('temporary-sites.bulk-delete') }}" id="bulkDeleteForm" class="mb-4">
                    @csrf
                    <div id="bulkActionsBar" class="hidden rounded-2xl bg-blue-50 border border-blue-200 dark:border-blue-700 px-4 sm:px-6 py-3 mb-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs sm:text-sm font-medium text-blue-900">
                                <span id="selectedCount">0</span> site(s) selected
                            </span>
                            <button type="submit"
                                onclick="return confirm('Are you sure you want to delete the selected temporary sites? This action cannot be undone.')"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-heading font-semibold text-white shadow-sm hover:from-red-700 hover:to-red-800 transition-all duration-300 active:scale-95">
                                <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span class="hidden sm:inline">Delete Selected</span>
                                <span class="sm:hidden">Delete</span>
                            </button>
                        </div>
                    </div>

                    <div class="hidden sm:block overflow-hidden rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-xl">
                        <table class="min-w-full divide-y divide-gray-200/50">
                            <thead class="bg-gradient-to-r from-slate-50/80 to-white/60">
                                <tr>
                                    <th scope="col" class="px-4 py-4">
                                        <input type="checkbox" id="selectAll"
                                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-600 cursor-pointer">
                                    </th>
                                    <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Temp ID</th>
                                    <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Atoll</th>
                                    <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Site Name</th>
                                    <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Coverage</th>
                                    <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Site Status</th>
                                    <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Added Date</th>
                                    <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/30 bg-white/60">
                                @forelse($temporarySites as $site)
                                    <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                        <td class="px-4 py-4">
                                            <input type="checkbox" name="site_ids[]" value="{{ $site->id }}"
                                                class="site-checkbox h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-600 cursor-pointer">
                                        </td>
                                        <td class="px-4 py-4">
                                            <a href="{{ route('temporary-sites.show', $site) }}" class="text-blue-600 hover:text-blue-800 font-heading font-semibold hover:underline">
                                                {{ $site->temp_site_id }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $site->atoll_code }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $site->site_name }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $site->coverage }}</td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-col gap-1.5">
                                                @if(str_contains(strtolower($site->coverage), '2g'))
                                                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                                                        <input type="checkbox"
                                                               data-site-id="{{ $site->id }}"
                                                               data-tech="2g"
                                                               class="tech-toggle h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-600"
                                                               {{ $site->is_2g_online ? 'checked' : '' }}>
                                                        <span class="text-xs font-medium {{ $site->is_2g_online ? 'text-green-600' : 'text-red-600 dark:text-red-400' }}">
                                                            2G {{ $site->is_2g_online ? 'Online' : 'Offline' }}
                                                        </span>
                                                    </label>
                                                @endif

                                                @if(str_contains(strtolower($site->coverage), '3g'))
                                                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                                                        <input type="checkbox"
                                                               data-site-id="{{ $site->id }}"
                                                               data-tech="3g"
                                                               class="tech-toggle h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-600"
                                                               {{ $site->is_3g_online ? 'checked' : '' }}>
                                                        <span class="text-xs font-medium {{ $site->is_3g_online ? 'text-green-600' : 'text-red-600 dark:text-red-400' }}">
                                                            3G {{ $site->is_3g_online ? 'Online' : 'Offline' }}
                                                        </span>
                                                    </label>
                                                @endif

                                                @if(str_contains(strtolower($site->coverage), '4g'))
                                                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                                                        <input type="checkbox"
                                                               data-site-id="{{ $site->id }}"
                                                               data-tech="4g"
                                                               class="tech-toggle h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-600"
                                                               {{ $site->is_4g_online ? 'checked' : '' }}>
                                                        <span class="text-xs font-medium {{ $site->is_4g_online ? 'text-green-600' : 'text-red-600 dark:text-red-400' }}">
                                                            4G {{ $site->is_4g_online ? 'Online' : 'Offline' }}
                                                        </span>
                                                    </label>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $site->added_date->format('d M Y') }}</td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                                @if($site->status === 'Temporary') bg-yellow-100 text-yellow-800
                                                @elseif($site->status === 'Resolved') bg-green-100 text-green-800
                                                @elseif($site->status === 'Monitoring') bg-blue-100 text-blue-800
                                                @else bg-gray-100 dark:bg-gray-900 text-gray-800
                                                @endif">
                                                {{ $site->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('temporary-sites.show', $site) }}"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-gray-600 to-gray-700 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:from-gray-700 hover:to-gray-800 transition-all duration-300">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                                <a href="{{ route('temporary-sites.edit', $site) }}"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:from-blue-700 hover:to-blue-800 transition-all duration-300">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('temporary-sites.destroy', $site) }}" class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this temporary site? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:from-red-700 hover:to-red-800 transition-all duration-300">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-12 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">No temporary sites found</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter to find what you're looking for.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="sm:hidden space-y-3">
                        @forelse($temporarySites as $site)
                            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg overflow-hidden">
                                <div class="p-3">
                                    <div class="flex items-start gap-2.5 mb-3">
                                        <input type="checkbox" name="site_ids[]" value="{{ $site->id }}"
                                            class="site-checkbox h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-600 cursor-pointer mt-0.5">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('temporary-sites.show', $site) }}" class="font-heading font-bold text-blue-600 hover:text-blue-800 text-sm mb-1 block">
                                                {{ $site->temp_site_id }}
                                            </a>
                                            <p class="text-xs text-gray-900 dark:text-gray-100 mb-1">{{ $site->site_name }}</p>
                                            <div class="flex flex-wrap gap-1.5 text-xs text-gray-600 dark:text-gray-400 mb-2">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    {{ $site->atoll_code }}
                                                </span>
                                                <span>•</span>
                                                <span>{{ $site->coverage }}</span>
                                                <span>•</span>
                                                <span>{{ $site->added_date->format('d M Y') }}</span>
                                            </div>
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                                                @if($site->status === 'Temporary') bg-yellow-100 text-yellow-800
                                                @elseif($site->status === 'Resolved') bg-green-100 text-green-800
                                                @elseif($site->status === 'Monitoring') bg-blue-100 text-blue-800
                                                @else bg-gray-100 dark:bg-gray-900 text-gray-800
                                                @endif">
                                                {{ $site->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                        <a href="{{ route('temporary-sites.show', $site) }}"
                                            class="flex-1 inline-flex items-center justify-center gap-1 rounded-lg bg-gradient-to-r from-gray-600 to-gray-700 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:from-gray-700 hover:to-gray-800 transition-all duration-300 active:scale-95">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                        <a href="{{ route('temporary-sites.edit', $site) }}"
                                            class="flex-1 inline-flex items-center justify-center gap-1 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:from-blue-700 hover:to-blue-800 transition-all duration-300 active:scale-95">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('temporary-sites.destroy', $site) }}" class="flex-1"
                                            onsubmit="return confirm('Are you sure you want to delete this temporary site? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-1 rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:from-red-700 hover:to-red-800 transition-all duration-300 active:scale-95">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">No temporary sites found</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter.</p>
                            </div>
                        @endforelse
                    </div>
                </form>
            @else
                <!-- Non-admin view (no checkboxes or bulk actions) -->
                <div class="hidden sm:block overflow-hidden rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-xl">
                    <table class="min-w-full divide-y divide-gray-200/50">
                        <thead class="bg-gradient-to-r from-slate-50/80 to-white/60">
                            <tr>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Temp ID</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Atoll</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Site Name</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Coverage</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Added Date</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-heading font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/30 bg-white/60">
                            @forelse($temporarySites as $site)
                                <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                    <td class="px-4 py-4">
                                        <a href="{{ route('temporary-sites.show', $site) }}" class="text-blue-600 hover:text-blue-800 font-heading font-semibold hover:underline">
                                            {{ $site->temp_site_id }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $site->atoll_code }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $site->site_name }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $site->coverage }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $site->added_date->format('d M Y') }}</td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                            @if($site->status === 'Temporary') bg-yellow-100 text-yellow-800
                                            @elseif($site->status === 'Resolved') bg-green-100 text-green-800
                                            @elseif($site->status === 'Monitoring') bg-blue-100 text-blue-800
                                            @else bg-gray-100 dark:bg-gray-900 text-gray-800
                                            @endif">
                                            {{ $site->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium">
                                        <a href="{{ route('temporary-sites.show', $site) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-gray-600 to-gray-700 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:from-gray-700 hover:to-gray-800 transition-all duration-300">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">No temporary sites found</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter to find what you're looking for.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards for non-admin -->
                <div class="sm:hidden space-y-3">
                    @forelse($temporarySites as $site)
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg overflow-hidden">
                            <div class="p-3">
                                <a href="{{ route('temporary-sites.show', $site) }}" class="font-heading font-bold text-blue-600 hover:text-blue-800 text-sm mb-1 block">
                                    {{ $site->temp_site_id }}
                                </a>
                                <p class="text-xs text-gray-900 dark:text-gray-100 mb-1">{{ $site->site_name }}</p>
                                <div class="flex flex-wrap gap-1.5 text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $site->atoll_code }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $site->coverage }}</span>
                                    <span>•</span>
                                    <span>{{ $site->added_date->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                                        @if($site->status === 'Temporary') bg-yellow-100 text-yellow-800
                                        @elseif($site->status === 'Resolved') bg-green-100 text-green-800
                                        @elseif($site->status === 'Monitoring') bg-blue-100 text-blue-800
                                        @else bg-gray-100 dark:bg-gray-900 text-gray-800
                                        @endif">
                                        {{ $site->status }}
                                    </span>
                                    <a href="{{ route('temporary-sites.show', $site) }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-gray-600 to-gray-700 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:from-gray-700 hover:to-gray-800 transition-all duration-300 active:scale-95">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">No temporary sites found</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            <!-- Pagination -->
            @if($temporarySites->hasPages())
                <div class="mt-6">
                    {{ $temporarySites->links() }}
                </div>
            @endif
        </div>
    </div>

    @if(Auth::user()->canManageTemporarySites())
        <script>
            // Bulk selection functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const siteCheckboxes = document.querySelectorAll('.site-checkbox');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCount = document.getElementById('selectedCount');

            function updateBulkActions() {
                const checkedCount = document.querySelectorAll('.site-checkbox:checked').length;
                selectedCount.textContent = checkedCount;

                if (checkedCount > 0) {
                    bulkActionsBar.classList.remove('hidden');
                } else {
                    bulkActionsBar.classList.add('hidden');
                }

                // Update select all checkbox state
                if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === siteCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }

            selectAllCheckbox?.addEventListener('change', function() {
                siteCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            siteCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });
        </script>
    @endif

    <!-- Technology Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const techToggles = document.querySelectorAll('.tech-toggle');

            techToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const siteId = this.dataset.siteId;
                    const tech = this.dataset.tech;
                    const isOnline = this.checked;
                    const label = this.nextElementSibling;

                    // Update UI immediately for better UX
                    if (isOnline) {
                        label.textContent = `${tech.toUpperCase()} Online`;
                        label.classList.remove('text-red-600 dark:text-red-400');
                        label.classList.add('text-green-600');
                    } else {
                        label.textContent = `${tech.toUpperCase()} Offline`;
                        label.classList.remove('text-green-600');
                        label.classList.add('text-red-600 dark:text-red-400');
                    }

                    // Send AJAX request to update the database
                    fetch(`/temporary-sites/${siteId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            tech: tech,
                            is_online: isOnline
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Status updated successfully');
                        } else {
                            console.error('Failed to update status');
                            // Revert UI changes
                            this.checked = !isOnline;
                            if (!isOnline) {
                                label.textContent = `${tech.toUpperCase()} Online`;
                                label.classList.remove('text-red-600 dark:text-red-400');
                                label.classList.add('text-green-600');
                            } else {
                                label.textContent = `${tech.toUpperCase()} Offline`;
                                label.classList.remove('text-green-600');
                                label.classList.add('text-red-600 dark:text-red-400');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert UI changes
                        this.checked = !isOnline;
                        if (!isOnline) {
                            label.textContent = `${tech.toUpperCase()} Online`;
                            label.classList.remove('text-red-600 dark:text-red-400');
                            label.classList.add('text-green-600');
                        } else {
                            label.textContent = `${tech.toUpperCase()} Offline`;
                            label.classList.remove('text-green-600');
                            label.classList.add('text-red-600 dark:text-red-400');
                        }
                    });
                });
            });
        });
    </script>
@endsection
