@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-heading text-2xl lg:text-3xl font-bold bg-gradient-to-r from-purple-900 via-purple-800 to-purple-900 bg-clip-text text-transparent">
            Create New FBB Island
        </h2>
        <a href="{{ route('fbb-islands.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
        </a>
    </div>
@endsection

@section('content')
    <div class="py-4 sm:py-6 lg:py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl sm:rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-4 sm:p-6 lg:p-8">
                <form method="POST" action="{{ route('fbb-islands.store') }}" class="space-y-6 sm:space-y-8">
                    @csrf

                    <!-- Region & Island Name -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Region <span class="text-red-500">*</span>
                            </label>
                            <select name="region_id"
                                    required
                                    class="w-full rounded-xl sm:rounded-2xl border px-4 py-3 sm:py-3.5 text-base @error('region_id') border-red-300 dark:border-red-700 @else border-gray-300 dark:border-gray-600 @enderror touch-manipulation">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->code }} - {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('region_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Island Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="island_name"
                                   value="{{ old('island_name') }}"
                                   required
                                   class="w-full rounded-xl sm:rounded-2xl border px-4 py-3 sm:py-3.5 text-base @error('island_name') border-red-300 dark:border-red-700 @else border-gray-300 dark:border-gray-600 @enderror touch-manipulation"
                                   placeholder="e.g., Hulhumale, Vilufushi">
                            @error('island_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter the island name where FBB service is available</p>
                        </div>
                    </div>

                    <!-- Technology -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Technology <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="technology"
                               value="{{ old('technology') }}"
                               required
                               class="w-full rounded-xl sm:rounded-2xl border px-4 py-3 sm:py-3.5 text-base @error('technology') border-red-300 dark:border-red-700 @else border-gray-300 dark:border-gray-600 @enderror touch-manipulation"
                               placeholder="e.g., FTTH, FTTx, IPOE, FTTx-IPOE">
                        @error('technology')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Specify the FBB technology used (FTTH, FTTx, IPOE, etc.)</p>
                    </div>

                    <!-- Active Status -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Status</h3>
                        <label class="flex items-start gap-3 p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-green-300 dark:border-green-700 hover:bg-green-50 cursor-pointer transition-colors active:scale-[0.98] touch-manipulation">
                            <input type="checkbox"
                                   name="is_active"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="mt-1 h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500 dark:focus:ring-green-400 touch-manipulation">
                            <div class="flex-1">
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Active Island</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">FBB service is currently active on this island</p>
                            </div>
                        </label>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Remarks <span class="text-gray-500 dark:text-gray-400">(Optional)</span>
                        </label>
                        <textarea name="remarks"
                                  rows="4"
                                  class="w-full rounded-xl sm:rounded-2xl border border-gray-300 dark:border-gray-600 px-4 py-3 sm:py-3.5 text-base @error('remarks') border-red-300 dark:border-red-700 @enderror touch-manipulation"
                                  placeholder="Add any additional notes or remarks about this FBB island...">{{ old('remarks') }}</textarea>
                        @error('remarks')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-6 border-t">
                        <a href="{{ route('fbb-islands.index') }}"
                           class="order-2 sm:order-1 text-center rounded-xl border-2 border-gray-300 dark:border-gray-600 px-6 py-3.5 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors touch-manipulation">
                            Cancel
                        </a>
                        <button type="submit"
                                class="order-1 sm:order-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-3.5 text-base font-semibold text-white hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg hover:shadow-xl touch-manipulation">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create FBB Island
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
