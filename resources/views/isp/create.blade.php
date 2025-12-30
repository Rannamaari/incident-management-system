@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Add New ISP Link</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create a new ISP backhaul link with escalation contacts</p>
    </div>

    <form method="POST" action="{{ route('isp.store') }}" x-data="ispForm()" class="space-y-6">
        @csrf

        {{-- ISP Link Information --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">ISP Link Information</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="isp_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        ISP Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="isp_name" id="isp_name" required value="{{ old('isp_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    @error('isp_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="circuit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Circuit ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="circuit_id" id="circuit_id" required value="{{ old('circuit_id') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    @error('circuit_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="link_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Link Type <span class="text-red-500">*</span>
                    </label>
                    <select name="link_type" id="link_type" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                        <option value="">Select Type</option>
                        @foreach(\App\Models\IspLink::LINK_TYPES as $type)
                            <option value="{{ $type }}" {{ old('link_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('link_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                        <option value="">Select Status</option>
                        @foreach(\App\Models\IspLink::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', 'Up') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Capacity Information --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Capacity Information</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="total_capacity_gbps" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Total Capacity (Gbps) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="total_capacity_gbps" id="total_capacity_gbps" 
                           x-model.number="totalCapacity" @input="calculateStats()"
                           step="0.01" min="0" required value="{{ old('total_capacity_gbps') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    @error('total_capacity_gbps')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="current_capacity_gbps" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Current Capacity (Gbps) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="current_capacity_gbps" id="current_capacity_gbps" 
                           x-model.number="currentCapacity" @input="calculateStats()"
                           step="0.01" min="0" required value="{{ old('current_capacity_gbps') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    @error('current_capacity_gbps')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-slate-800 rounded-lg">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Lost Capacity:</span>
                        <span class="ml-2 font-semibold text-red-600 dark:text-red-400" x-text="lostCapacity.toFixed(2) + ' Gbps'"></span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Availability:</span>
                        <span class="ml-2 font-semibold" 
                              :class="availability >= 95 ? 'text-green-600 dark:text-green-400' : (availability >= 90 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400')"
                              x-text="availability.toFixed(2) + '%'"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRTG Integration --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">PRTG Integration (Optional)</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Leave blank if not using PRTG monitoring</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="prtg_sensor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        PRTG Sensor ID
                    </label>
                    <input type="text" name="prtg_sensor_id" id="prtg_sensor_id" value="{{ old('prtg_sensor_id') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    @error('prtg_sensor_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="prtg_api_endpoint" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        PRTG API Endpoint
                    </label>
                    <input type="url" name="prtg_api_endpoint" id="prtg_api_endpoint" value="{{ old('prtg_api_endpoint') }}"
                           placeholder="https://..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    @error('prtg_api_endpoint')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Location Information --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Location Information</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="location_a" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Location A <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="location_a" id="location_a" required value="{{ old('location_a') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    @error('location_a')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location_b" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Location B <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="location_b" id="location_b" required value="{{ old('location_b') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    @error('location_b')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Escalation Matrix --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Escalation Matrix</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">At least one contact is required</p>
                </div>
                <button type="button" @click="addContact()"
                        class="inline-flex items-center px-3 py-2 bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Contact
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(contact, index) in contacts" :key="index">
                    <div class="p-4 border border-gray-300 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-slate-800">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label :for="'level_' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Level <span class="text-red-500">*</span>
                                </label>
                                <select :name="'escalation_contacts[' + index + '][escalation_level]'" 
                                        :id="'level_' + index" required x-model="contact.escalation_level"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                                    <option value="">Select</option>
                                    <option value="L1">L1</option>
                                    <option value="L2">L2</option>
                                    <option value="L3">L3</option>
                                </select>
                            </div>

                            <div>
                                <label :for="'name_' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Contact Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" :name="'escalation_contacts[' + index + '][contact_name]'" 
                                       :id="'name_' + index" required x-model="contact.contact_name"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                            </div>

                            <div>
                                <label :for="'phone_' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Phone <span class="text-red-500">*</span>
                                </label>
                                <input type="text" :name="'escalation_contacts[' + index + '][contact_phone]'" 
                                       :id="'phone_' + index" required x-model="contact.contact_phone"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                            </div>

                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label :for="'email_' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Email
                                    </label>
                                    <input type="email" :name="'escalation_contacts[' + index + '][contact_email]'" 
                                           :id="'email_' + index" x-model="contact.contact_email"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="removeContact(index)"
                                            x-show="contacts.length > 1"
                                            class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white dark:bg-slate-900 border border-gray-400 dark:border-white/10 rounded shadow-sm dark:shadow-black/40 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h2>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Additional Information
                </label>
                <textarea name="notes" id="notes" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-white/10 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('isp.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create ISP Link
            </button>
        </div>
    </form>
</div>

<script>
function ispForm() {
    return {
        totalCapacity: {{ old('total_capacity_gbps', 0) }},
        currentCapacity: {{ old('current_capacity_gbps', 0) }},
        lostCapacity: 0,
        availability: 0,
        contacts: [
            {
                escalation_level: '{{ old("escalation_contacts.0.escalation_level", "") }}',
                contact_name: '{{ old("escalation_contacts.0.contact_name", "") }}',
                contact_phone: '{{ old("escalation_contacts.0.contact_phone", "") }}',
                contact_email: '{{ old("escalation_contacts.0.contact_email", "") }}'
            }
        ],
        init() {
            this.calculateStats();
        },
        calculateStats() {
            this.lostCapacity = Math.max(0, this.totalCapacity - this.currentCapacity);
            this.availability = this.totalCapacity > 0 
                ? (this.currentCapacity / this.totalCapacity) * 100 
                : 0;
        },
        addContact() {
            this.contacts.push({
                escalation_level: '',
                contact_name: '',
                contact_phone: '',
                contact_email: ''
            });
        },
        removeContact(index) {
            if (this.contacts.length > 1) {
                this.contacts.splice(index, 1);
            }
        }
    }
}
</script>
@endsection
