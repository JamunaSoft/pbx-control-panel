@extends('layouts.app')

@section('title', 'Voicemail: ' . $voicemail->mailbox)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Voicemail: {{ $voicemail->mailbox }}</h1>
        <a href="{{ route('voicemails.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Voicemails
        </a>
    </div>

    <!-- Voicemail Details -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <!-- Mailbox -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Mailbox</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $voicemail->mailbox }}</dd>
                </div>

                <!-- Full Name -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $voicemail->fullname ?? 'Not set' }}</dd>
                </div>

                <!-- Email -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $voicemail->email ?? 'Not set' }}</dd>
                </div>

                <!-- Password -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Password</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $voicemail->password ?? 'Not set' }}</dd>
                </div>

                <!-- Associated Extension -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Associated Extension</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($voicemail->extension)
                            {{ $voicemail->extension->extension_number }} ({{ $voicemail->extension->display_name }})
                        @else
                            Not associated
                        @endif
                    </dd>
                </div>

                <!-- Created At -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $voicemail->created_at->format('Y-m-d H:i:s') }}</dd>
                </div>

                <!-- Updated At -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $voicemail->updated_at->format('Y-m-d H:i:s') }}</dd>
                </div>
            </dl>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('voicemails.edit', $voicemail) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Voicemail
                </a>

                <form method="POST" action="{{ route('voicemails.destroy', $voicemail) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this voicemail?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Voicemail
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection