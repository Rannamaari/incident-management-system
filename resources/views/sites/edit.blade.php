@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-heading text-2xl lg:text-3xl font-bold">Edit Site</h2>
        <a href="{{ route('sites.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white">Back to List</a>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border bg-white shadow-xl p-8">
                <form method="POST" action="{{ route('sites.update', $site) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Site ID <span class="text-red-500">*</span></label>
                            <input type="text" name="site_id" value="{{ old('site_id', $site->site_id) }}" required class="w-full rounded-2xl border px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Atoll Code <span class="text-red-500">*</span></label>
                            <input type="text" name="atoll_code" value="{{ old('atoll_code', $site->atoll_code) }}" required class="w-full rounded-2xl border px-4 py-3">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Site Name <span class="text-red-500">*</span></label>
                            <input type="text" name="site_name" value="{{ old('site_name', $site->site_name) }}" required class="w-full rounded-2xl border px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coverage <span class="text-red-500">*</span></label>
                            <input type="text" name="coverage" value="{{ old('coverage', $site->coverage) }}" required class="w-full rounded-2xl border px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Operational Date <span class="text-red-500">*</span></label>
                            <input type="date" name="operational_date" value="{{ old('operational_date', $site->operational_date->format('Y-m-d')) }}" required class="w-full rounded-2xl border px-4 py-3">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transmission / Backhaul <span class="text-red-500">*</span></label>
                            <input type="text" name="transmission_or_backhaul" value="{{ old('transmission_or_backhaul', $site->transmission_or_backhaul) }}" required class="w-full rounded-2xl border px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" required class="w-full rounded-2xl border px-4 py-3">
                                <option value="Active" {{ old('status', $site->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Monitoring" {{ old('status', $site->status) == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                                <option value="Maintenance" {{ old('status', $site->status) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="Inactive" {{ old('status', $site->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Review Date</label>
                            <input type="date" name="review_date" value="{{ old('review_date', $site->review_date?->format('Y-m-d')) }}" class="w-full rounded-2xl border px-4 py-3">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                            <textarea name="remarks" rows="4" class="w-full rounded-2xl border px-4 py-3">{{ old('remarks', $site->remarks) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t">
                        <a href="{{ route('sites.index') }}" class="rounded-xl border px-6 py-3 font-semibold">Cancel</a>
                        <button type="submit" class="rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-semibold text-white">Update Site</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
