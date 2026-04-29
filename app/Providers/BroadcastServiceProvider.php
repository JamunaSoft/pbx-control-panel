<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load channel authorizations from routes/channels.php
        if (file_exists($path = $this->app->basePath('routes/channels.php'))) {
            require $path;
        }
    }
}
