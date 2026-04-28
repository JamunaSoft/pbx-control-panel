<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Asterisk AMI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to Asterisk Manager Interface (AMI)
    |
    */

    'ami' => [
        'host' => env('ASTERISK_AMI_HOST', '127.0.0.1'),
        'port' => env('ASTERISK_AMI_PORT', 5038),
        'username' => env('ASTERISK_AMI_USERNAME', 'admin'),
        'secret' => env('ASTERISK_AMI_SECRET', 'password'),
        'connect_timeout' => env('ASTERISK_AMI_CONNECT_TIMEOUT', 10),
        'read_timeout' => env('ASTERISK_AMI_READ_TIMEOUT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asterisk ARI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to Asterisk REST Interface (ARI)
    |
    */

    'ari' => [
        'host' => env('ASTERISK_ARI_HOST', '127.0.0.1'),
        'port' => env('ASTERISK_ARI_PORT', 8088),
        'username' => env('ASTERISK_ARI_USERNAME', 'asterisk'),
        'password' => env('ASTERISK_ARI_PASSWORD', 'asterisk'),
        'app_name' => env('ASTERISK_ARI_APP_NAME', 'pbx-control-panel'),
        'connect_timeout' => env('ASTERISK_ARI_CONNECT_TIMEOUT', 10),
        'read_timeout' => env('ASTERISK_ARI_READ_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asterisk General Configuration
    |--------------------------------------------------------------------------
    |
    | General configuration options
    |
    */

    'general' => [
        'auto_connect' => env('ASTERISK_AUTO_CONNECT', true),
        'connection_retry_interval' => env('ASTERISK_RETRY_INTERVAL', 30), // seconds
        'max_retry_attempts' => env('ASTERISK_MAX_RETRIES', 5),
        'cache_ttl' => env('ASTERISK_CACHE_TTL', 60), // seconds
        'log_events' => env('ASTERISK_LOG_EVENTS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asterisk Event Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for event handling and real-time updates
    |
    */

    'events' => [
        'enabled' => env('ASTERISK_EVENTS_ENABLED', true),
        'queue' => env('ASTERISK_EVENTS_QUEUE', 'asterisk-events'),
        'broadcast_channel' => env('ASTERISK_EVENTS_CHANNEL', 'asterisk-events'),
        'heartbeat_interval' => env('ASTERISK_HEARTBEAT_INTERVAL', 30), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Asterisk Call Recording Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for call recording features
    |
    */

    'recording' => [
        'enabled' => env('ASTERISK_RECORDING_ENABLED', true),
        'format' => env('ASTERISK_RECORDING_FORMAT', 'wav'),
        'directory' => env('ASTERISK_RECORDING_DIR', '/var/spool/asterisk/monitor'),
        'max_duration' => env('ASTERISK_MAX_RECORDING_DURATION', 3600), // seconds
        'max_silence' => env('ASTERISK_MAX_RECORDING_SILENCE', 30), // seconds
    ],
];