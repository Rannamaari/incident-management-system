@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Create New User
            </h2>
            <p class="mt-2 text-lg text-gray-600 font-medium">Add a new user to the system</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-2.5 text-white shadow-lg hover:from-gray-800 hover:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                <!-- Section Header -->
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-heading font-semibold text-gray-900">User Information</h3>
                            <p class="text-sm text-gray-600">Fill in the fields below to create a new user</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('name') border-red-300 @enderror"
                                placeholder="Enter user's full name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('email') border-red-300 @enderror"
                                placeholder="user@example.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role" id="role" required
                                class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('role') border-red-300 @enderror">
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin - Full system access</option>
                                <option value="editor" {{ old('role') === 'editor' ? 'selected' : '' }}>Editor - Create and edit incidents</option>
                                <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>Viewer - Read-only access</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">
                                <strong>Admin:</strong> Full system access including user management<br>
                                <strong>Editor:</strong> Can create, edit, and export incidents<br>
                                <strong>Viewer:</strong> Read-only access to view incidents
                            </p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('password') border-red-300 @enderror"
                                placeholder="Minimum 8 characters">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white"
                                placeholder="Re-enter password">
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('users.index') }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-blue-700 hover:to-blue-800 transform">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


