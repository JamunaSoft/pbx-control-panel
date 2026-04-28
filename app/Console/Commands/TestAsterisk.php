<?php

namespace App\Console\Commands;

use App\Services\Asterisk\AsteriskService;
use Illuminate\Console\Command;

class TestAsterisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:asterisk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Asterisk AMI and ARI connectivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Asterisk connectivity...');

        $asterisk = app(AsteriskService::class);

        $connected = $asterisk->connect();

        if ($connected) {
            $this->info('✅ Asterisk services connected successfully');

            // Test getting system info
            $systemInfo = $asterisk->getSystemInfo();
            $this->info('System Info:');
            $this->table(
                ['Property', 'Value'],
                collect($systemInfo)->map(function($value, $key) {
                    return [$key, $value];
                })->toArray()
            );

            // Test getting extension status (if extensions exist)
            $extensions = \App\Models\Extension::all();
            if ($extensions->count() > 0) {
                $this->info('Testing extension status...');
                $firstExtension = $extensions->first();
                $status = $asterisk->getExtensionStatus($firstExtension->extension_number);
                $this->info("Extension {$firstExtension->extension_number} status: {$status}");
            }

        } else {
            $this->error('❌ Failed to connect to Asterisk services');
            $this->warn('Make sure Asterisk is running and AMI/ARI are properly configured');
        }
    }
}
}
