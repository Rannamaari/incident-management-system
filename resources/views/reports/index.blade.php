@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-6 border border-white/20">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="font-heading text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            📊 Incident Reports & Analytics
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Comprehensive incident management insights and trends</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full lg:w-auto">
                        <!-- Enhanced Date Range Filter -->
                        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col gap-3 w-full lg:w-auto" id="reportsFilterForm" data-force-get="true">
                            <!-- Quick Presets -->
                            <div class="flex flex-wrap gap-2">
                                <button type="submit" name="preset" value="this_week"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request('preset') == 'this_week' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:border-purple-400' }}">
                                    This Week
                                </button>
                                <button type="submit" name="preset" value="last_week"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request('preset') == 'last_week' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:border-purple-400' }}">
                                    Last Week
                                </button>
                                <button type="submit" name="preset" value="this_month"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request('preset') == 'this_month' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:border-purple-400' }}">
                                    This Month
                                </button>
                                <button type="submit" name="preset" value="last_month"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request('preset') == 'last_month' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:border-purple-400' }}">
                                    Last Month
                                </button>
                                <button type="submit" name="preset" value="this_quarter"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request('preset') == 'this_quarter' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:border-purple-400' }}">
                                    This Quarter
                                </button>
                                <button type="submit" name="preset" value="last_quarter"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request('preset') == 'last_quarter' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:border-purple-400' }}">
                                    Last Quarter
                                </button>
                                <button type="submit" name="preset" value="ytd"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ request('preset') == 'ytd' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:border-purple-400' }}">
                                    YTD
                                </button>
                                <button type="button" onclick="toggleCustomRange()"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ (request('start_date') || request('end_date')) && !request('preset') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:border-purple-400' }}">
                                    Custom
                                </button>
                            </div>

                            <!-- Custom Date Range (hidden by default) -->
                            <div id="customRangeInputs" class="{{ (request('start_date') || request('end_date')) && !request('preset') ? '' : 'hidden' }} flex flex-col sm:flex-row gap-2">
                                <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="From Date"
                                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent min-w-[140px]"
                                       title="Start Date">
                                <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="To Date"
                                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent min-w-[140px]"
                                       title="End Date">
                                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg text-sm font-medium hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                    Apply
                                </button>
                            </div>

                            @if(request()->has('preset') || request('start_date') || request('end_date'))
                            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 transition-all duration-200 inline-flex items-center gap-2 w-fit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                            @endif
                        </form>
                        
                        <!-- Total Incidents Display -->
                        <div class="text-right border-l border-gray-200 dark:border-gray-700 pl-4 relative">
                            @if(request('start_date') || request('end_date'))
                                <div class="absolute -top-2 -right-2 w-3 h-3 bg-purple-500 rounded-full animate-pulse"></div>
                            @endif
                            <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['totalIncidents'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                @if(request('start_date') || request('end_date'))
                                    🔍 Filtered Results
                                @else
                                    📊 Total Incidents
                                @endif
                            </div>
                            @if(request('start_date') || request('end_date'))
                                <div class="text-xs text-purple-600 mt-1 font-medium">
                                    📅 {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'Start' }} - 
                                    {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'End' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Incidents Section -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-lg">{{ $chartData['openIncidents'] }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">Open Incidents</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['openIncidents'] }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-lg">{{ $chartData['criticalIncidents'] }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">Critical</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['criticalIncidents'] }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-sm">{{ $chartData['avgResolutionTime'] }}h</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">Avg Resolution</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['avgResolutionTime'] }}h</div>
                    </div>
                </div>
            </div>

            <!-- SLA Section -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-lg">{{ $chartData['slaBreached'] }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">SLA Breached</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['slaBreached'] }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-lg">{{ $chartData['totalIncidents'] - $chartData['slaBreached'] }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">SLA Achieved</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['totalIncidents'] - $chartData['slaBreached'] }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-lg">{{ $chartData['rcaRequired'] }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">RCA Required</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['rcaRequired'] }}</div>
                    </div>
                </div>
            </div>

            <!-- RCA Section -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-lg">{{ $chartData['rcaStats']['total'] }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">Total RCAs</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['rcaStats']['total'] }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-lg">{{ $chartData['rcaStats']['compliance'] }}%</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">RCA Compliance</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['rcaStats']['compliance'] }}%</div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2 rounded-full transition-all duration-500"
                                 style="width: {{ $chartData['rcaStats']['compliance'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-lg">{{ $chartData['rcaStats']['pending'] }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-heading font-medium text-gray-500 dark:text-gray-400">Pending RCAs</div>
                        <div class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100">{{ $chartData['rcaStats']['pending'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Draft + In Review</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Severity Distribution -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">Incidents by Severity</h3>
                <div class="relative">
                    <canvas id="severityChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">Incidents by Status</h3>
                <div class="relative">
                    <canvas id="statusChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">Monthly Incident Trends</h3>
                <div class="relative">
                    <canvas id="monthlyChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Daily Trends -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">Daily Incident Trends</h3>
                <div class="relative">
                    <canvas id="dailyChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">Incidents by Category</h3>
                <div class="relative">
                    <canvas id="categoryChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Resolution Time Distribution -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">Resolution Time Distribution</h3>
                <div class="relative">
                    <canvas id="resolutionTimeChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Fault Types -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">Incidents by Fault Type</h3>
                <div class="relative">
                    <canvas id="faultTypeChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Outage Types -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">Incidents by Outage Type</h3>
                <div class="relative">
                    <canvas id="outageTypeChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- SLA Performance -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20 lg:col-span-2">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">SLA Performance</h3>
                <div class="relative">
                    <canvas id="slaChart" width="800" height="200"></canvas>
                </div>
            </div>

            <!-- RCA Status Distribution -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">RCA Status Distribution</h3>
                <div class="relative">
                    <canvas id="rcaStatusChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- RCA Compliance Overview -->
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">RCA Compliance Overview</h3>
                <div class="relative">
                    <canvas id="rcaComplianceChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    
    // Add form validation and session monitoring
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('#reportsFilterForm');
        if (form) {
            
            // Add session status indicator
            const sessionIndicator = document.createElement('div');
            sessionIndicator.id = 'session-status';
            sessionIndicator.className = 'hidden fixed top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs z-50';
            sessionIndicator.textContent = '🟢 Session Active';
            document.body.appendChild(sessionIndicator);
            
            // Check session status periodically
            function checkSession() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const csrfContent = csrfToken ? csrfToken.getAttribute('content') : null;
                
                if (csrfToken && csrfContent && csrfContent.length > 20) {
                    sessionIndicator.className = sessionIndicator.className.replace('bg-red-500', 'bg-green-500');
                    sessionIndicator.textContent = '🟢 Session Active';
                } else {
                    sessionIndicator.className = sessionIndicator.className.replace('bg-green-500', 'bg-red-500');
                    sessionIndicator.textContent = '🔴 Session Expired';
                }
            }
            
            // Check session every 30 seconds
            setInterval(checkSession, 30000);
            
            form.addEventListener('submit', function(e) {
                const startDate = document.querySelector('input[name="start_date"]').value;
                const endDate = document.querySelector('input[name="end_date"]').value;
                
                // Session check before submission
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const csrfContent = csrfToken ? csrfToken.getAttribute('content') : null;
                
                if (!csrfToken || !csrfContent || csrfContent.length < 20) {
                    e.preventDefault();
                    alert('Your session has expired. Please refresh the page and login again.');
                    window.location.reload();
                    return false;
                }
                
                // Validate date range
                if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                    e.preventDefault();
                    alert('Start date cannot be later than end date.');
                    return false;
                }
                
                // Show loading state  
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = '⏳ Loading...';
                }
            });
        }
        
        // Show session indicator after 2 seconds
        setTimeout(function() {
            const indicator = document.querySelector('#session-status');
            if (indicator) {
                indicator.classList.remove('hidden');
                setTimeout(() => indicator.classList.add('hidden'), 3000);
            }
        }, 2000);
    });
    
    // Chart.js default configuration
    Chart.defaults.plugins.legend.position = 'bottom';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 20;

    // Color schemes
    const severityColors = {
        'Critical': '#dc2626',
        'High': '#ea580c', 
        'Medium': '#d97706',
        'Low': '#16a34a'
    };

    const statusColors = {
        'Open': '#dc2626',
        'In Progress': '#ea580c',
        'Monitoring': '#d97706', 
        'Closed': '#16a34a'
    };

    // Severity Chart
    const severityCtx = document.getElementById('severityChart').getContext('2d');
    const severityData = @json($chartData['severityData']);
    new Chart(severityCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(severityData),
            datasets: [{
                data: Object.values(severityData),
                backgroundColor: Object.keys(severityData).map(key => severityColors[key]),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Status Chart  
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($chartData['statusData']);
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: Object.keys(statusData).map(key => statusColors[key]),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = @json($chartData['monthlyTrends']);
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.labels,
            datasets: [{
                label: 'Incidents',
                data: monthlyData.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Daily Trends Chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyData = @json($chartData['dailyTrends']);
    new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: dailyData.labels,
            datasets: [{
                label: 'Daily Incidents',
                data: dailyData.data,
                backgroundColor: 'rgba(139, 92, 246, 0.8)',
                borderColor: '#8b5cf6',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($chartData['categoryData']);
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: categoryData.labels,
            datasets: [{
                label: 'Incidents',
                data: categoryData.data,
                backgroundColor: [
                    '#3b82f6',
                    '#10b981', 
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#06b6d4'
                ],
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Resolution Time Chart
    const resolutionTimeCtx = document.getElementById('resolutionTimeChart').getContext('2d');
    const resolutionTimeData = @json($chartData['resolutionTimeData']);
    new Chart(resolutionTimeCtx, {
        type: 'doughnut',
        data: {
            labels: resolutionTimeData.labels,
            datasets: [{
                data: resolutionTimeData.data,
                backgroundColor: [
                    '#10b981',
                    '#3b82f6',
                    '#f59e0b',
                    '#ef4444',
                    '#7c2d12'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Fault Type Chart
    const faultTypeCtx = document.getElementById('faultTypeChart').getContext('2d');
    const faultTypeData = @json($chartData['faultTypeData']);
    new Chart(faultTypeCtx, {
        type: 'pie',
        data: {
            labels: faultTypeData.labels,
            datasets: [{
                data: faultTypeData.data,
                backgroundColor: [
                    '#8b5cf6',
                    '#06b6d4',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#6366f1'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Outage Type Chart
    const outageTypeCtx = document.getElementById('outageTypeChart').getContext('2d');
    const outageTypeData = @json($chartData['outageTypeData']);
    new Chart(outageTypeCtx, {
        type: 'polarArea',
        data: {
            labels: outageTypeData.labels,
            datasets: [{
                data: outageTypeData.data,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(139, 92, 246, 0.7)',
                    'rgba(6, 182, 212, 0.7)',
                    'rgba(99, 102, 241, 0.7)',
                    'rgba(236, 72, 153, 0.7)',
                    'rgba(34, 197, 94, 0.7)'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // SLA Performance Chart
    const slaCtx = document.getElementById('slaChart').getContext('2d');
    const slaData = @json($chartData['slaPerformance']);
    new Chart(slaCtx, {
        type: 'doughnut',
        data: {
            labels: slaData.labels,
            datasets: [{
                data: slaData.data,
                backgroundColor: ['#16a34a', '#dc2626'],
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // RCA Status Distribution Chart
    const rcaStatusCtx = document.getElementById('rcaStatusChart').getContext('2d');
    const rcaStatusData = @json($chartData['rcaStatusData']);
    new Chart(rcaStatusCtx, {
        type: 'doughnut',
        data: {
            labels: rcaStatusData.labels,
            datasets: [{
                data: rcaStatusData.data,
                backgroundColor: [
                    '#f59e0b', // Draft - amber
                    '#3b82f6', // In Review - blue
                    '#10b981', // Approved - green
                    '#6b7280'  // Closed - gray
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // RCA Compliance Chart
    const rcaComplianceCtx = document.getElementById('rcaComplianceChart').getContext('2d');
    const rcaStats = @json($chartData['rcaStats']);
    new Chart(rcaComplianceCtx, {
        type: 'bar',
        data: {
            labels: ['Required', 'Created', 'Gap'],
            datasets: [{
                label: 'RCAs',
                data: [
                    rcaStats.required + rcaStats.created,
                    rcaStats.created,
                    rcaStats.required
                ],
                backgroundColor: [
                    'rgba(139, 92, 246, 0.8)',  // Total - purple
                    'rgba(16, 185, 129, 0.8)',  // Created - green
                    'rgba(239, 68, 68, 0.8)'    // Gap - red
                ],
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Toggle custom date range visibility
    function toggleCustomRange() {
        const customRangeInputs = document.getElementById('customRangeInputs');
        if (customRangeInputs.classList.contains('hidden')) {
            customRangeInputs.classList.remove('hidden');
        } else {
            customRangeInputs.classList.add('hidden');
        }
    }
</script>
@endsection