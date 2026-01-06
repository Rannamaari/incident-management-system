@extends('layouts.app')

@section('header')
    <div class="-mx-4 sm:-mx-6 lg:-mx-8 bg-gradient-to-br from-slate-50 via-white to-indigo-50/50 dark:from-slate-900 dark:via-slate-800 dark:to-gray-900 px-3 sm:px-4 lg:px-8 py-4 sm:py-6 lg:py-8 border-b border-gray-200 dark:border-white/10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-3 sm:gap-4 lg:gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg grid place-items-center">
                            <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-heading text-xl sm:text-2xl lg:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-100 dark:to-white bg-clip-text text-transparent">Notification Settings</h1>
                            <p class="mt-1 sm:mt-2 text-xs sm:text-sm lg:text-lg text-gray-600 dark:text-gray-400 font-medium">Manage email notification levels and recipients</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="openAddLevelModal()" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Level
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- System Status -->
            <div class="mb-6 bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">System Status</h3>
                    <div class="mt-3 flex items-center gap-4">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">Email Notifications:</span>
                            @if(config('incident-notifications.enabled'))
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 fill-green-500" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 fill-red-500" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                    Disabled
                                </span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500">Configure in .env: INCIDENT_NOTIFICATIONS_ENABLED</span>
                    </div>
                </div>
            </div>

            <!-- Auto-Send Settings (Admin Only) -->
            @if(auth()->user()->isAdmin())
            <div class="mb-6 overflow-hidden rounded-3xl border border-gray-100/50 bg-white/80 backdrop-blur-sm shadow-lg">
                <div class="px-6 py-5 border-b border-gray-200/50">
                    <div class="flex items-center gap-3">
                        <div class="grid h-8 w-8 place-items-center rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200">
                            <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-gray-900">Auto-Send Settings</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="text-base font-semibold text-gray-900">Automatic Email Notifications</h4>
                            <p class="mt-1 text-sm text-gray-600">
                                When enabled, new incidents will automatically send email notifications after a 5-minute delay.
                                Creators can cancel the notification during this window if they made a mistake.
                            </p>
                        </div>
                        <form action="{{ route('notification-settings.auto-send.update') }}" method="POST" class="ml-4">
                            @csrf
                            <input type="hidden" name="enabled" value="{{ \App\Models\NotificationSetting::isAutoSendEnabled() ? 0 : 1 }}">
                            <button type="submit"
                                class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition-all duration-300
                                {{ \App\Models\NotificationSetting::isAutoSendEnabled()
                                    ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                {{ \App\Models\NotificationSetting::isAutoSendEnabled() ? 'Enabled' : 'Disabled' }}
                            </button>
                        </form>
                    </div>

                    @if(\App\Models\NotificationSetting::isAutoSendEnabled())
                    <div class="mt-4 rounded-xl bg-blue-50 border border-blue-200 p-4">
                        <div class="flex gap-3">
                            <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm text-blue-700">
                                <strong>Important:</strong> Email notifications will be sent 5 minutes after incident creation.
                                Manual notifications (bell icon) are sent immediately. Ensure your queue worker is running.
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Notification Levels -->
            <div class="space-y-6">
                @forelse($levels as $level)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- Level Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $level->name }}
                                        </h3>
                                        @if($level->is_active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">Inactive</span>
                                        @endif
                                    </div>
                                    @if($level->description)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $level->description }}</p>
                                    @endif
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Severities:</span>
                                        @foreach($level->severities as $severity)
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                                {{ $severity === 'High' ? 'bg-red-50 text-red-700 ring-red-600/20' : '' }}
                                                {{ $severity === 'Medium' ? 'bg-yellow-50 text-yellow-800 ring-yellow-600/20' : '' }}
                                                {{ $severity === 'Low' ? 'bg-green-50 text-green-700 ring-green-600/20' : '' }}">
                                                {{ $severity }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <form action="{{ route('notification-settings.levels.toggle', $level) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                            {{ $level->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <button onclick='editLevel(@json($level))' class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                        Edit
                                    </button>
                                    <form action="{{ route('notification-settings.levels.destroy', $level) }}" method="POST" class="inline" onsubmit="return confirm('Delete this level and all its recipients?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Recipients -->
                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        Recipients <span class="text-gray-500">({{ $level->recipients->count() }})</span>
                                    </h4>
                                    <button onclick="openAddRecipientModal({{ $level->id }})" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                        + Add Recipient
                                    </button>
                                </div>

                                @if($level->recipients->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                            <thead>
                                                <tr>
                                                    <th class="py-2 pl-0 pr-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Email</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Name</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Department</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                                    <th class="py-2 pl-3 pr-0 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($level->recipients as $recipient)
                                                    <tr>
                                                        <td class="whitespace-nowrap py-3 pl-0 pr-3 text-sm text-gray-900 dark:text-gray-100">{{ $recipient->email }}</td>
                                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $recipient->name ?? '-' }}</td>
                                                        <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $recipient->department ?? '-' }}</td>
                                                        <td class="whitespace-nowrap px-3 py-3 text-sm">
                                                            @if($recipient->is_active)
                                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">Active</span>
                                                            @else
                                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td class="whitespace-nowrap py-3 pl-3 pr-0 text-right text-sm">
                                                            <form action="{{ route('notification-settings.recipients.toggle', $recipient) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                                    {{ $recipient->is_active ? 'Deactivate' : 'Activate' }}
                                                                </button>
                                                            </form>
                                                            <button onclick='editRecipient(@json($recipient))' class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                                            <form action="{{ route('notification-settings.recipients.destroy', $recipient) }}" method="POST" class="inline" onsubmit="return confirm('Remove this recipient?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">No recipients configured.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No notification levels</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a notification level.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Add/Edit Level Modal -->
    <div id="levelModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white mb-4" id="levelModalTitle">Add Level</h3>
                    <form id="levelForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="levelMethod" value="POST">

                        <div class="space-y-4">
                            <div>
                                <label for="level_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                                <input type="text" name="name" id="level_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label for="level_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea name="description" id="level_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Severities *</label>
                                <div class="space-y-2">
                                    <label class="inline-flex items-center mr-4">
                                        <input type="checkbox" name="severities[]" value="Low" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Low</span>
                                    </label>
                                    <label class="inline-flex items-center mr-4">
                                        <input type="checkbox" name="severities[]" value="Medium" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Medium</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="severities[]" value="High" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">High</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="level_sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                                <input type="number" name="sort_order" id="level_sort_order" min="0" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2">
                                Save
                            </button>
                            <button type="button" onclick="closeLevelModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Recipient Modal -->
    <div id="recipientModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white mb-4" id="recipientModalTitle">Add Recipient</h3>
                    <form id="recipientForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="recipientMethod" value="POST">

                        <div class="space-y-4">
                            <div>
                                <label for="recipient_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                                <input type="email" name="email" id="recipient_email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label for="recipient_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" name="name" id="recipient_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label for="recipient_department" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                                <input type="text" name="department" id="recipient_department" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2">
                                Save
                            </button>
                            <button type="button" onclick="closeRecipientModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAddLevelModal() {
            document.getElementById('levelModalTitle').textContent = 'Add Notification Level';
            document.getElementById('levelForm').action = "{{ route('notification-settings.levels.store') }}";
            document.getElementById('levelMethod').value = 'POST';
            document.getElementById('level_name').value = '';
            document.getElementById('level_description').value = '';
            document.getElementById('level_sort_order').value = '0';
            document.querySelectorAll('input[name="severities[]"]').forEach(cb => cb.checked = false);
            document.getElementById('levelModal').classList.remove('hidden');
        }

        function editLevel(level) {
            document.getElementById('levelModalTitle').textContent = 'Edit Notification Level';
            document.getElementById('levelForm').action = `/notification-settings/levels/${level.id}`;
            document.getElementById('levelMethod').value = 'PUT';
            document.getElementById('level_name').value = level.name;
            document.getElementById('level_description').value = level.description || '';
            document.getElementById('level_sort_order').value = level.sort_order;
            document.querySelectorAll('input[name="severities[]"]').forEach(cb => {
                cb.checked = level.severities.includes(cb.value);
            });
            document.getElementById('levelModal').classList.remove('hidden');
        }

        function closeLevelModal() {
            document.getElementById('levelModal').classList.add('hidden');
        }

        function openAddRecipientModal(levelId) {
            document.getElementById('recipientModalTitle').textContent = 'Add Recipient';
            document.getElementById('recipientForm').action = `/notification-settings/levels/${levelId}/recipients`;
            document.getElementById('recipientMethod').value = 'POST';
            document.getElementById('recipient_email').value = '';
            document.getElementById('recipient_name').value = '';
            document.getElementById('recipient_department').value = '';
            document.getElementById('recipientModal').classList.remove('hidden');
        }

        function editRecipient(recipient) {
            document.getElementById('recipientModalTitle').textContent = 'Edit Recipient';
            document.getElementById('recipientForm').action = `/notification-settings/recipients/${recipient.id}`;
            document.getElementById('recipientMethod').value = 'PUT';
            document.getElementById('recipient_email').value = recipient.email;
            document.getElementById('recipient_name').value = recipient.name || '';
            document.getElementById('recipient_department').value = recipient.department || '';
            document.getElementById('recipientModal').classList.remove('hidden');
        }

        function closeRecipientModal() {
            document.getElementById('recipientModal').classList.add('hidden');
        }

        window.onclick = function(event) {
            const levelModal = document.getElementById('levelModal');
            const recipientModal = document.getElementById('recipientModal');
            if (event.target == levelModal) closeLevelModal();
            if (event.target == recipientModal) closeRecipientModal();
        }
    </script>
@endsection
