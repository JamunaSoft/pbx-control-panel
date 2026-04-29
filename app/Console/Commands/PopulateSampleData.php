<?php

namespace App\Console\Commands;

use App\Models\CallQueue;
use App\Models\CallRoute;
use App\Models\ConferenceRoom;
use App\Models\Extension;
use App\Models\Ivr;
use App\Models\Trunk;
use App\Models\User;
use App\Models\Voicemail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PopulateSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:populate-sample-data {--force : Force overwrite existing data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the PBX database with sample data for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->option('force') && $this->dataExists()) {
            if (! $this->confirm('Sample data already exists. Continue anyway? This may create duplicates.')) {
                return;
            }
        }

        $this->info('Populating PBX database with sample data...');

        $this->createUsers();
        $this->createExtensions();
        $this->createTrunks();
        $this->createQueues();
        $this->createIvrs();
        $this->createCallRoutes();
        $this->createConferenceRooms();
        $this->createVoicemails();

        $this->info('Sample data populated successfully!');
        $this->info('You can now log in with:');
        $this->info('Email: admin@pbx.local');
        $this->info('Password: password');
    }

    private function dataExists()
    {
        return Extension::exists() || Trunk::exists() || CallQueue::exists();
    }

    private function createUsers()
    {
        $this->info('Creating roles...');

        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        $this->info('Creating users...');

        User::firstOrCreate(
            ['email' => 'admin@pbx.local'],
            [
                'name' => 'PBX Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->assignRole('admin');

        User::firstOrCreate(
            ['email' => 'user@pbx.local'],
            [
                'name' => 'PBX User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->assignRole('user');
    }

    private function createExtensions()
    {
        $this->info('Creating extensions...');

        $extensions = [
            [
                'extension_number' => '1001',
                'password' => 'password123',
                'display_name' => 'John Doe',
                'email' => 'john@company.com',
                'device_type' => 'sip',
                'context' => 'default',
                'status' => 'offline',
                'voicemail_enabled' => true,
                'call_forwarding_enabled' => false,
            ],
            [
                'extension_number' => '1002',
                'password' => 'password123',
                'display_name' => 'Jane Smith',
                'email' => 'jane@company.com',
                'device_type' => 'sip',
                'context' => 'default',
                'status' => 'offline',
                'voicemail_enabled' => true,
                'call_forwarding_enabled' => false,
            ],
            [
                'extension_number' => '1003',
                'password' => 'password123',
                'display_name' => 'Bob Johnson',
                'email' => 'bob@company.com',
                'device_type' => 'sip',
                'context' => 'default',
                'status' => 'offline',
                'voicemail_enabled' => true,
                'call_forwarding_enabled' => false,
            ],
            [
                'extension_number' => '1004',
                'password' => 'password123',
                'display_name' => 'Alice Brown',
                'email' => 'alice@company.com',
                'device_type' => 'sip',
                'context' => 'default',
                'status' => 'offline',
                'voicemail_enabled' => true,
                'call_forwarding_enabled' => false,
            ],
            [
                'extension_number' => '1005',
                'password' => 'password123',
                'display_name' => 'Charlie Wilson',
                'email' => 'charlie@company.com',
                'device_type' => 'sip',
                'context' => 'default',
                'status' => 'offline',
                'voicemail_enabled' => true,
                'call_forwarding_enabled' => false,
            ],
        ];

        foreach ($extensions as $extension) {
            Extension::firstOrCreate(
                ['extension_number' => $extension['extension_number']],
                $extension
            );
        }
    }

    private function createTrunks()
    {
        $this->info('Creating trunks...');

        $trunks = [
            [
                'trunk_name' => 'voip-provider-1',
                'provider' => 'VoIP Provider Inc',
                'host' => 'sip.provider1.com',
                'username' => 'company123',
                'secret' => 'secret123',
                'context' => 'from-trunk',
                'status' => 'active',
                'type' => 'sip',
                'port' => 5060,
                'cost_per_minute' => 0.02,
            ],
            [
                'trunk_name' => 'backup-provider',
                'provider' => 'Backup VoIP',
                'host' => 'sip.backup.com',
                'username' => 'company456',
                'secret' => 'secret456',
                'context' => 'from-trunk',
                'status' => 'active',
                'type' => 'sip',
                'port' => 5060,
                'cost_per_minute' => 0.03,
            ],
        ];

        foreach ($trunks as $trunk) {
            Trunk::firstOrCreate(
                ['trunk_name' => $trunk['trunk_name']],
                $trunk
            );
        }
    }

    private function createQueues()
    {
        $this->info('Creating call queues...');

        $queues = [
            [
                'queue_name' => 'support',
                'strategy' => 'ringall',
                'timeout' => 15,
                'wrapuptime' => 5,
                'maxlen' => 10,
                'announce' => 'queue-support',
                'context' => 'default',
                'enabled' => true,
                'servicelevel' => 30,
                'musicclass' => 'default',
            ],
            [
                'queue_name' => 'sales',
                'strategy' => 'fewestcalls',
                'timeout' => 20,
                'wrapuptime' => 10,
                'maxlen' => 5,
                'announce' => 'queue-sales',
                'context' => 'default',
                'enabled' => true,
                'servicelevel' => 60,
                'musicclass' => 'default',
            ],
        ];

        foreach ($queues as $queue) {
            $queueModel = CallQueue::firstOrCreate(
                ['queue_name' => $queue['queue_name']],
                $queue
            );

            // Add some extensions to queues
            if ($queue['queue_name'] === 'support') {
                $extensions = Extension::whereIn('extension_number', ['1001', '1002'])->get();
                if ($extensions->isNotEmpty()) {
                    $queueModel->extensions()->sync($extensions->pluck('id')->mapWithKeys(fn ($id) => [$id => ['penalty' => 1]]));
                }
            } elseif ($queue['queue_name'] === 'sales') {
                $extensions = Extension::whereIn('extension_number', ['1003', '1004'])->get();
                if ($extensions->isNotEmpty()) {
                    $queueModel->extensions()->sync($extensions->pluck('id')->mapWithKeys(fn ($id) => [$id => ['penalty' => 1]]));
                }
            }
        }
    }

    private function createIvrs()
    {
        $this->info('Creating IVRs...');

        $ivrs = [
            [
                'name' => 'main-menu',
                'greeting_audio' => 'ivr-main-greeting',
                'timeout_action' => 'repeat',
                'timeout_seconds' => 10,
                'menu_options' => [
                    ['key' => '1', 'action' => 'extension', 'destination' => '1001'],
                    ['key' => '2', 'action' => 'queue', 'destination' => 'support'],
                    ['key' => '3', 'action' => 'queue', 'destination' => 'sales'],
                    ['key' => '0', 'action' => 'extension', 'destination' => '1005'],
                ],
                'invalid_input_action' => 'repeat',
                'max_attempts' => 3,
                'enabled' => true,
            ],
        ];

        foreach ($ivrs as $ivr) {
            Ivr::firstOrCreate(
                ['name' => $ivr['name']],
                $ivr
            );
        }
    }

    private function createCallRoutes()
    {
        $this->info('Creating call routes...');

        $routes = [
            [
                'name' => 'Main Inbound Route',
                'pattern' => '_X.',
                'destination_type' => 'ivr',
                'destination_value' => 'main-menu',
                'priority' => 1,
                'context' => 'default',
                'enabled' => true,
            ],
            [
                'name' => 'Emergency Route',
                'pattern' => '911',
                'destination_type' => 'trunk',
                'destination_value' => 'voip-provider-1',
                'priority' => 1,
                'context' => 'default',
                'enabled' => true,
            ],
            [
                'name' => 'International Route',
                'pattern' => '011.',
                'destination_type' => 'trunk',
                'destination_value' => 'voip-provider-1',
                'priority' => 1,
                'context' => 'default',
                'enabled' => true,
            ],
        ];

        foreach ($routes as $route) {
            CallRoute::firstOrCreate(
                ['name' => $route['name']],
                $route
            );
        }
    }

    private function createConferenceRooms()
    {
        $this->info('Creating conference rooms...');

        $rooms = [
            [
                'room_number' => '3001',
                'name' => 'Main Conference Room',
                'pin' => '1234',
                'max_participants' => 10,
                'recording_enabled' => true,
                'wait_for_moderator' => false,
                'moderator_pin' => '9999',
                'mute_on_join' => false,
                'enabled' => true,
            ],
            [
                'room_number' => '3002',
                'name' => 'Training Room',
                'pin' => '5678',
                'max_participants' => 20,
                'recording_enabled' => false,
                'wait_for_moderator' => true,
                'moderator_pin' => '8888',
                'mute_on_join' => true,
                'enabled' => true,
            ],
        ];

        foreach ($rooms as $room) {
            ConferenceRoom::firstOrCreate(
                ['room_number' => $room['room_number']],
                $room
            );
        }
    }

    private function createVoicemails()
    {
        $this->info('Creating voicemails...');

        $voicemails = [
            [
                'mailbox' => '1001',
                'context' => 'default',
                'password' => '1234',
                'fullname' => 'John Doe',
                'email' => 'john@company.com',
                'email_notification' => true,
                'language' => 'en',
                'timezone' => 'America/New_York',
                'enabled' => true,
            ],
            [
                'mailbox' => '1002',
                'context' => 'default',
                'password' => '1234',
                'fullname' => 'Jane Smith',
                'email' => 'jane@company.com',
                'email_notification' => true,
                'language' => 'en',
                'timezone' => 'America/New_York',
                'enabled' => true,
            ],
            [
                'mailbox' => '1003',
                'context' => 'default',
                'password' => '1234',
                'fullname' => 'Bob Johnson',
                'email' => 'bob@company.com',
                'email_notification' => true,
                'language' => 'en',
                'timezone' => 'America/New_York',
                'enabled' => true,
            ],
        ];

        foreach ($voicemails as $voicemail) {
            Voicemail::firstOrCreate(
                ['mailbox' => $voicemail['mailbox']],
                $voicemail
            );
        }
    }
}
