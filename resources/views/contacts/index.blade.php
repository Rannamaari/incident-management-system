@extends('layouts.app')

@section('header')
    <!-- Hero -->
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-blue-50/50 dark:from-slate-900 dark:via-slate-800 dark:to-gray-900 px-4 sm:px-6 lg:px-8 py-4 sm:py-8 border-b border-gray-200 dark:border-white/10 dark:shadow-lg dark:shadow-black/40">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:gap-6">
                <!-- Title and Icon -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg grid place-items-center">
                        <svg class="h-5 w-5 sm:h-7 sm:w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-100 dark:to-white bg-clip-text text-transparent">Phone Book</h1>
                        <p class="mt-1 text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-400 font-medium hidden sm:block">Quick search for contacts and phone numbers</p>
                    </div>
                </div>

                <!-- Search Box and New Contact Button -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <div class="flex-1">
                        <form method="GET" action="{{ route('contacts.index') }}">
                            <div class="relative">
                                <input type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search name, phone, company..."
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 text-sm font-medium bg-white dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400/20 transition-all duration-200">
                                <svg class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </form>
                    </div>

                    @if(Auth::user()->canManageContacts())
                        <a href="{{ route('contacts.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-2.5 sm:py-3 font-heading font-semibold text-sm sm:text-base text-white shadow-lg transition-all duration-300 hover:shadow-xl whitespace-nowrap">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>New Contact</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6 lg:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Filters -->
            <div class="mb-4 sm:mb-6">
                <form method="GET" action="{{ route('contacts.index') }}" class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 sm:gap-3">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <select name="category" onchange="this.form.submit()" class="rounded-lg sm:rounded-xl border border-gray-300 dark:border-gray-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium bg-white dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400/20 transition-all duration-200">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <select name="atoll" onchange="this.form.submit()" class="rounded-lg sm:rounded-xl border border-gray-300 dark:border-gray-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium bg-white dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400/20 transition-all duration-200">
                        <option value="">All Atolls</option>
                        @foreach($atolls as $atl)
                            <option value="{{ $atl }}" {{ request('atoll') == $atl ? 'selected' : '' }}>{{ $atl }}</option>
                        @endforeach
                    </select>

                    <select name="per_page" onchange="this.form.submit()" class="rounded-lg sm:rounded-xl border border-gray-300 dark:border-gray-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium bg-white dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400/20 transition-all duration-200 col-span-2 sm:col-span-1">
                        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>

                    @if(request()->hasAny(['search', 'category', 'atoll']))
                        <a href="{{ route('contacts.index') }}" class="col-span-2 sm:col-span-1 rounded-lg sm:rounded-xl bg-gradient-to-r from-gray-100 to-gray-200 px-4 sm:px-5 py-2 font-heading font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 transition-all duration-300 hover:from-gray-200 hover:to-gray-300 text-center">
                            Clear All
                        </a>
                    @endif
                </form>

                <div class="mt-2 sm:mt-3 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-medium">{{ $contacts->firstItem() ?? 0 }}</span>–<span class="font-medium">{{ $contacts->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $contacts->total() }}</span> contacts
                </div>
            </div>

            @if(Auth::user()->canManageContacts())
                <form id="bulkDeleteForm" method="POST" action="{{ route('contacts.bulk-delete') }}" class="mb-3 sm:mb-4">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                            <span id="selectedCount">0</span> selected
                        </div>
                        <button type="submit" id="bulkDeleteBtn"
                            class="hidden inline-flex items-center gap-1 sm:gap-2 rounded-lg sm:rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-3 sm:px-4 py-1.5 sm:py-2 font-heading font-semibold text-xs sm:text-sm text-white shadow-md transition-all duration-300 hover:from-red-700 hover:to-red-800"
                            onclick="return confirm('Are you sure you want to delete the selected contacts? This action cannot be undone.');">
                            <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="hidden sm:inline">Delete Selected</span>
                            <span class="sm:hidden">Delete</span>
                        </button>
                    </div>
                </form>
            @endif

            <!-- Contacts Grid (Mobile Cards / Desktop Table) -->
            <div class="overflow-hidden rounded-3xl border border-gray-100 dark:border-white/10 bg-white/80 dark:bg-slate-900 backdrop-blur-sm shadow-lg dark:shadow-black/40">

                <!-- Desktop Table -->
                <div class="hidden lg:block">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-gradient-to-r from-gray-50 to-gray-100/80 dark:from-slate-800 dark:to-slate-900/80 backdrop-blur-sm text-xs uppercase tracking-wide text-gray-700 dark:text-gray-300 font-semibold">
                            <tr>
                                @if(Auth::user()->canManageContacts())
                                    <th class="font-heading px-4 py-3 text-left w-12">
                                        <input type="checkbox" id="selectAll"
                                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400">
                                    </th>
                                @endif
                                <th class="font-heading px-4 py-3 text-left">Contact</th>
                                <th class="font-heading px-4 py-3 text-left">Phone</th>
                                <th class="font-heading px-4 py-3 text-left">Company</th>
                                <th class="font-heading px-4 py-3 text-left">Location</th>
                                <th class="font-heading px-4 py-3 text-left">Category</th>
                                @if(Auth::user()->canManageContacts())
                                    <th class="font-heading px-4 py-3 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($contacts as $contact)
                                <tr class="hover:bg-gradient-to-r hover:from-gray-50/50 hover:to-blue-50/30 dark:hover:bg-white/5 transition-all duration-200">
                                    @if(Auth::user()->canManageContacts())
                                        <td class="px-4 py-4">
                                            <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}"
                                                class="contact-checkbox h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400"
                                                form="bulkDeleteForm">
                                        </td>
                                    @endif
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 grid place-items-center flex-shrink-0">
                                                <span class="text-sm font-heading font-bold text-blue-700 dark:text-blue-400">{{ $contact->initials }}</span>
                                            </div>
                                            <div>
                                                <div class="font-heading font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $contact->name }}
                                                    @if($contact->island)
                                                        <span class="text-sm font-normal text-gray-600 dark:text-gray-400">({{ $contact->island }})</span>
                                                    @elseif($contact->site)
                                                        <span class="text-sm font-normal text-gray-600 dark:text-gray-400">({{ $contact->site }})</span>
                                                    @endif
                                                </div>
                                                @if($contact->email)
                                                    <a href="mailto:{{ $contact->email }}" class="text-xs text-blue-600 hover:underline">{{ $contact->email }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="tel:{{ $contact->phone }}" class="font-heading font-medium text-blue-600 hover:underline">{{ $contact->phone }}</a>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $contact->company }}</div>
                                        @if($contact->role)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $contact->role }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($contact->island)
                                            <div class="text-gray-900 dark:text-gray-100">{{ $contact->island }}</div>
                                        @endif
                                        @if($contact->atoll)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $contact->atoll }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($contact->category)
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-heading font-medium bg-blue-100 text-blue-800">
                                                {{ $contact->category }}
                                            </span>
                                        @endif
                                    </td>
                                    @if(Auth::user()->canManageContacts())
                                        <td class="px-4 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('contacts.edit', $contact) }}"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:text-blue-400 hover:bg-blue-100 transition-colors duration-200">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this contact? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition-colors duration-200">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->canManageContacts() ? '7' : '5' }}" class="px-4 py-12 text-center">
                                        <div class="mx-auto max-w-sm">
                                            <svg class="mx-auto mb-4 h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="text-lg font-heading font-medium text-gray-600 dark:text-gray-400">No contacts found</p>
                                            <p class="mt-1 text-sm text-gray-400">Try adjusting your search or filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden divide-y divide-gray-100">
                    @forelse($contacts as $contact)
                        <div class="p-3 hover:bg-blue-50/30 dark:hover:bg-white/5 transition-colors duration-200">
                            <div class="flex items-start gap-2.5">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 grid place-items-center flex-shrink-0">
                                    <span class="text-xs font-heading font-bold text-blue-700 dark:text-blue-400">{{ $contact->initials }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-heading font-semibold text-sm text-gray-900 dark:text-gray-100 mb-0.5 leading-tight">
                                        {{ $contact->name }}
                                        @if($contact->island)
                                            <span class="text-xs font-normal text-gray-600 dark:text-gray-400 block sm:inline sm:ml-1">({{ $contact->island }})</span>
                                        @elseif($contact->site)
                                            <span class="text-xs font-normal text-gray-600 dark:text-gray-400 block sm:inline sm:ml-1">({{ $contact->site }})</span>
                                        @endif
                                    </div>
                                    <a href="tel:{{ $contact->phone }}" class="text-blue-600 text-sm font-medium hover:underline mb-1.5 block">{{ $contact->phone }}</a>

                                    @if($contact->company)
                                        <div class="text-xs text-gray-700 dark:text-gray-300 mb-1 truncate">{{ $contact->company }}</div>
                                    @endif

                                    @if($contact->category)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-heading font-medium bg-blue-100 text-blue-800">
                                            {{ $contact->category }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if(Auth::user()->canManageContacts())
                                <div class="mt-2.5 flex items-center gap-1.5">
                                    <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}"
                                        class="contact-checkbox h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400"
                                        form="bulkDeleteForm">
                                    <a href="{{ route('contacts.edit', $contact) }}"
                                        class="flex-1 inline-flex items-center justify-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 dark:text-blue-400 hover:bg-blue-100 transition-colors duration-200 active:scale-95">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="flex-1"
                                        onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-1 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition-colors duration-200 active:scale-95">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="mx-auto mb-4 h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-lg font-heading font-medium text-gray-600 dark:text-gray-400">No contacts found</p>
                            <p class="mt-1 text-sm text-gray-400">Try adjusting your search or filters</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $contacts->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>

    @if(Auth::user()->canManageContacts())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
                const selectedCountSpan = document.getElementById('selectedCount');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                // Select/deselect all checkboxes
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        contactCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateSelectedCount();
                    });
                }

                // Update count when individual checkboxes change
                contactCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateSelectedCount();

                        // Update "select all" checkbox state
                        if (selectAllCheckbox) {
                            const allChecked = Array.from(contactCheckboxes).every(cb => cb.checked);
                            const noneChecked = Array.from(contactCheckboxes).every(cb => !cb.checked);
                            selectAllCheckbox.checked = allChecked;
                            selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
                        }
                    });
                });

                // Update selected count and button visibility
                function updateSelectedCount() {
                    const checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
                    selectedCountSpan.textContent = checkedCount;

                    if (checkedCount > 0) {
                        bulkDeleteBtn.classList.remove('hidden');
                        bulkDeleteBtn.classList.add('inline-flex');
                    } else {
                        bulkDeleteBtn.classList.add('hidden');
                        bulkDeleteBtn.classList.remove('inline-flex');
                    }
                }

                // Initial count update
                updateSelectedCount();
            });
        </script>
    @endif
@endsection
