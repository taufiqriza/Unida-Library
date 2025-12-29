<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HoneypotProtection
{
    public function handle(Request $request, Closure $next)
    {
        // Check honeypot field - should be empty
        if ($request->filled('website_url') || $request->filled('fax_number')) {
            \Log::warning('Honeypot triggered - bot detected', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Silently reject - don't give feedback to bots
            return response()->json(['message' => 'Success'], 200);
        }
        
        // Check timestamp - form should take at least 2 seconds to fill
        if ($request->has('_form_time')) {
            $formTime = (int) $request->input('_form_time');
            $elapsed = time() - $formTime;
            
            if ($elapsed < 2) {
                \Log::warning('Form submitted too fast - bot detected', [
                    'ip' => $request->ip(),
                    'elapsed' => $elapsed,
                ]);
                
                return response()->json(['message' => 'Success'], 200);
            }
        }
        
        return $next($request);
    }
}
