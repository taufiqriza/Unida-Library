<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminIpWhitelist
{
    public function handle(Request $request, Closure $next)
    {
        $allowedIps = array_filter(array_map('trim', explode(',', env('ADMIN_ALLOWED_IPS', ''))));
        
        // If no IPs configured, allow all (for initial setup)
        if (empty($allowedIps)) {
            return $next($request);
        }
        
        $clientIp = $request->ip();
        
        // Check if IP is in whitelist
        foreach ($allowedIps as $allowedIp) {
            if ($this->ipMatches($clientIp, $allowedIp)) {
                return $next($request);
            }
        }
        
        \Log::warning('Admin access blocked - IP not whitelisted', [
            'ip' => $clientIp,
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
        ]);
        
        abort(403, 'Access denied.');
    }
    
    protected function ipMatches(string $ip, string $pattern): bool
    {
        // Exact match
        if ($ip === $pattern) return true;
        
        // CIDR notation (e.g., 192.168.1.0/24)
        if (str_contains($pattern, '/')) {
            [$subnet, $mask] = explode('/', $pattern);
            $mask = (int) $mask;
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            $maskLong = -1 << (32 - $mask);
            return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
        }
        
        // Wildcard (e.g., 192.168.1.*)
        if (str_contains($pattern, '*')) {
            $regex = str_replace(['.', '*'], ['\.', '\d+'], $pattern);
            return (bool) preg_match('/^' . $regex . '$/', $ip);
        }
        
        return false;
    }
}
