<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Panel IP Restriction Middleware
 * 
 * Restricts access to admin panel based on IP whitelist.
 * Can be bypassed for local development or disabled via config.
 */
class AdminIpRestriction
{
    /**
     * Check if IP restriction is enabled
     */
    protected function isEnabled(): bool
    {
        return config('security.admin_ip_restriction_enabled', true);
    }

    /**
     * Get allowed IPs from config or env
     */
    protected function getAllowedIps(): array
    {
        // Default allowed IPs (localhost)
        $defaults = [
            '127.0.0.1',
            '::1',
        ];

        // Get additional IPs from environment
        $envIps = env('ADMIN_ALLOWED_IPS', '');
        $additionalIps = $envIps ? array_map('trim', explode(',', $envIps)) : [];

        return array_merge($defaults, $additionalIps);
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Skip if restriction is disabled
        if (!$this->isEnabled()) {
            return $next($request);
        }

        // Skip in local/development environment
        if (app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        $clientIp = $request->ip();
        $allowedIps = $this->getAllowedIps();

        // Check if IP is in allowed list
        if (!in_array($clientIp, $allowedIps)) {
            // Log the unauthorized access attempt
            Log::warning('Admin panel access denied', [
                'ip' => $clientIp,
                'user_agent' => $request->userAgent(),
                'path' => $request->path(),
                'timestamp' => now()->toISOString(),
            ]);

            abort(403, 'Access denied. Your IP is not authorized.');
        }

        return $next($request);
    }
}
