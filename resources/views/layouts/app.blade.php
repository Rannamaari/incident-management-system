<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS via CDN -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js for navigation interactions -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50 min-h-screen">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200/50 relative z-0">
                    <div class="max-w-7xl mx-auto py-6 lg:py-8 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="min-h-screen">
                <!-- Success Message -->
                @if (session('success'))
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-sm backdrop-blur-sm" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium whitespace-pre-line">{{ session('success') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Error Message -->
                @if (session('error'))
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-sm backdrop-blur-sm" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium whitespace-pre-line">{{ session('error') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Import Errors -->
                @if (session('import_errors') && count(session('import_errors')) > 0)
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-xl shadow-sm backdrop-blur-sm" role="alert">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="font-semibold mb-2">Import Errors (showing first 20):</h3>
                                    <ul class="list-disc list-inside space-y-1 text-sm max-h-60 overflow-y-auto">
                                        @foreach(array_slice(session('import_errors'), 0, 20) as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                        @if(count(session('import_errors')) > 20)
                                            <li class="font-medium">... and {{ count(session('import_errors')) - 20 }} more errors</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-sm backdrop-blur-sm" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-200/50 mt-auto">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="text-center text-sm text-gray-500">
                        System developed and designed by Ibrahim Husham Abdullah
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>