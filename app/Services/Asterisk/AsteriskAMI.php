<?php

namespace App\Services\Asterisk;

use Exception;
use Illuminate\Support\Facades\Log;

class AsteriskAMI
{
    private $socket;
    private $host;
    private $port;
    private $username;
    private $secret;
    private $connected = false;
    private $authenticated = false;

    public function __construct($host = '127.0.0.1', $port = 5038, $username = 'admin', $secret = 'password')
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->secret = $secret;
    }

    /**
     * Connect to Asterisk AMI
     */
    public function connect()
    {
        try {
            $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, 10);

            if (!$this->socket) {
                throw new Exception("Cannot connect to Asterisk AMI: $errstr ($errno)");
            }

            $this->connected = true;
            $this->readWelcomeMessage();

            return $this->authenticate();
        } catch (Exception $e) {
            Log::error('Asterisk AMI connection failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Read welcome message
     */
    private function readWelcomeMessage()
    {
        $response = $this->readResponse();
        if (!str_contains($response, 'Asterisk Call Manager')) {
            throw new Exception('Invalid AMI welcome message');
        }
    }

    /**
     * Authenticate with Asterisk
     */
    private function authenticate()
    {
        $this->sendCommand("Action: Login\r\nUsername: {$this->username}\r\nSecret: {$this->secret}\r\n\r\n");

        $response = $this->readResponse();

        if (str_contains($response, 'Response: Success')) {
            $this->authenticated = true;
            return true;
        }

        throw new Exception('AMI authentication failed');
    }

    /**
     * Send command to Asterisk
     */
    public function sendCommand($command)
    {
        if (!$this->connected || !$this->authenticated) {
            throw new Exception('Not connected to Asterisk AMI');
        }

        fwrite($this->socket, $command);
    }

    /**
     * Read response from Asterisk
     */
    public function readResponse()
    {
        $response = '';
        $timeout = time() + 5; // 5 second timeout

        while (!feof($this->socket) && time() < $timeout) {
            $line = fgets($this->socket, 4096);

            if ($line === false) {
                break;
            }

            $response .= $line;

            // Check for end of response
            if (str_contains($response, "\r\n\r\n")) {
                break;
            }
        }

        return $response;
    }

    /**
     * Get extension status
     */
    public function getExtensionStatus($extension)
    {
        $actionId = uniqid();

        $command = "Action: ExtensionState\r\n";
        $command .= "ActionID: {$actionId}\r\n";
        $command .= "Exten: {$extension}\r\n";
        $command .= "Context: default\r\n";
        $command .= "\r\n";

        $this->sendCommand($command);
        $response = $this->readResponse();

        // Parse status from response
        if (preg_match('/State: (\d+)/', $response, $matches)) {
            return $this->mapExtensionState($matches[1]);
        }

        return 'unknown';
    }

    /**
     * Map Asterisk extension state to our status
     */
    private function mapExtensionState($state)
    {
        $states = [
            '-2' => 'offline',  // Extension Removed
            '-1' => 'offline',  // Extension Deactivated
            '0' => 'offline',   // Idle
            '1' => 'busy',      // In Use
            '2' => 'busy',      // Busy
            '4' => 'offline',   // Unavailable
            '8' => 'ringing',   // Ringing
            '16' => 'busy',     // On Hold
        ];

        return $states[$state] ?? 'unknown';
    }

    /**
     * Get active calls
     */
    public function getActiveCalls()
    {
        $actionId = uniqid();

        $command = "Action: Status\r\n";
        $command .= "ActionID: {$actionId}\r\n";
        $command .= "\r\n";

        $this->sendCommand($command);

        $calls = [];
        $timeout = time() + 10;

        while (time() < $timeout) {
            $response = $this->readResponse();

            if (str_contains($response, 'Event: Status')) {
                $call = $this->parseStatusEvent($response);
                if ($call) {
                    $calls[] = $call;
                }
            }

            if (str_contains($response, "ActionID: {$actionId}")) {
                break;
            }

            usleep(100000); // 0.1 second
        }

        return $calls;
    }

    /**
     * Parse status event
     */
    private function parseStatusEvent($response)
    {
        $lines = explode("\r\n", $response);
        $event = [];

        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $event[trim($key)] = trim($value);
            }
        }

        if (isset($event['Channel'])) {
            return [
                'id' => $event['Channel'],
                'caller' => $event['CallerIDNum'] ?? '',
                'callee' => $event['ConnectedLineNum'] ?? '',
                'status' => $this->mapChannelState($event['ChannelState'] ?? ''),
                'channel' => $event['Channel'],
                'start_time' => isset($event['Seconds']) ? now()->subSeconds($event['Seconds'])->toISOString() : now()->toISOString(),
            ];
        }

        return null;
    }

    /**
     * Map channel state
     */
    private function mapChannelState($state)
    {
        $states = [
            '0' => 'down',
            '3' => 'dialing',
            '4' => 'alerting',
            '6' => 'up',
            '7' => 'busy',
        ];

        return $states[$state] ?? 'unknown';
    }

    /**
     * Originate a call
     */
    public function originateCall($channel, $context, $extension, $priority = 1, $callerId = null)
    {
        $actionId = uniqid();

        $command = "Action: Originate\r\n";
        $command .= "ActionID: {$actionId}\r\n";
        $command .= "Channel: {$channel}\r\n";
        $command .= "Context: {$context}\r\n";
        $command .= "Exten: {$extension}\r\n";
        $command .= "Priority: {$priority}\r\n";

        if ($callerId) {
            $command .= "CallerID: {$callerId}\r\n";
        }

        $command .= "Timeout: 30000\r\n"; // 30 seconds
        $command .= "\r\n";

        $this->sendCommand($command);
        $response = $this->readResponse();

        return str_contains($response, 'Response: Success');
    }

    /**
     * Hang up a channel
     */
    public function hangupChannel($channel)
    {
        $actionId = uniqid();

        $command = "Action: Hangup\r\n";
        $command .= "ActionID: {$actionId}\r\n";
        $command .= "Channel: {$channel}\r\n";
        $command .= "\r\n";

        $this->sendCommand($command);
        $response = $this->readResponse();

        return str_contains($response, 'Response: Success');
    }

    /**
     * Transfer call
     */
    public function transferCall($channel, $extension, $context = 'default')
    {
        $actionId = uniqid();

        $command = "Action: Redirect\r\n";
        $command .= "ActionID: {$actionId}\r\n";
        $command .= "Channel: {$channel}\r\n";
        $command .= "Exten: {$extension}\r\n";
        $command .= "Context: {$context}\r\n";
        $command .= "Priority: 1\r\n";
        $command .= "\r\n";

        $this->sendCommand($command);
        $response = $this->readResponse();

        return str_contains($response, 'Response: Success');
    }

    /**
     * Get system information
     */
    public function getSystemInfo()
    {
        $actionId = uniqid();

        $command = "Action: CoreStatus\r\n";
        $command .= "ActionID: {$actionId}\r\n";
        $command .= "\r\n";

        $this->sendCommand($command);
        $response = $this->readResponse();

        $info = [];

        if (preg_match('/CoreStartupTime: (.+)/', $response, $matches)) {
            $info['uptime'] = $matches[1];
        }

        if (preg_match('/CoreReloadTime: (.+)/', $response, $matches)) {
            $info['last_reload'] = $matches[1];
        }

        return $info;
    }

    /**
     * Disconnect from AMI
     */
    public function disconnect()
    {
        if ($this->connected) {
            $this->sendCommand("Action: Logoff\r\n\r\n");
            fclose($this->socket);
            $this->connected = false;
            $this->authenticated = false;
        }
    }

    /**
     * Check if connected
     */
    public function isConnected()
    {
        return $this->connected && $this->authenticated;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}