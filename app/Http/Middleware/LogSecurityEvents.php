<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogSecurityEvents
{
    protected array $sensitiveRoutes = [
        'login', 'register', 'password', 'admin', 'staff/control'
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log failed authentication attempts
        if ($response->getStatusCode() === 401 || $response->getStatusCode() === 403) {
            Log::channel('security')->warning('Access denied', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'user_id' => $request->user()?->id,
            ]);
        }

        // Log suspicious activity on sensitive routes
        if ($this->isSensitiveRoute($request) && $response->getStatusCode() >= 400) {
            Log::channel('security')->warning('Sensitive route error', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'status' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }

    protected function isSensitiveRoute(Request $request): bool
    {
        foreach ($this->sensitiveRoutes as $route) {
            if (str_contains($request->path(), $route)) {
                return true;
            }
        }
        return false;
    }
}
