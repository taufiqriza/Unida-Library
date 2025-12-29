<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class BruteForceProtection
{
    public function handle(Request $request, Closure $next, string $key = 'global', int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $rateLimitKey = $key . ':' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            
            \Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'key' => $key,
                'url' => $request->fullUrl(),
            ]);
            
            abort(429, "Too many requests. Please try again in {$seconds} seconds.");
        }
        
        RateLimiter::hit($rateLimitKey, $decayMinutes * 60);
        
        return $next($request);
    }
}
