<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $key = $request->user() ? $request->user()->id : $request->ip();

        if (RateLimiter::tooManyAttempts($key, 60)) { // 60 requests per minute
            return response()->json([
                'error' => 'Too many requests. Please try again later.'
            ], 429)->header('Retry-After', RateLimiter::availableIn($key));
        }

        RateLimiter::hit($key);

        return $next($request);
    }
}