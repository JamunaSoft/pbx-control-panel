@extends('layouts.app')

@section('title', 'Edit Call Route')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Edit Call Route: {{ $callRoute->name }}</h1>
        <a href="{{ route('call-routes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Call Routes
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('call-routes.update', $callRoute) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Route Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $callRoute->name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pattern -->
                    <div>
                        <label for="pattern" class="block text-sm font-medium text-gray-700">Pattern (Dialplan)</label>
                        <input type="text" id="pattern" name="pattern" value="{{ old('pattern', $callRoute->pattern) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('pattern')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Destination Type -->
                    <div>
                        <label for="destination_type" class="block text-sm font-medium text-gray-700">Destination Type</label>
                        <select id="destination_type" name="destination_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="extension" {{ old('destination_type', $callRoute->destination_type) === 'extension' ? 'selected' : '' }}>Extension</option>
                            <option value="queue" {{ old('destination_type', $callRoute->destination_type) === 'queue' ? 'selected' : '' }}>Queue</option>
                            <option value="ivr" {{ old('destination_type', $callRoute->destination_type) === 'ivr' ? 'selected' : '' }}>IVR</option>
                            <option value="trunk" {{ old('destination_type', $callRoute->destination_type) === 'trunk' ? 'selected' : '' }}>Trunk</option>
                        </select>
                        @error('destination_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Destination -->
                    <div>
                        <label for="destination" class="block text-sm font-medium text-gray-700">Destination</label>
                        <input type="text" id="destination" name="destination" value="{{ old('destination', $callRoute->destination) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('destination')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <input type="number" id="priority" name="priority" value="{{ old('priority', $callRoute->priority) }}" min="1" max="100"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Enabled -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" id="enabled" name="enabled" value="1"
                                   {{ old('enabled', $callRoute->enabled) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="enabled" class="ml-2 block text-sm text-gray-900">
                                Enable Route
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('call-routes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Call Route
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection