@extends('layouts.app')

@section('title', 'Edit Trunk')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Edit Trunk</h2>

            <form method="POST" action="{{ route('trunks.update', $trunk) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Trunk Name -->
                    <div class="sm:col-span-1">
                        <label for="trunk_name" class="block text-sm font-medium text-gray-700">Trunk Name</label>
                        <input type="text" name="trunk_name" id="trunk_name" value="{{ old('trunk_name', $trunk->trunk_name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               required>
                        @error('trunk_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Provider -->
                    <div class="sm:col-span-1">
                        <label for="provider" class="block text-sm font-medium text-gray-700">Provider</label>
                        <input type="text" name="provider" id="provider" value="{{ old('provider', $trunk->provider) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               required>
                        @error('provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Host -->
                    <div class="sm:col-span-1">
                        <label for="host" class="block text-sm font-medium text-gray-700">Host</label>
                        <input type="text" name="host" id="host" value="{{ old('host', $trunk->host) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               required>
                        @error('host')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Port -->
                    <div class="sm:col-span-1">
                        <label for="port" class="block text-sm font-medium text-gray-700">Port</label>
                        <input type="number" name="port" id="port" value="{{ old('port', $trunk->port) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               min="1" max="65535">
                        @error('port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="sm:col-span-1">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $trunk->username) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secret -->
                    <div class="sm:col-span-1">
                        <label for="secret" class="block text-sm font-medium text-gray-700">Secret</label>
                        <input type="password" name="secret" id="secret"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Leave blank to keep current">
                        @error('secret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="sm:col-span-1">
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="sip" {{ old('type', $trunk->type) == 'sip' ? 'selected' : '' }}>SIP</option>
                            <option value="iax" {{ old('type', $trunk->type) == 'iax' ? 'selected' : '' }}>IAX</option>
                            <option value="pjsip" {{ old('type', $trunk->type) == 'pjsip' ? 'selected' : '' }}>PJSIP</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Context -->
                    <div class="sm:col-span-1">
                        <label for="context" class="block text-sm font-medium text-gray-700">Context</label>
                        <input type="text" name="context" id="context" value="{{ old('context', $trunk->context) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('context')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cost per Minute -->
                    <div class="sm:col-span-1">
                        <label for="cost_per_minute" class="block text-sm font-medium text-gray-700">Cost per Minute</label>
                        <input type="number" name="cost_per_minute" id="cost_per_minute" value="{{ old('cost_per_minute', $trunk->cost_per_minute) }}" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('cost_per_minute')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="sm:col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="active" {{ old('status', $trunk->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $trunk->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Failover Enabled -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="failover_enabled" id="failover_enabled" value="1"
                                   {{ old('failover_enabled', $trunk->failover_enabled) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="failover_enabled" class="ml-2 block text-sm text-gray-900">
                                Enable Failover
                            </label>
                        </div>
                        @error('failover_enabled')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('trunks.show', $trunk) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Trunk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection