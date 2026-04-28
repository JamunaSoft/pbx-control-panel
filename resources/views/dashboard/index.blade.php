@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- System Status -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">System Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Asterisk Status</p>
                            <p class="text-lg font-semibold text-green-600">{{ $stats['system_health']['asterisk_status'] ?? 'Unknown' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">Active Channels</p>
                            <p class="text-lg font-semibold text-blue-600">{{ $stats['system_health']['active_channels'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">CPU Usage</p>
                            <p class="text-lg font-semibold text-yellow-600">{{ $stats['system_health']['cpu_usage'] ?? 0 }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-800">Memory Usage</p>
                            <p class="text-lg font-semibold text-purple-600">{{ $stats['system_health']['memory_usage'] ?? 0 }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Extensions -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Extensions</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">{{ $stats['extensions']['total'] }}</div>
                                <div class="text-sm text-green-600">{{ $stats['extensions']['online'] }} online</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trunks -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Trunks</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">{{ $stats['trunks']['total'] }}</div>
                                <div class="text-sm text-green-600">{{ $stats['trunks']['active'] }} active</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queues -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Call Queues</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">{{ $stats['queues']['total'] }}</div>
                                <div class="text-sm text-blue-600">{{ $stats['queues']['total_members'] }} members</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Calls -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Calls</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">{{ $stats['recent_calls']['total'] }}</div>
                                <div class="text-sm text-green-600">{{ $stats['recent_calls']['answered'] }} answered</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call Volume Chart -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Call Volume Today</h3>
            <div class="h-64">
                <canvas id="callVolumeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">System started successfully</p>
                        <p class="text-sm text-gray-500">All services are running normally</p>
                    </div>
                    <div class="flex-shrink-0 text-sm text-gray-500">
                        2 hours ago
                    </div>
                </div>
                <!-- More activity items would be added here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Laravel Echo with Pusher
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config("broadcasting.connections.pusher.key") }}',
        cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
        encrypted: true
    });

    // Listen for extension status updates
    window.Echo.channel('extensions')
        .listen('.extension.status.updated', (e) => {
            console.log('Extension status updated:', e.extension);
            // Update extension status in UI
            updateExtensionStatus(e.extension);
        });

    // Function to update extension status display
    function updateExtensionStatus(extension) {
        const statusElement = document.querySelector(`[data-extension-id="${extension.id}"] .status-badge`);
        if (statusElement) {
            // Remove existing status classes
            statusElement.className = 'inline-flex px-2 py-1 text-xs font-semibold rounded-full status-badge';

            // Add new status class
            switch(extension.status) {
                case 'online':
                    statusElement.classList.add('bg-green-100', 'text-green-800');
                    break;
                case 'ringing':
                    statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
                    break;
                case 'busy':
                    statusElement.classList.add('bg-red-100', 'text-red-800');
                    break;
                default:
                    statusElement.classList.add('bg-gray-100', 'text-gray-800');
            }

            statusElement.textContent = extension.status.charAt(0).toUpperCase() + extension.status.slice(1);
        }
    }

    // Chart initialization
    const ctx = document.getElementById('callVolumeChart').getContext('2d');
    const callVolumeData = @json($stats['call_volume_today'] ?? array_fill(0, 24, 0));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
            datasets: [{
                label: 'Calls',
                data: callVolumeData,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush
@endsection