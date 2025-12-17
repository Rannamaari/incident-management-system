@extends('layouts.app')

@section('header')
    <!-- Hero -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-indigo-50/50 px-4 sm:px-6 lg:px-8 py-8 border-b border-gray-200/30">
        <div class="mx-auto max-w-7xl">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg grid place-items-center transform hover:scale-105 transition-all duration-300">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-heading text-3xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">Profile Settings</h1>
                    <p class="mt-2 text-lg text-gray-600 font-medium">View and manage your account information</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 lg:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Success Message -->
            @if(session('status') === 'profile-updated')
                <div class="mb-6 rounded-2xl bg-gradient-to-r from-green-50 to-green-100 border border-green-200 p-4">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-heading font-medium text-green-800">Profile updated successfully!</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="rounded-3xl border border-gray-100/50 bg-white/90 backdrop-blur-sm p-6 shadow-lg">
                        <div class="text-center">
                            <!-- Avatar -->
                            <div class="mx-auto h-24 w-24 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg mb-4">
                                <span class="text-3xl font-heading font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                            </div>

                            <!-- User Info -->
                            <h2 class="text-2xl font-heading font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                            <p class="text-sm text-gray-600 mb-3">{{ $user->email }}</p>

                            <!-- Role Badge -->
                            <div class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-heading font-medium
                                @if($user->isAdmin())
                                    bg-red-100 text-red-800
                                @elseif($user->isEditor())
                                    bg-blue-100 text-blue-800
                                @else
                                    bg-green-100 text-green-800
                                @endif">
                                <svg class="mr-1.5 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                {{ $user->getRoleDisplayName() }}
                            </div>

                            <!-- Stats -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="text-sm text-gray-600">
                                    <p class="mb-2"><span class="font-heading font-semibold">Member since:</span> {{ $user->created_at->format('M d, Y') }}</p>
                                    <p><span class="font-heading font-semibold">Last updated:</span> {{ $user->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="lg:col-span-2">
                    <div class="rounded-3xl border border-gray-100/50 bg-white/90 backdrop-blur-sm p-6 lg:p-8 shadow-lg">
                        <h3 class="text-xl font-heading font-bold text-gray-900 mb-6">Account Information</h3>

                        <div class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-heading font-semibold text-gray-700 mb-2">Full Name</label>
                                <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-gray-900">
                                    {{ $user->name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-heading font-semibold text-gray-700 mb-2">Email Address</label>
                                <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-gray-900">
                                    {{ $user->email }}
                                </div>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-heading font-semibold text-gray-700 mb-2">Role</label>
                                <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-gray-900">
                                    {{ $user->getRoleDisplayName() }}
                                </div>
                            </div>

                            <!-- Permissions -->
                            <div>
                                <label class="block text-sm font-heading font-semibold text-gray-700 mb-3">Permissions</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- View Incidents -->
                                    <div class="flex items-center gap-2 rounded-xl bg-green-50 border border-green-200 px-4 py-2.5">
                                        <svg class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm font-heading font-medium text-green-800">View Incidents</span>
                                    </div>

                                    <!-- Edit Incidents -->
                                    @if($user->canEditIncidents())
                                        <div class="flex items-center gap-2 rounded-xl bg-green-50 border border-green-200 px-4 py-2.5">
                                            <svg class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm font-heading font-medium text-green-800">Edit Incidents</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 rounded-xl bg-gray-50 border border-gray-200 px-4 py-2.5">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="text-sm font-heading font-medium text-gray-500">Edit Incidents</span>
                                        </div>
                                    @endif

                                    <!-- Delete Incidents -->
                                    @if($user->canDeleteIncidents())
                                        <div class="flex items-center gap-2 rounded-xl bg-green-50 border border-green-200 px-4 py-2.5">
                                            <svg class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm font-heading font-medium text-green-800">Delete Incidents</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 rounded-xl bg-gray-50 border border-gray-200 px-4 py-2.5">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="text-sm font-heading font-medium text-gray-500">Delete Incidents</span>
                                        </div>
                                    @endif

                                    <!-- Manage Users -->
                                    @if($user->canManageUsers())
                                        <div class="flex items-center gap-2 rounded-xl bg-green-50 border border-green-200 px-4 py-2.5">
                                            <svg class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm font-heading font-medium text-green-800">Manage Users</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 rounded-xl bg-gray-50 border border-gray-200 px-4 py-2.5">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="text-sm font-heading font-medium text-gray-500">Manage Users</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Info Note -->
                            <div class="rounded-xl bg-blue-50 border border-blue-200 p-4">
                                <div class="flex gap-3">
                                    <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-heading font-semibold text-blue-900 mb-1">Account Information</p>
                                        <p class="text-sm text-blue-800">Your profile information is managed by the system administrator. If you need to update your details or change your role, please contact an administrator.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
