<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('staff')->check()) {
            $user = auth('staff')->user();
            // Update setiap 1 menit untuk mengurangi query
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 1) {
                $user->update([
                    'is_online' => true,
                    'last_seen_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
