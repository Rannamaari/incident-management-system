@extends('layouts.app')

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                Edit Temporary Site
            </h2>
            <p class="mt-2 text-lg text-gray-600 font-medium">Update temporary site information</p>
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
            <div class="overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-xl">
                <!-- Section Header -->
                <div class="border-b border-gray-200/50 bg-gradient-to-r from-slate-50/80 to-white/60 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 shadow-md">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-semibold text-gray-900">Site Information</h3>
                            <p class="text-sm text-gray-600">Update the fields below to edit the temporary site</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('temporary-sites.update', $temporarySite) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information Section -->
                        <div class="space-y-4">
                            <h4 class="font-heading font-semibold text-gray-900 border-b pb-2">Basic Information</h4>

                            <!-- Temp Site ID -->
                            <div>
                                <label for="temp_site_id" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Temp Site ID <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="temp_site_id" id="temp_site_id" value="{{ old('temp_site_id', $temporarySite->temp_site_id) }}" required
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('temp_site_id') border-red-300 @enderror"
                                    placeholder="e.g., TS001">
                                @error('temp_site_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Atoll Code -->
                            <div>
                                <label for="atoll_code" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Atoll Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="atoll_code" id="atoll_code" value="{{ old('atoll_code', $temporarySite->atoll_code) }}" required
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('atoll_code') border-red-300 @enderror"
                                    placeholder="e.g., AA, ADh, B, Dh">
                                @error('atoll_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Site Name -->
                            <div>
                                <label for="site_name" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Site Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $temporarySite->site_name) }}" required
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('site_name') border-red-300 @enderror"
                                    placeholder="e.g., AA_Veligandu_Resort">
                                @error('site_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Coverage -->
                            <div>
                                <label for="coverage" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Coverage <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="coverage" id="coverage" value="{{ old('coverage', $temporarySite->coverage) }}" required
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('coverage') border-red-300 @enderror"
                                    placeholder="e.g., 2G/3G/4G, 3G/4G">
                                @error('coverage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Added Date -->
                            <div>
                                <label for="added_date" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Added Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="added_date" id="added_date" value="{{ old('added_date', $temporarySite->added_date->format('Y-m-d')) }}" required
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('added_date') border-red-300 @enderror">
                                @error('added_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Network Information Section -->
                        <div class="space-y-4">
                            <h4 class="font-heading font-semibold text-gray-900 border-b pb-2">Network Information</h4>

                            <!-- Transmission / Backhaul -->
                            <div>
                                <label for="transmission_or_backhaul" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Transmission / Backhaul <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="transmission_or_backhaul" id="transmission_or_backhaul" value="{{ old('transmission_or_backhaul', $temporarySite->transmission_or_backhaul) }}" required
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('transmission_or_backhaul') border-red-300 @enderror"
                                    placeholder="e.g., Rasdhoo – Veligandu link">
                                @error('transmission_or_backhaul')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status and Review Section -->
                        <div class="space-y-4">
                            <h4 class="font-heading font-semibold text-gray-900 border-b pb-2">Status & Review</h4>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('status') border-red-300 @enderror">
                                    <option value="Temporary" {{ old('status', $temporarySite->status) == 'Temporary' ? 'selected' : '' }}>Temporary</option>
                                    <option value="Resolved" {{ old('status', $temporarySite->status) == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="Remove from list" {{ old('status', $temporarySite->status) == 'Remove from list' ? 'selected' : '' }}>Remove from list</option>
                                    <option value="Monitoring" {{ old('status', $temporarySite->status) == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Review Date -->
                            <div>
                                <label for="review_date" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Review Date (Optional)
                                </label>
                                <input type="date" name="review_date" id="review_date" value="{{ old('review_date', $temporarySite->review_date?->format('Y-m-d')) }}"
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('review_date') border-red-300 @enderror">
                                @error('review_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="space-y-4">
                            <h4 class="font-heading font-semibold text-gray-900 border-b pb-2">Additional Information</h4>

                            <!-- Remarks -->
                            <div>
                                <label for="remarks" class="block text-sm font-heading font-medium text-gray-700 mb-2">
                                    Remarks
                                </label>
                                <textarea name="remarks" id="remarks" rows="4"
                                    class="w-full rounded-2xl border border-gray-300/50 px-4 py-3 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-white focus:bg-white @error('remarks') border-red-300 @enderror"
                                    placeholder="Enter any additional notes (e.g., under construction, power issues, renovation status)">{{ old('remarks', $temporarySite->remarks) }}</textarea>
                                @error('remarks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('temporary-sites.index') }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-heading font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:from-blue-700 hover:to-blue-800 transform">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Site
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
