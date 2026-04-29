<?php

namespace App\Providers;

use App\Services\Asterisk\AsteriskService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Asterisk service as singleton
        $this->app->singleton(AsteriskService::class, function ($app) {
            return new AsteriskService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-connect to Asterisk if enabled - disabled for development to avoid bootstrap issues
        // if (config('asterisk.general.auto_connect', true)) {
        //     $this->app->make(AsteriskService::class)->connect();
        // }
    }
}
