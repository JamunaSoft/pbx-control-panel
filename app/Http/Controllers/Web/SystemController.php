<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    /**
     * Display system status.
     */
    public function status()
    {
        // Get system health data
        $systemHealth = $this->getFallbackSystemHealth();

        return view('system.status', compact('systemHealth'));
    }

    /**
     * Get fallback system health data
     */
    private function getFallbackSystemHealth()
    {
        return [
            'asterisk_status' => 'unknown',
            'cpu_usage' => 0,
            'memory_usage' => 0,
            'active_channels' => 0,
            'uptime' => 'Unknown',
        ];
    }
}
