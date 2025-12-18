@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-heading text-2xl lg:text-3xl font-bold">Create Site</h2>
        <a href="{{ route('sites.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white">
            Back to List
        </a>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                <form method="POST" action="{{ route('sites.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Site ID <span class="text-red-500">*</span></label>
                            <input type="text" name="site_id" value="{{ old('site_id') }}" required
                                class="w-full rounded-2xl border px-4 py-3 @error('site_id') border-red-300 @enderror" placeholder="e.g., SITE001">
                            @error('site_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Atoll Code <span class="text-red-500">*</span></label>
                            <input type="text" name="atoll_code" value="{{ old('atoll_code') }}" required
                                class="w-full rounded-2xl border px-4 py-3 @error('atoll_code') border-red-300 @enderror">
                            @error('atoll_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Site Name <span class="text-red-500">*</span></label>
                            <input type="text" name="site_name" value="{{ old('site_name') }}" required
                                class="w-full rounded-2xl border px-4 py-3 @error('site_name') border-red-300 @enderror">
                            @error('site_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coverage <span class="text-red-500">*</span></label>
                            <input type="text" name="coverage" value="{{ old('coverage') }}" required
                                class="w-full rounded-2xl border px-4 py-3 @error('coverage') border-red-300 @enderror" placeholder="e.g., 2G/3G/4G">
                            @error('coverage')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Operational Date <span class="text-red-500">*</span></label>
                            <input type="date" name="operational_date" value="{{ old('operational_date') }}" required
                                class="w-full rounded-2xl border px-4 py-3 @error('operational_date') border-red-300 @enderror">
                            @error('operational_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transmission / Backhaul <span class="text-red-500">*</span></label>
                            <input type="text" name="transmission_or_backhaul" value="{{ old('transmission_or_backhaul') }}" required
                                class="w-full rounded-2xl border px-4 py-3 @error('transmission_or_backhaul') border-red-300 @enderror">
                            @error('transmission_or_backhaul')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" required class="w-full rounded-2xl border px-4 py-3 @error('status') border-red-300 @enderror">
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Monitoring" {{ old('status') == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                                <option value="Maintenance" {{ old('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Review Date</label>
                            <input type="date" name="review_date" value="{{ old('review_date') }}"
                                class="w-full rounded-2xl border px-4 py-3 @error('review_date') border-red-300 @enderror">
                            @error('review_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                            <textarea name="remarks" rows="4" class="w-full rounded-2xl border px-4 py-3 @error('remarks') border-red-300 @enderror">{{ old('remarks') }}</textarea>
                            @error('remarks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t">
                        <a href="{{ route('sites.index') }}" class="rounded-xl border px-6 py-3 font-semibold">Cancel</a>
                        <button type="submit" class="rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-semibold text-white">
                            Create Site
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
