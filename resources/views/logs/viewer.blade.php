@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 border-b border-gray-300 dark:border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laravel Logs</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            View and manage application logs
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('logs.download') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </a>
                        <form method="POST" action="{{ route('logs.clear') }}" class="inline"
                              onsubmit="return confirm('Are you sure you want to clear all logs? This cannot be undone.');">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 dark:border-red-600 rounded-md shadow-sm text-sm font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Clear Logs
                            </button>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mt-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- File Info -->
        @if(!$noFile)
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="bg-white dark:bg-slate-900 border border-gray-300 dark:border-white/10 rounded-lg p-4">
                    <div class="flex flex-wrap gap-6 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Total Lines:</span>
                            <span class="ml-2 font-semibold text-gray-900 dark:text-white">{{ number_format($totalLines) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">File Size:</span>
                            <span class="ml-2 font-semibold text-gray-900 dark:text-white">{{ $fileSize }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Last Modified:</span>
                            <span class="ml-2 font-semibold text-gray-900 dark:text-white">{{ $lastModified }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Showing:</span>
                            <span class="ml-2 font-semibold text-gray-900 dark:text-white">{{ count($logs) }} entries</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" action="{{ route('logs.viewer') }}" class="bg-white dark:bg-slate-900 border border-gray-300 dark:border-white/10 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Number of Lines -->
                    <div>
                        <label for="lines" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Lines to Show
                        </label>
                        <select name="lines" id="lines"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                            <option value="50" {{ $currentLines == 50 ? 'selected' : '' }}>Last 50</option>
                            <option value="100" {{ $currentLines == 100 ? 'selected' : '' }}>Last 100</option>
                            <option value="200" {{ $currentLines == 200 ? 'selected' : '' }}>Last 200</option>
                            <option value="500" {{ $currentLines == 500 ? 'selected' : '' }}>Last 500</option>
                            <option value="1000" {{ $currentLines == 1000 ? 'selected' : '' }}>Last 1000</option>
                        </select>
                    </div>

                    <!-- Log Level -->
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Log Level
                        </label>
                        <select name="level" id="level"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                            <option value="" {{ $currentLevel == '' ? 'selected' : '' }}>All Levels</option>
                            <option value="error" {{ $currentLevel == 'error' ? 'selected' : '' }}>Error</option>
                            <option value="warning" {{ $currentLevel == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="info" {{ $currentLevel == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="debug" {{ $currentLevel == 'debug' ? 'selected' : '' }}>Debug</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Search
                        </label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ $currentSearch }}"
                               placeholder="Search logs..."
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    </div>

                    <!-- Submit -->
                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                            Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Auto-refresh toggle -->
                <div class="mt-4 flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="autoRefresh" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Auto-refresh every 10 seconds</span>
                    </label>
                </div>
            </form>
        </div>

        <!-- Logs Display -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            @if($noFile)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-yellow-600 dark:text-yellow-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2">Log File Not Found</h3>
                    <p class="text-yellow-700 dark:text-yellow-400">
                        The log file does not exist yet. It will be created when the application logs its first message.
                    </p>
                </div>
            @elseif(count($logs) == 0)
                <div class="bg-gray-50 dark:bg-slate-900 border border-gray-300 dark:border-white/10 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Logs Found</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        No log entries match your current filters. Try adjusting your search criteria.
                    </p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($logs as $log)
                        <div class="bg-white dark:bg-slate-900 border-l-4 border border-gray-300 dark:border-white/10 rounded-lg
                                    {{ $log['level'] == 'error' ? 'border-l-red-500' : '' }}
                                    {{ $log['level'] == 'warning' ? 'border-l-yellow-500' : '' }}
                                    {{ $log['level'] == 'info' ? 'border-l-blue-500' : '' }}
                                    {{ $log['level'] == 'debug' ? 'border-l-gray-500' : '' }}">
                            <div class="p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold uppercase
                                                     {{ $log['level'] == 'error' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}
                                                     {{ $log['level'] == 'warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                                     {{ $log['level'] == 'info' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                                     {{ $log['level'] == 'debug' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                            {{ $log['level'] }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $log['timestamp'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-sm text-gray-900 dark:text-gray-100 font-mono whitespace-pre-wrap break-all">
                                    {{ $log['message'] }}
                                </div>

                                @if(count($log['stack']) > 0)
                                    <details class="mt-2">
                                        <summary class="cursor-pointer text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                                            Show stack trace ({{ count($log['stack']) }} lines)
                                        </summary>
                                        <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded text-xs font-mono text-gray-700 dark:text-gray-300 overflow-x-auto">
                                            @foreach($log['stack'] as $stackLine)
                                                <div>{{ $stackLine }}</div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    </div>
@endsection

@push('scripts')
<script>
    // Auto-refresh functionality
    const autoRefreshCheckbox = document.getElementById('autoRefresh');
    let refreshInterval;

    autoRefreshCheckbox.addEventListener('change', function() {
        if (this.checked) {
            refreshInterval = setInterval(() => {
                window.location.reload();
            }, 10000); // 10 seconds
        } else {
            clearInterval(refreshInterval);
        }
    });

    // Clean up interval on page unload
    window.addEventListener('beforeunload', () => {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
@endpush
