<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockSuspiciousUserAgents
{
    protected array $blockedAgents = [
        'sqlmap', 'nikto', 'nmap', 'masscan', 'zgrab',
        'python-requests', 'curl/', 'wget/', 'libwww-perl',
        'scrapy', 'httpclient', 'java/', 'go-http-client',
        'ahrefsbot', 'semrushbot', 'dotbot', 'mj12bot',
        'blexbot', 'seekport', 'seznambot', 'yandexbot',
    ];
    
    protected array $blockedPatterns = [
        '/^$/i', // Empty user agent
        '/^\-$/i', // Just a dash
    ];

    public function handle(Request $request, Closure $next)
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        // Block empty user agents on non-API routes
        if (empty($userAgent) && !$request->is('api/*')) {
            $this->logAndBlock($request, 'Empty user agent');
        }
        
        // Check blocked agents
        foreach ($this->blockedAgents as $agent) {
            if (str_contains($userAgent, $agent)) {
                $this->logAndBlock($request, "Blocked agent: {$agent}");
            }
        }
        
        return $next($request);
    }
    
    protected function logAndBlock(Request $request, string $reason): void
    {
        \Log::warning('Suspicious user agent blocked', [
            'ip' => $request->ip(),
            'reason' => $reason,
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);
        
        abort(403, 'Access denied.');
    }
}
