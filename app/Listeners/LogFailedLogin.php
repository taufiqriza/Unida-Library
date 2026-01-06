<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $ip = request()->ip();
        $email = $event->credentials['email'] ?? 'unknown';
        $guard = $event->guard;
        $today = now()->format('Y-m-d');

        // Increment daily counter
        Cache::increment("failed_login_count_{$today}");
        
        // Track by IP (for rate limiting detection)
        $ipKey = "failed_login_ip_{$ip}_{$today}";
        Cache::increment($ipKey);
        Cache::put($ipKey, Cache::get($ipKey, 1), now()->endOfDay());

        // Log the attempt
        Log::warning('Failed login attempt', [
            'ip' => $ip,
            'email' => $email,
            'guard' => $guard,
            'user_agent' => request()->userAgent(),
        ]);
    }
}
