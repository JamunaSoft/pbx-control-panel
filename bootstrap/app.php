<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function ($schedule) {
        // Add scheduled tasks here if needed
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // API middleware removed since we're not using API routes initially
        // $middleware->alias([
        //     'rate.limit.api' => \App\Http\Middleware\RateLimitApi::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
