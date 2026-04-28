@extends('layouts.app')

@section('title', 'Create Extension')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Create Extension</h1>
        <a href="{{ route('extensions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Extensions
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('extensions.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Extension Number -->
                    <div>
                        <label for="extension_number" class="block text-sm font-medium text-gray-700">Extension Number</label>
                        <input type="text" id="extension_number" name="extension_number" value="{{ old('extension_number') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('extension_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name</label>
                        <input type="text" id="display_name" name="display_name" value="{{ old('display_name') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" value="{{ old('password') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email (Optional)</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Device Type -->
                    <div>
                        <label for="device_type" class="block text-sm font-medium text-gray-700">Device Type</label>
                        <select id="device_type" name="device_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="sip" {{ old('device_type', 'sip') === 'sip' ? 'selected' : '' }}>SIP</option>
                            <option value="pjsip" {{ old('device_type', 'sip') === 'pjsip' ? 'selected' : '' }}>PJSIP</option>
                        </select>
                        @error('device_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Context -->
                    <div>
                        <label for="context" class="block text-sm font-medium text-gray-700">Context</label>
                        <input type="text" id="context" name="context" value="{{ old('context', 'default') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('context')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Call Forwarding -->
                    <div class="md:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" id="call_forwarding_enabled" name="call_forwarding_enabled" value="1"
                                   {{ old('call_forwarding_enabled') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="call_forwarding_enabled" class="ml-2 block text-sm text-gray-900">
                                Enable Call Forwarding
                            </label>
                        </div>
                        <div class="mt-2" id="call_forwarding_number_div" style="display: {{ old('call_forwarding_enabled') ? 'block' : 'none' }};">
                            <label for="call_forwarding_number" class="block text-sm font-medium text-gray-700">Forwarding Number</label>
                            <input type="text" id="call_forwarding_number" name="call_forwarding_number" value="{{ old('call_forwarding_number') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Do Not Disturb -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" id="dnd_enabled" name="dnd_enabled" value="1"
                                   {{ old('dnd_enabled') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="dnd_enabled" class="ml-2 block text-sm text-gray-900">
                                Do Not Disturb
                            </label>
                        </div>
                    </div>

                    <!-- Voicemail -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" id="voicemail_enabled" name="voicemail_enabled" value="1"
                                   {{ old('voicemail_enabled', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="voicemail_enabled" class="ml-2 block text-sm text-gray-900">
                                Enable Voicemail
                            </label>
                        </div>
                        <div class="mt-2" id="voicemail_box_div" style="display: {{ old('voicemail_enabled', true) ? 'block' : 'none' }};">
                            <label for="voicemail_box" class="block text-sm font-medium text-gray-700">Voicemail Box</label>
                            <input type="text" id="voicemail_box" name="voicemail_box" value="{{ old('voicemail_box', old('extension_number')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('extensions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Extension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Call forwarding toggle
    document.getElementById('call_forwarding_enabled').addEventListener('change', function() {
        document.getElementById('call_forwarding_number_div').style.display = this.checked ? 'block' : 'none';
    });

    // Voicemail toggle
    document.getElementById('voicemail_enabled').addEventListener('change', function() {
        document.getElementById('voicemail_box_div').style.display = this.checked ? 'block' : 'none';
    });

    // Auto-fill voicemail box with extension number
    document.getElementById('extension_number').addEventListener('input', function() {
        if (document.getElementById('voicemail_enabled').checked) {
            document.getElementById('voicemail_box').value = this.value;
        }
    });
});
</script>
@endsection