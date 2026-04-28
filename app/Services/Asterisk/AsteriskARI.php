<?php

namespace App\Services\Asterisk;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsteriskARI
{
    private $baseUrl;
    private $username;
    private $password;
    private $appName;

    public function __construct($host = '127.0.0.1', $port = 8088, $username = 'asterisk', $password = 'asterisk', $appName = 'pbx-control-panel')
    {
        $this->baseUrl = "http://{$host}:{$port}/ari";
        $this->username = $username;
        $this->password = $password;
        $this->appName = $appName;
    }

    /**
     * Make authenticated HTTP request
     */
    private function makeRequest($method, $endpoint, $data = [])
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(10)
                ->$method($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("ARI request failed: {$method} {$endpoint}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error("ARI request exception: {$method} {$endpoint}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get ARI info
     */
    public function getInfo()
    {
        return $this->makeRequest('GET', '/info');
    }

    /**
     * Get active channels
     */
    public function getChannels()
    {
        return $this->makeRequest('GET', '/channels');
    }

    /**
     * Get channel details
     */
    public function getChannel($channelId)
    {
        return $this->makeRequest('GET', "/channels/{$channelId}");
    }

    /**
     * Create channel (originate call)
     */
    public function originateChannel($endpoint, $extension, $context = 'default', $callerId = null, $timeout = 30)
    {
        $data = [
            'endpoint' => $endpoint,
            'extension' => $extension,
            'context' => $context,
            'priority' => 1,
            'app' => $this->appName,
            'timeout' => $timeout * 1000, // Convert to milliseconds
        ];

        if ($callerId) {
            $data['callerId'] = $callerId;
        }

        return $this->makeRequest('POST', '/channels', $data);
    }

    /**
     * Hang up channel
     */
    public function hangupChannel($channelId)
    {
        return $this->makeRequest('DELETE', "/channels/{$channelId}");
    }

    /**
     * Answer channel
     */
    public function answerChannel($channelId)
    {
        return $this->makeRequest('POST', "/channels/{$channelId}/answer");
    }

    /**
     * Play audio on channel
     */
    public function playAudio($channelId, $media)
    {
        $data = [
            'media' => $media,
        ];

        return $this->makeRequest('POST', "/channels/{$channelId}/play", $data);
    }

    /**
     * Record channel
     */
    public function recordChannel($channelId, $name, $format = 'wav', $maxDuration = 0, $maxSilence = 0)
    {
        $data = [
            'name' => $name,
            'format' => $format,
            'maxDurationSeconds' => $maxDuration,
            'maxSilenceSeconds' => $maxSilence,
        ];

        return $this->makeRequest('POST', "/channels/{$channelId}/record", $data);
    }

    /**
     * Stop recording
     */
    public function stopRecording($channelId, $recordingName)
    {
        return $this->makeRequest('DELETE', "/channels/{$channelId}/recordings/{$recordingName}");
    }

    /**
     * Get bridges
     */
    public function getBridges()
    {
        return $this->makeRequest('GET', '/bridges');
    }

    /**
     * Create bridge
     */
    public function createBridge($type = 'mixing', $name = null)
    {
        $data = [
            'type' => $type,
        ];

        if ($name) {
            $data['name'] = $name;
        }

        return $this->makeRequest('POST', '/bridges', $data);
    }

    /**
     * Add channel to bridge
     */
    public function addChannelToBridge($bridgeId, $channelId)
    {
        return $this->makeRequest('POST', "/bridges/{$bridgeId}/addChannel", [
            'channel' => $channelId,
        ]);
    }

    /**
     * Remove channel from bridge
     */
    public function removeChannelFromBridge($bridgeId, $channelId)
    {
        return $this->makeRequest('POST', "/bridges/{$bridgeId}/removeChannel", [
            'channel' => $channelId,
        ]);
    }

    /**
     * Delete bridge
     */
    public function deleteBridge($bridgeId)
    {
        return $this->makeRequest('DELETE', "/bridges/{$bridgeId}");
    }

    /**
     * Get applications
     */
    public function getApplications()
    {
        return $this->makeRequest('GET', '/applications');
    }

    /**
     * Subscribe to events
     */
    public function subscribeToEvents($eventSource)
    {
        return $this->makeRequest('POST', "/applications/{$this->appName}/subscription", [
            'eventSource' => $eventSource,
        ]);
    }

    /**
     * Unsubscribe from events
     */
    public function unsubscribeFromEvents($eventSource)
    {
        return $this->makeRequest('DELETE', "/applications/{$this->appName}/subscription", [
            'eventSource' => $eventSource,
        ]);
    }

    /**
     * Get endpoints
     */
    public function getEndpoints()
    {
        return $this->makeRequest('GET', '/endpoints');
    }

    /**
     * Get endpoint details
     */
    public function getEndpoint($tech, $resource)
    {
        return $this->makeRequest('GET', "/endpoints/{$tech}/{$resource}");
    }

    /**
     * Send message to endpoint
     */
    public function sendMessage($tech, $resource, $body)
    {
        return $this->makeRequest('PUT', "/endpoints/{$tech}/{$resource}/sendMessage", [
            'body' => $body,
        ]);
    }

    /**
     * Get mailboxes
     */
    public function getMailboxes()
    {
        return $this->makeRequest('GET', '/mailboxes');
    }

    /**
     * Get mailbox details
     */
    public function getMailbox($mailboxName)
    {
        return $this->makeRequest('GET', "/mailboxes/{$mailboxName}");
    }

    /**
     * Delete mailbox
     */
    public function deleteMailbox($mailboxName)
    {
        return $this->makeRequest('DELETE', "/mailboxes/{$mailboxName}");
    }

    /**
     * Get recordings
     */
    public function getRecordings()
    {
        return $this->makeRequest('GET', '/recordings');
    }

    /**
     * Get recording details
     */
    public function getRecording($recordingName)
    {
        return $this->makeRequest('GET', "/recordings/{$recordingName}");
    }

    /**
     * Delete recording
     */
    public function deleteRecording($recordingName)
    {
        return $this->makeRequest('DELETE', "/recordings/{$recordingName}");
    }

    /**
     * Get sounds
     */
    public function getSounds()
    {
        return $this->makeRequest('GET', '/sounds');
    }

    /**
     * Check if ARI is available
     */
    public function isAvailable()
    {
        $info = $this->getInfo();
        return $info !== null;
    }
}