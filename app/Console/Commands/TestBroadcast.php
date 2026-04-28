<?php

namespace App\Console\Commands;

use App\Events\ExtensionStatusUpdated;
use App\Models\Extension;
use Illuminate\Console\Command;

class TestBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:broadcast {extension_number} {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test broadcasting extension status updates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $extensionNumber = $this->argument('extension_number');
        $status = $this->argument('status');

        $extension = Extension::where('extension_number', $extensionNumber)->first();

        if (!$extension) {
            $this->error("Extension {$extensionNumber} not found");
            return;
        }

        $extension->update(['status' => $status]);

        broadcast(new ExtensionStatusUpdated($extension));

        $this->info("Broadcasted extension status update: {$extensionNumber} -> {$status}");
    }
}
}
