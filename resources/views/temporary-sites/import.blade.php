@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Import Temporary Sites
            </h2>
            <p class="mt-2 text-lg text-gray-600 font-medium">Bulk import sites from CSV file or pasted text</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('temporary-sites.index') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-2.5 text-white shadow-lg hover:from-gray-800 hover:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to List
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <!-- Instructions -->
            <div class="mb-6 rounded-3xl border border-blue-100/50 bg-blue-50/80 backdrop-blur-sm shadow-lg p-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-600 shadow-md flex-shrink-0">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-heading font-semibold text-blue-900 mb-2">Import Instructions</h3>
                        <div class="text-sm text-blue-800 space-y-2">
                            <p><strong>CSV Format:</strong> Your CSV should have these columns (in order):</p>
                            <p class="font-mono text-xs bg-white/50 rounded p-2">
                                temp_site_id, atoll_code, site_name, coverage, added_date, transmission_or_backhaul, remarks
                            </p>
                            <p><strong>Date Format:</strong> Dates can be in DD-MM-YYYY or MM-DD-YYYY format (e.g., 06-12-2023 or 12/06/2023)</p>
                            <p><strong>Duplicate Handling:</strong> If a Temp Site ID already exists, it will be updated with the new data.</p>
                            <p><strong>Headers:</strong> The first row can optionally contain headers (they will be automatically detected and skipped).</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Form -->
            <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-green-600 to-green-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-semibold text-gray-900">Choose Import Method</h3>
                            <p class="text-sm text-gray-600">Upload a CSV file or paste CSV text directly</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('temporary-sites.import.process') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Import Type Selection -->
                        <div>
                            <label class="block text-sm font-heading font-medium text-gray-700 mb-3">Import Method</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex items-start p-4 border-2 rounded-2xl cursor-pointer transition-all duration-300 hover:bg-blue-50/50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                                    <input type="radio" name="import_type" value="file" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-600" checked onclick="toggleImportType('file')">
                                    <div class="ml-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="font-heading font-semibold text-gray-900">Upload CSV File</span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-600">Select a CSV file from your computer</p>
                                    </div>
                                </label>

                                <label class="relative flex items-start p-4 border-2 rounded-2xl cursor-pointer transition-all duration-300 hover:bg-blue-50/50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                                    <input type="radio" name="import_type" value="text" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-600" onclick="toggleImportType('text')">
                                    <div class="ml-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="font-heading font-semibold text-gray-900">Paste CSV Text</span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-600">Copy and paste CSV data directly</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div id="fileUpload">
                            <label for="csv_file" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                CSV File
                            </label>
                            <div class="relative">
                                <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt"
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('csv_file') border-red-300 @enderror">
                                @error('csv_file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Accepted formats: .csv, .txt (max 10MB)</p>
                        </div>

                        <!-- Text Paste -->
                        <div id="textPaste" class="hidden">
                            <label for="csv_text" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                CSV Text
                            </label>
                            <textarea name="csv_text" id="csv_text" rows="12"
                                class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white font-mono text-sm @error('csv_text') border-red-300 @enderror"
                                placeholder="Paste your CSV data here. Each row should be on a new line.&#10;Example:&#10;TS001,AA,AA_Veligandu_Resort,2G/3G/4G,06-12-2023,Rasdhoo-Veligandu link,Resort under renovation&#10;TS002,ADh,Adh_Holiday_Island_Resort,2G/3G/4G,01-11-2024,HolidayIsland-Maamigili link,Frequent power outages"></textarea>
                            @error('csv_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Paste CSV data with comma-separated values. One site per line.</p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('temporary-sites.index') }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-green-600 to-green-700 px-6 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-green-700 hover:to-green-800 transform">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Import Sites
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Example Format -->
            <div class="mt-6 rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg p-6">
                <h3 class="font-heading font-semibold text-gray-900 mb-3">Example CSV Format</h3>
                <div class="bg-gray-50 rounded-xl p-4 overflow-x-auto">
                    <pre class="text-xs font-mono text-gray-700">temp_site_id,atoll_code,site_name,coverage,added_date,transmission_or_backhaul,remarks
TS001,AA,AA_Veligandu_Resort,2G/3G/4G,06-12-2023,Rasdhoo-Veligandu link,Resort closed for renovation
TS002,ADh,Adh_Holiday_Island_Resort,2G/3G/4G,01-11-2024,HolidayIsland-Maamigili link,Frequent power outages
TS003,B,B_Kihaadhuffaru_Resort,3G/4G,13-09-2025,Kihaadhuffaru-Dharavandhoo link,Under construction</pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleImportType(type) {
            const fileUpload = document.getElementById('fileUpload');
            const textPaste = document.getElementById('textPaste');
            const fileInput = document.getElementById('csv_file');
            const textArea = document.getElementById('csv_text');

            if (type === 'file') {
                fileUpload.classList.remove('hidden');
                textPaste.classList.add('hidden');
                fileInput.required = true;
                textArea.required = false;
            } else {
                fileUpload.classList.add('hidden');
                textPaste.classList.remove('hidden');
                fileInput.required = false;
                textArea.required = true;
            }
        }
    </script>
@endsection
