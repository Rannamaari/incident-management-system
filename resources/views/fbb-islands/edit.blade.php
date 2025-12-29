@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-heading text-2xl lg:text-3xl font-bold bg-gradient-to-r from-purple-900 via-purple-800 to-purple-900 bg-clip-text text-transparent">
                Edit FBB Island
            </h2>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">{{ $fbbIsland->region->code }} - {{ $fbbIsland->island_name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('fbb-islands.show', $fbbIsland) }}" class="inline-flex items-center gap-2 rounded-2xl bg-gray-700 px-5 py-2.5 text-white hover:bg-gray-800 transition-colors">
                Cancel
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('fbb-islands.update', $fbbIsland) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- FBB Island Information (Read-only) -->
                <div class="overflow-hidden rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-6">FBB Island Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Region</label>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $fbbIsland->region->name }} ({{ $fbbIsland->region->code }})</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Island Name</label>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $fbbIsland->island_name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Editable Settings -->
                <div class="overflow-hidden rounded-3xl border bg-white dark:bg-gray-800 shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-6">Settings</h3>

                    <div class="space-y-4">
                        <!-- Technology -->
                        <div class="p-4 rounded-xl bg-gray-50">
                            <label for="technology" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                                Technology <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="technology"
                                   id="technology"
                                   value="{{ old('technology', $fbbIsland->technology) }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:focus:ring-purple-400"
                                   placeholder="e.g., FTTH, FTTx, IPOE, FTTx-IPOE">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Specify the FBB technology used</p>
                            @error('technology')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-gray-50">
                            <input type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ $fbbIsland->is_active ? 'checked' : '' }}
                                   class="h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500 dark:focus:ring-green-400">
                            <label for="is_active" class="flex-1 cursor-pointer">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Active Island</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">FBB service is currently active on this island</span>
                            </label>
                        </div>

                        <!-- Remarks -->
                        <div class="p-4 rounded-xl bg-gray-50">
                            <label for="remarks" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                                Remarks
                            </label>
                            <textarea name="remarks"
                                      id="remarks"
                                      rows="4"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:focus:ring-purple-400"
                                      placeholder="Additional notes or remarks about this FBB island">{{ old('remarks', $fbbIsland->remarks) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Any additional information about this FBB island</p>
                            @error('remarks')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('fbb-islands.show', $fbbIsland) }}"
                       class="inline-flex items-center gap-2 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-3 font-semibold text-white hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>

            <!-- Delete Section -->
            @if(Auth::user()->canManageSites())
                <div class="mt-6 overflow-hidden rounded-3xl border border-red-200 bg-red-50 shadow-xl p-8">
                    <h3 class="text-lg font-heading font-semibold text-red-900 mb-4">Danger Zone</h3>
                    <p class="text-sm text-red-700 mb-4">Once you delete this FBB island, there is no going back. Please be certain.</p>
                    <form method="POST" action="{{ route('fbb-islands.destroy', $fbbIsland) }}" onsubmit="return confirm('Are you sure you want to delete this FBB island? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 text-base font-semibold text-white hover:from-red-700 hover:to-red-800 transition-all shadow-lg touch-manipulation">
                            Delete FBB Island
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
