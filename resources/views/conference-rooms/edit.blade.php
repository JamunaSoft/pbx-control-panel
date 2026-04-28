@extends('layouts.app')

@section('title', 'Edit Conference Room')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Edit Conference Room: {{ $conferenceRoom->name }}</h1>
        <a href="{{ route('conference-rooms.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Conference Rooms
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('conference-rooms.update', $conferenceRoom) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Room Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $conferenceRoom->name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Number -->
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number</label>
                        <input type="text" id="room_number" name="room_number" value="{{ old('room_number', $conferenceRoom->room_number) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('room_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PIN -->
                    <div>
                        <label for="pin" class="block text-sm font-medium text-gray-700">PIN (Optional)</label>
                        <input type="text" id="pin" name="pin" value="{{ old('pin', $conferenceRoom->pin) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('pin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Participants -->
                    <div>
                        <label for="max_participants" class="block text-sm font-medium text-gray-700">Max Participants</label>
                        <input type="number" id="max_participants" name="max_participants" value="{{ old('max_participants', $conferenceRoom->max_participants) }}" min="1" max="100"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('max_participants')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Enabled -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" id="enabled" name="enabled" value="1"
                                   {{ old('enabled', $conferenceRoom->enabled) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="enabled" class="ml-2 block text-sm text-gray-900">
                                Enable Conference Room
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('conference-rooms.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Conference Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection