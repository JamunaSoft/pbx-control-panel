<?php

namespace App\Services\Asterisk;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class AsteriskService
{
    private $ami;
    private $ari;
    private $connected = false;

    public function __construct()
    {
        $this->ami = new AsteriskAMI(
            config('asterisk.ami.host', '127.0.0.1'),
            config('asterisk.ami.port', 5038),
            config('asterisk.ami.username', 'admin'),
            config('asterisk.ami.secret', 'password')
        );

        $this->ari = new AsteriskARI(
            config('asterisk.ari.host', '127.0.0.1'),
            config('asterisk.ari.port', 8088),
            config('asterisk.ari.username', 'asterisk'),
            config('asterisk.ari.password', 'asterisk'),
            config('asterisk.ari.app_name', 'pbx-control-panel')
        );
    }

    /**
     * Connect to Asterisk services
     */
    public function connect()
    {
        $amiConnected = $this->ami->connect();
        $ariAvailable = $this->ari->isAvailable();

        $this->connected = $amiConnected && $ariAvailable;

        Log::info('Asterisk connection status', [
            'ami' => $amiConnected,
            'ari' => $ariAvailable,
            'overall' => $this->connected
        ]);

        return $this->connected;
    }

    /**
     * Check if connected to Asterisk
     */
    public function isConnected()
    {
        return $this->connected && $this->ami->isConnected() && $this->ari->isAvailable();
    }

    /**
     * Get extension status
     */
    public function getExtensionStatus($extension)
    {
        if (!$this->isConnected()) {
            return Cache::get("extension_status_{$extension}", 'unknown');
        }

        try {
            $status = $this->ami->getExtensionStatus($extension);
            Cache::put("extension_status_{$extension}", $status, 60); // Cache for 1 minute
            return $status;
        } catch (Exception $e) {
            Log::error('Failed to get extension status', ['extension' => $extension, 'error' => $e->getMessage()]);
            return Cache::get("extension_status_{$extension}", 'unknown');
        }
    }

    /**
     * Get all extensions status
     */
    public function getAllExtensionsStatus(Collection $extensions)
    {
        $statuses = [];

        foreach ($extensions as $extension) {
            $statuses[$extension->extension_number] = $this->getExtensionStatus($extension->extension_number);
        }

        return $statuses;
    }

    /**
     * Get active calls
     */
    public function getActiveCalls()
    {
        if (!$this->isConnected()) {
            return Cache::get('active_calls', []);
        }

        try {
            // Try ARI first, fallback to AMI
            $calls = $this->ari->getChannels() ?: $this->ami->getActiveCalls();

            // Normalize the data
            $normalizedCalls = [];
            if ($calls) {
                foreach ($calls as $call) {
                    $normalizedCalls[] = [
                        'id' => $call['id'] ?? $call['channel'] ?? uniqid(),
                        'caller' => $call['caller'] ?? $call['caller_id'] ?? '',
                        'callee' => $call['callee'] ?? $call['connected_line_num'] ?? '',
                        'status' => $call['status'] ?? $call['state'] ?? 'unknown',
                        'channel' => $call['channel'] ?? $call['id'] ?? '',
                        'start_time' => $call['start_time'] ?? now()->toISOString(),
                        'duration' => $call['duration'] ?? 0,
                    ];
                }
            }

            Cache::put('active_calls', $normalizedCalls, 30); // Cache for 30 seconds
            return $normalizedCalls;
        } catch (Exception $e) {
            Log::error('Failed to get active calls', ['error' => $e->getMessage()]);
            return Cache::get('active_calls', []);
        }
    }

    /**
     * Originate a call
     */
    public function originateCall($from, $to, $callerId = null)
    {
        if (!$this->isConnected()) {
            throw new Exception('Not connected to Asterisk');
        }

        try {
            // Try ARI first, fallback to AMI
            $channel = "SIP/{$from}";

            $result = $this->ari->originateChannel($channel, $to, 'default', $callerId);

            if (!$result) {
                // Fallback to AMI
                $result = $this->ami->originateCall($channel, 'default', $to, 1, $callerId);
            }

            if ($result) {
                Log::info('Call originated successfully', ['from' => $from, 'to' => $to]);
                return true;
            }

            throw new Exception('Failed to originate call');
        } catch (Exception $e) {
            Log::error('Call origination failed', [
                'from' => $from,
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Hang up a call
     */
    public function hangupCall($channelId)
    {
        if (!$this->isConnected()) {
            throw new Exception('Not connected to Asterisk');
        }

        try {
            // Try ARI first, fallback to AMI
            $result = $this->ari->hangupChannel($channelId);

            if (!$result) {
                // Fallback to AMI
                $result = $this->ami->hangupChannel($channelId);
            }

            if ($result) {
                Log::info('Call hung up successfully', ['channel' => $channelId]);
                return true;
            }

            throw new Exception('Failed to hang up call');
        } catch (Exception $e) {
            Log::error('Call hangup failed', [
                'channel' => $channelId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Transfer a call
     */
    public function transferCall($channelId, $extension, $context = 'default')
    {
        if (!$this->isConnected()) {
            throw new Exception('Not connected to Asterisk');
        }

        try {
            $result = $this->ami->transferCall($channelId, $extension, $context);

            if ($result) {
                Log::info('Call transferred successfully', [
                    'channel' => $channelId,
                    'to' => $extension
                ]);
                return true;
            }

            throw new Exception('Failed to transfer call');
        } catch (Exception $e) {
            Log::error('Call transfer failed', [
                'channel' => $channelId,
                'to' => $extension,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get system information
     */
    public function getSystemInfo()
    {
        if (!$this->isConnected()) {
            return [
                'status' => 'disconnected',
                'uptime' => 'Unknown',
                'version' => 'Unknown',
                'active_channels' => 0,
                'active_calls' => 0,
            ];
        }

        try {
            $amiInfo = $this->ami->getSystemInfo();
            $ariInfo = $this->ari->getInfo();
            $channels = $this->getActiveCalls();

            return [
                'status' => 'connected',
                'uptime' => $amiInfo['uptime'] ?? 'Unknown',
                'version' => $ariInfo['build']['version'] ?? 'Unknown',
                'active_channels' => count($channels),
                'active_calls' => count(array_filter($channels, fn($c) => $c['status'] === 'up')),
                'last_updated' => now()->toISOString(),
            ];
        } catch (Exception $e) {
            Log::error('Failed to get system info', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'uptime' => 'Unknown',
                'version' => 'Unknown',
                'active_channels' => 0,
                'active_calls' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get queue status
     */
    public function getQueueStatus()
    {
        // This would typically use AMI QueueStatus command
        // For now, return mock data
        return [
            [
                'name' => 'support',
                'members' => 5,
                'waiting_calls' => 2,
                'longest_wait' => 45,
                'completed_calls' => 125,
                'abandoned_calls' => 3,
            ],
            [
                'name' => 'sales',
                'members' => 3,
                'waiting_calls' => 0,
                'longest_wait' => 0,
                'completed_calls' => 89,
                'abandoned_calls' => 1,
            ],
        ];
    }

    /**
     * Reload Asterisk configuration
     */
    public function reloadConfig($module = null)
    {
        if (!$this->isConnected()) {
            throw new Exception('Not connected to Asterisk');
        }

        try {
            $command = $module ? "module reload {$module}" : "reload";

            // This would send AMI command to reload
            Log::info('Asterisk config reload requested', ['module' => $module]);

            return true;
        } catch (Exception $e) {
            Log::error('Config reload failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Test connection
     */
    public function testConnection()
    {
        return [
            'ami_connected' => $this->ami->isConnected(),
            'ari_available' => $this->ari->isAvailable(),
            'overall_connected' => $this->isConnected(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Disconnect from services
     */
    public function disconnect()
    {
        $this->ami->disconnect();
        $this->connected = false;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}