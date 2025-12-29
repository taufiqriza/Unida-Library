<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentFilter
{
    protected array $blockedPatterns = [
        // Gambling/Judol keywords
        'slot\s*gacor', 'slot\s*online', 'judi\s*online', 'judi\s*bola',
        'togel', 'poker\s*online', 'casino\s*online', 'sbobet', 'maxbet',
        'taruhan', 'bandar\s*bola', 'agen\s*judi', 'daftar\s*slot',
        'rtp\s*slot', 'bocoran\s*slot', 'pola\s*slot', 'scatter',
        'pragmatic\s*play', 'pg\s*soft', 'habanero', 'joker123',
        'deposit\s*pulsa', 'bonus\s*new\s*member', 'freebet',
        'livechat\s*24\s*jam', 'withdraw\s*cepat', 'jackpot',
        
        // Suspicious URLs
        'bit\.ly', 'tinyurl', 's\.id', 'cutt\.ly',
        
        // SQL Injection patterns
        'union\s+select', 'drop\s+table', 'insert\s+into.*values',
        '\/\*.*\*\/', '--\s*$', 'xp_cmdshell', 'exec\s*\(',
        
        // XSS patterns
        '<script', 'javascript:', 'onerror\s*=', 'onload\s*=',
        'onclick\s*=', 'onmouseover\s*=', 'onfocus\s*=',
    ];

    public function handle(Request $request, Closure $next)
    {
        if ($this->containsBlockedContent($request)) {
            \Log::warning('Blocked suspicious content', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
            ]);
            
            abort(403, 'Request blocked due to suspicious content.');
        }

        return $next($request);
    }

    protected function containsBlockedContent(Request $request): bool
    {
        $inputs = $request->except(['_token', '_method', 'password', 'password_confirmation']);
        $content = $this->flattenArray($inputs);
        
        foreach ($content as $value) {
            if (!is_string($value)) continue;
            
            foreach ($this->blockedPatterns as $pattern) {
                if (preg_match('/' . $pattern . '/i', $value)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $prefix . $key . '.'));
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }
}
