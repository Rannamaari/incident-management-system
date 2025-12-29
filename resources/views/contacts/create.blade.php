@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Create New Contact
            </h2>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 font-medium">Add a new contact to the phone book</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('contacts.index') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-2.5 text-white shadow-lg hover:from-gray-800 hover:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Phone Book
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-gray-100 dark:border-gray-700/50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-xl">
                <!-- Section Header -->
                <div class="border-b border-gray-200 dark:border-gray-700/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900 dark:text-gray-100">Contact Information</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Fill in the fields below to create a new contact</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('contacts.store') }}" class="space-y-6">
                        @csrf

                        <!-- Basic Information Section -->
                        <div class="space-y-4">
                            <h4 class="font-heading font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Basic Information</h4>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('name') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter contact's full name">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('phone') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter phone number">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('email') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="contact@example.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Organization Information Section -->
                        <div class="space-y-4">
                            <h4 class="font-heading font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Organization Information</h4>

                            <!-- Company -->
                            <div>
                                <label for="company" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Company
                                </label>
                                <input type="text" name="company" id="company" value="{{ old('company') }}"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('company') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter company name">
                                @error('company')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Role/Position
                                </label>
                                <input type="text" name="role" id="role" value="{{ old('role') }}"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('role') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter role or position">
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Category
                                </label>
                                <input type="text" name="category" id="category" value="{{ old('category') }}"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('category') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter category (e.g., GMR, SA, FSO)">
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Location Information Section -->
                        <div class="space-y-4">
                            <h4 class="font-heading font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Location Information</h4>

                            <!-- Region -->
                            <div>
                                <label for="region" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Region
                                </label>
                                <input type="text" name="region" id="region" value="{{ old('region') }}"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('region') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter region">
                                @error('region')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Atoll -->
                            <div>
                                <label for="atoll" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Atoll
                                </label>
                                <input type="text" name="atoll" id="atoll" value="{{ old('atoll') }}"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('atoll') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter atoll">
                                @error('atoll')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Island -->
                            <div>
                                <label for="island" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Island
                                </label>
                                <input type="text" name="island" id="island" value="{{ old('island') }}"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('island') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter island">
                                @error('island')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Site -->
                            <div>
                                <label for="site" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Site
                                </label>
                                <input type="text" name="site" id="site" value="{{ old('site') }}"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('site') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter site">
                                @error('site')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="space-y-4">
                            <h4 class="font-heading font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Additional Information</h4>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-heading font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Notes
                                </label>
                                <textarea name="notes" id="notes" rows="4"
                                    class="w-full rounded-2xl border border-gray-300 dark:border-gray-600/50 px-4 py-3 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-300 hover:bg-white dark:bg-gray-800 focus:bg-white dark:bg-gray-800 @error('notes') border-red-300 dark:border-red-700 @enderror"
                                    placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('contacts.index') }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 shadow-sm transition-all duration-300 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-blue-700 hover:to-blue-800 transform">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Contact
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
