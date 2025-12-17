@extends('layouts.app')

@section('header')
    <!-- Hero -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-blue-50/50 px-4 sm:px-6 lg:px-8 py-8 border-b border-gray-200/30">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg grid place-items-center transform hover:scale-105 transition-all duration-300">
                            <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-heading text-3xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">Import Incidents</h1>
                            <p class="mt-2 text-lg text-gray-600 font-medium">Upload Excel file to import incidents into the system</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('incidents.index') }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gray-100 px-6 py-3 font-semibold text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-200">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Incidents
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
        <!-- Instructions Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200/50 p-6 mb-6">
            <h2 class="font-heading text-xl font-semibold text-gray-900 mb-4">📋 Excel File Requirements</h2>
            <div class="space-y-3 text-sm text-gray-700">
                <div class="flex items-start gap-3">
                    <span class="text-blue-600 font-bold">✓</span>
                    <div>
                        <strong>Required Columns:</strong>
                        <ul class="mt-1 ml-4 list-disc space-y-1">
                            <li><strong>Incident Summary</strong> - Description of the incident (required)</li>
                            <li><strong>Start Date and Time</strong> - When the incident started (required)</li>
                            <li><strong>Severity</strong> - Severity level: Low, Medium, High, or Critical (required)</li>
                            <li><strong>Resolution Date and Time</strong> - When the incident was resolved (optional)</li>
                        </ul>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-blue-600 font-bold">ℹ</span>
                    <div>
                        <strong>File Format:</strong> Excel files (.xlsx or .xls) up to 10MB
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-blue-600 font-bold">💡</span>
                    <div>
                        <strong>Column Name Flexibility:</strong> The system will automatically detect columns with names like:
                        <ul class="mt-1 ml-4 list-disc space-y-1">
                            <li>"Incident Summary", "Summary", "Description" for incident details</li>
                            <li>"Start Date", "Start Date and Time", "Incident Start" for start time</li>
                            <li>"Resolution Date", "Resolved Date", "Resolution Date and Time" for resolution time</li>
                            <li>"Severity", "Severity Level" for severity</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200/50 p-8">
            <form action="{{ route('incidents.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- File Upload -->
                <div>
                    <label for="excel_file" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                        Select Excel File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="excel_file" 
                           id="excel_file" 
                           accept=".xlsx,.xls"
                           required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all duration-300 @error('excel_file') border-red-300 @enderror">
                    <p class="mt-2 text-sm text-gray-500">Accepted formats: .xlsx, .xls (Max size: 10MB)</p>
                    @error('excel_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-blue-700 hover:to-blue-800 transform">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Import Incidents
                    </button>
                    <a href="{{ route('incidents.index') }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gray-100 px-6 py-3 font-semibold text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-200">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Example Format -->
        <div class="bg-gray-50 rounded-2xl border border-gray-200/50 p-6 mt-6">
            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 mb-4">📊 Example Excel Format</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="font-heading px-4 py-2 text-left text-sm font-heading font-semibold text-gray-700 border-b">Incident Summary</th>
                            <th class="font-heading px-4 py-2 text-left text-sm font-heading font-semibold text-gray-700 border-b">Start Date and Time</th>
                            <th class="font-heading px-4 py-2 text-left text-sm font-heading font-semibold text-gray-700 border-b">Resolution Date and Time</th>
                            <th class="font-heading px-4 py-2 text-left text-sm font-heading font-semibold text-gray-700 border-b">Severity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700 border-b">Network outage affecting multiple sites</td>
                            <td class="px-4 py-2 text-sm text-gray-700 border-b">2025-01-15 10:30</td>
                            <td class="px-4 py-2 text-sm text-gray-700 border-b">2025-01-15 14:45</td>
                            <td class="px-4 py-2 text-sm text-gray-700 border-b">High</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700 border-b">Single site connectivity issue</td>
                            <td class="px-4 py-2 text-sm text-gray-700 border-b">2025-01-16 09:00</td>
                            <td class="px-4 py-2 text-sm text-gray-700 border-b"></td>
                            <td class="px-4 py-2 text-sm text-gray-700 border-b">Medium</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

