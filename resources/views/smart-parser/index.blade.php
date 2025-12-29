@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-purple-900 via-purple-800 to-purple-900 bg-clip-text text-transparent">
                AI Logger
            </h2>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 font-medium">Instantly create incidents from messages</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('incidents.index') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-2.5 text-white shadow-lg hover:from-gray-800 hover:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 1200px;">

            <!-- Info Section (Collapsible) -->
            <div x-data="{ open: false }" class="mb-6 rounded-2xl border border-purple-100 bg-gradient-to-r from-purple-50/50 to-indigo-50/50 backdrop-blur-sm shadow-sm">
                <!-- Header (Always Visible) -->
                <button @click="open = !open" class="w-full p-6 flex items-center justify-between hover:bg-purple-50/30 transition-colors rounded-2xl">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-600 to-purple-700 shadow-md">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-heading text-sm font-heading font-semibold uppercase tracking-wide text-purple-900">
                                How it works
                            </h4>
                            <p class="text-xs text-purple-600 mt-0.5">Click to expand</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-purple-600 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Collapsible Content -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="px-6 pb-6"
                     style="display: none;">
                    <div class="space-y-2 text-sm text-purple-800 border-t border-purple-200 dark:border-purple-700/50 pt-4">
                        <p>This tool intelligently parses incident closure messages and automatically extracts key details:</p>
                        <ul class="ml-4 space-y-1">
                            <li>• <strong>Summary:</strong> Service names, cell IDs, or affected elements</li>
                            <li>• <strong>Outage Category:</strong> Automatically detects Power, RAN, Transmission, etc.</li>
                            <li>• <strong>Category:</strong> Identifies FBB, RAN, International, Enterprise</li>
                            <li>• <strong>Duration:</strong> Extracts duration and calculates outage start time</li>
                            <li>• <strong>Root Cause:</strong> Captures the cause description</li>
                            <li>• <strong>Status:</strong> Detects Open (down) or Closed (on service)</li>
                        </ul>
                        <p class="mt-3 text-purple-700 dark:text-purple-400 font-medium">Simply paste your message below and let the AI do the work!</p>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg">
                <!-- Section Header -->
                <div class="border-b border-gray-200 dark:border-gray-700/50 bg-gradient-to-r from-purple-50/80 to-white/60 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-600 to-purple-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100">Paste Incident Closure Message</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">The AI will extract and populate all fields automatically</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('smart-parser.parse') }}" class="space-y-6">
                        @csrf

                        <!-- Incident Message Textarea -->
                        <div>
                            <label for="incident_message" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Incident Closure Message <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="incident_message"
                                id="incident_message"
                                rows="12"
                                placeholder="Example:&#10;&#10;GA_Kondey FBB is on service since 1220hrs 21/12/2025&#10;Duration: 30mins&#10;Cause: Local power failure.&#10;&#10;OR&#10;&#10;Below mentioned cells are on service since 1042hrs 21/12/2025&#10;AA_Kandholhudhoo_Resort_U900-3752B&#10;AA_Kandholhudhoo_Resort_G900-3751B&#10;AA_Kandholhudhoo_Resort_L900_B&#10;Duration: 2hrs 12mins&#10;Cause: Under investigation. Cells came on service after Resort IT gave power reset to RRU."
                                class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-purple-600 dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-600/20 dark:focus:ring-purple-400/20 bg-white dark:bg-gray-800 transition-all duration-300 resize-y font-mono text-sm @error('incident_message') border-red-300 dark:border-red-700 @enderror"
                            >{{ old('incident_message') }}</textarea>
                            @error('incident_message')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Paste the complete incident closure message including service details, duration, and cause
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('incidents.index') }}"
                                class="rounded-xl bg-gray-200 px-6 py-3 font-medium text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400/30 transition-all duration-300">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:from-purple-700 hover:to-purple-800 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-purple-400/30">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                Parse Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Example Messages -->
            <div class="mt-6 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 backdrop-blur-sm p-6">
                <h4 class="font-heading mb-4 text-sm font-heading font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                    Example Messages
                </h4>

                <div class="space-y-4">
                    <!-- Example 1 -->
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <h5 class="text-xs font-semibold text-purple-600 mb-2">Single FBB Outage (Closed)</h5>
                                <pre class="text-xs text-gray-700 dark:text-gray-300 font-mono whitespace-pre-wrap">Gn_Fuvahmulah FBB is on service since 1454hrs 18/12/2025
Duration: 40mins
Cause: Local Power Failure</pre>
                                <span class="inline-flex items-center mt-2 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Status: Closed
                                </span>
                            </div>
                            <button type="button" onclick="copyExample(1)"
                                class="flex-shrink-0 text-purple-600 hover:text-purple-700 dark:text-purple-400 text-xs font-medium">
                                Copy
                            </button>
                        </div>
                    </div>

                    <!-- Example 2 -->
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <h5 class="text-xs font-semibold text-purple-600 mb-2">Multiple Cell (RAN) Outage (Closed)</h5>
                                <pre class="text-xs text-gray-700 dark:text-gray-300 font-mono whitespace-pre-wrap">Below mentioned cells are on service since 1909hrs 20/12/2025
K_Hulhumale_TreeTop_ATM_AAU_L1800A,B
K_Hulhumale_TreeTop_ATM_AAU_L2100A,B
K_Hulhumale_TreeTop_ATM_AAU_U2100-2762A,B
Duration: 7hrs 10mins
Cause: Local power failure from pole.</pre>
                                <span class="inline-flex items-center mt-2 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Status: Closed
                                </span>
                            </div>
                            <button type="button" onclick="copyExample(2)"
                                class="flex-shrink-0 text-purple-600 hover:text-purple-700 dark:text-purple-400 text-xs font-medium">
                                Copy
                            </button>
                        </div>
                    </div>

                    <!-- Example 3 -->
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <h5 class="text-xs font-semibold text-purple-600 mb-2">Single Site (5G) Outage (Closed)</h5>
                                <pre class="text-xs text-gray-700 dark:text-gray-300 font-mono whitespace-pre-wrap">GDh_Thinadhoo 5G is on service since 1708hrs 18/12/2025
Duration: 41mins
Cause: DCDU Breaker Tripped during power outage. Site on service after power was restored and battery breaker was turned on.</pre>
                                <span class="inline-flex items-center mt-2 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Status: Closed
                                </span>
                            </div>
                            <button type="button" onclick="copyExample(3)"
                                class="flex-shrink-0 text-purple-600 hover:text-purple-700 dark:text-purple-400 text-xs font-medium">
                                Copy
                            </button>
                        </div>
                    </div>

                    <!-- Example 4 - Open Incident -->
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <h5 class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">Ongoing Outage (Open)</h5>
                                <pre class="text-xs text-gray-700 dark:text-gray-300 font-mono whitespace-pre-wrap">AA_Mahibadhoo FBB is down since 1430hrs 21/12/2025
Cause: Under investigation</pre>
                                <span class="inline-flex items-center mt-2 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Status: Open
                                </span>
                            </div>
                            <button type="button" onclick="copyExample(4)"
                                class="flex-shrink-0 text-purple-600 hover:text-purple-700 dark:text-purple-400 text-xs font-medium">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const examples = {
            1: `Gn_Fuvahmulah FBB is on service since 1454hrs 18/12/2025
Duration: 40mins
Cause: Local Power Failure`,
            2: `Below mentioned cells are on service since 1909hrs 20/12/2025
K_Hulhumale_TreeTop_ATM_AAU_L1800A,B
K_Hulhumale_TreeTop_ATM_AAU_L2100A,B
K_Hulhumale_TreeTop_ATM_AAU_U2100-2762A,B
Duration: 7hrs 10mins
Cause: Local power failure from pole.`,
            3: `GDh_Thinadhoo 5G is on service since 1708hrs 18/12/2025
Duration: 41mins
Cause: DCDU Breaker Tripped during power outage. Site on service after power was restored and battery breaker was turned on.`,
            4: `AA_Mahibadhoo FBB is down since 1430hrs 21/12/2025
Cause: Under investigation`
        };

        function copyExample(exampleNumber) {
            const textarea = document.getElementById('incident_message');
            textarea.value = examples[exampleNumber];
            textarea.focus();

            // Show feedback
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        }
    </script>
@endsection
