<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CallQueue;
use App\Models\Cdr;
use App\Models\Extension;
use App\Models\Trunk;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get stats directly from database
        $stats = $this->getFallbackStats();

        return view('dashboard.index', compact('stats'));
    }

    /**
     * Get fallback stats when API is unavailable
     */
    private function getFallbackStats()
    {
        return [
            'extensions' => [
                'total' => Extension::count(),
                'online' => Extension::where('status', 'online')->count(),
                'offline' => Extension::where('status', 'offline')->count(),
                'ringing' => Extension::where('status', 'ringing')->count(),
                'busy' => Extension::where('status', 'busy')->count(),
            ],
            'trunks' => [
                'total' => Trunk::count(),
                'active' => Trunk::where('status', 'active')->count(),
                'inactive' => Trunk::where('status', 'inactive')->count(),
            ],
            'queues' => [
                'total' => CallQueue::count(),
                'enabled' => CallQueue::where('enabled', true)->count(),
                'total_members' => CallQueue::withCount('extensions')->get()->sum('extensions_count'),
            ],
            'recent_calls' => [
                'total' => Cdr::where('start', '>=', now()->subDay())->count(),
                'answered' => Cdr::where('start', '>=', now()->subDay())->where('disposition', 'ANSWERED')->count(),
                'missed' => Cdr::where('start', '>=', now()->subDay())->whereIn('disposition', ['NO ANSWER', 'BUSY'])->count(),
            ],
            'system_health' => [
                'asterisk_status' => 'unknown',
                'cpu_usage' => 0,
                'memory_usage' => 0,
                'active_channels' => 0,
                'uptime' => 'Unknown',
            ],
            'call_volume_today' => array_fill(0, 24, 0),
        ];
    }
}
