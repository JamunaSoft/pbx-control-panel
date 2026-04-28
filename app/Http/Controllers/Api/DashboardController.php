<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\Trunk;
use App\Models\CallQueue;
use App\Models\Cdr;
use App\Services\Asterisk\AsteriskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DashboardController extends Controller
{
    protected $asterisk;

    public function __construct(AsteriskService $asterisk)
    {
        $this->asterisk = $asterisk;
    }

    /**
     * Get dashboard statistics
     */
    public function stats(Request $request)
    {
        $stats = [
            // Extensions
            'extensions' => [
                'total' => Extension::count(),
                'online' => Extension::where('status', 'online')->count(),
                'offline' => Extension::where('status', 'offline')->count(),
                'ringing' => Extension::where('status', 'ringing')->count(),
                'busy' => Extension::where('status', 'busy')->count(),
            ],

            // Trunks
            'trunks' => [
                'total' => Trunk::count(),
                'active' => Trunk::where('status', 'active')->count(),
                'inactive' => Trunk::where('status', 'inactive')->count(),
            ],

            // Queues
            'queues' => [
                'total' => CallQueue::count(),
                'enabled' => CallQueue::where('enabled', true)->count(),
                'total_members' => CallQueue::withCount('extensions')->get()->sum('extensions_count'),
            ],

            // Recent calls (last 24 hours)
            'recent_calls' => [
                'total' => Cdr::where('start', '>=', now()->subDay())->count(),
                'answered' => Cdr::where('start', '>=', now()->subDay())->where('disposition', 'ANSWERED')->count(),
                'missed' => Cdr::where('start', '>=', now()->subDay())->whereIn('disposition', ['NO ANSWER', 'BUSY'])->count(),
            ],

            // System health from Asterisk
            'system_health' => $this->asterisk->getSystemInfo(),

            // Call volume for today
            'call_volume_today' => $this->getCallVolumeToday(),
        ];

        return response()->json($stats);
    }

    /**
     * Get active calls using Asterisk service
     */
    public function activeCalls(Request $request)
    {
        $activeCalls = $this->asterisk->getActiveCalls();

        return response()->json([
            'active_calls' => $activeCalls,
            'total' => count($activeCalls),
            'connection_status' => $this->asterisk->isConnected() ? 'connected' : 'disconnected',
        ]);
    }

    /**
     * Get queue status
     */
    public function queueStatus(Request $request)
    {
        $queues = CallQueue::with(['extensions'])->get();

        $queueStatus = $queues->map(function ($queue) {
            return [
                'id' => $queue->id,
                'name' => $queue->queue_name,
                'strategy' => $queue->strategy,
                'members' => $queue->extensions->count(),
                'waiting_calls' => 0, // Would come from Asterisk queue status
                'longest_wait' => 0, // Would come from Asterisk queue status
                'completed_calls' => 0, // Would come from Asterisk queue status
                'abandoned_calls' => 0, // Would come from Asterisk queue status
            ];
        });

        return response()->json([
            'queues' => $queueStatus,
            'connection_status' => $this->asterisk->isConnected() ? 'connected' : 'disconnected',
        ]);
    }

    /**
     * Get extension status updates with real-time Asterisk data
     */
    public function extensionStatus(Request $request)
    {
        $extensions = Extension::select(['id', 'extension_number', 'display_name', 'status'])
                              ->get();

        // Update status from Asterisk if connected
        if ($this->asterisk->isConnected()) {
            $extensions->each(function ($extension) {
                $realStatus = $this->asterisk->getExtensionStatus($extension->extension_number);
                if ($realStatus !== 'unknown') {
                    $extension->status = $realStatus;
                    $extension->save(); // Update the database with real status
                }
            });
        }

        return response()->json([
            'extensions' => $extensions,
            'last_updated' => now()->toISOString(),
            'connection_status' => $this->asterisk->isConnected() ? 'connected' : 'disconnected',
        ]);
    }

    /**
     * Get call volume for today by hour
     */
    private function getCallVolumeToday()
    {
        $today = Carbon::today();

        $callVolume = Cdr::selectRaw('HOUR(start) as hour, COUNT(*) as calls')
                        ->whereDate('start', $today)
                        ->groupBy('hour')
                        ->orderBy('hour')
                        ->pluck('calls', 'hour')
                        ->toArray();

        // Fill in missing hours with 0
        $volumeByHour = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $volumeByHour[$hour] = $callVolume[$hour] ?? 0;
        }

        return $volumeByHour;
    }
}
