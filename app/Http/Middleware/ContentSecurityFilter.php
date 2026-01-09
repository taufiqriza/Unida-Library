<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Heyitsmi\ContentGuard\Facades\ContentGuard as ContentGuardFacade;

/**
 * Content Security Filter Middleware
 * 
 * Integrates ContentGuard with existing security system
 * for comprehensive content filtering and logging
 */
class ContentSecurityFilter
{
    protected array $protectedFields = [
        'message', 'content', 'description', 'comment', 'review', 
        'bio', 'notes', 'title', 'subject', 'feedback', 'text'
    ];

    protected array $exemptRoutes = [
        'staff.security.*', 'api.ddc.*', 'filament.*'
    ];

    public function handle(Request $request, Closure $next)
    {
        // Skip if content filtering is disabled
        if (!config('security.content_filter_enabled', true)) {
            return $next($request);
        }

        // Skip for exempt routes
        if ($this->isExemptRoute($request)) {
            return $next($request);
        }

        // Skip for non-content requests
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return $next($request);
        }

        // Check all input fields for violations
        $violations = $this->scanRequestContent($request);
        
        if (!empty($violations)) {
            // Log security event
            Log::channel('security')->warning('Content filter violation', [
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
                'route' => $request->route()?->getName(),
                'violations' => $violations,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
            ]);

            // Return appropriate response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Konten mengandung kata-kata yang tidak pantas',
                    'message' => 'Silakan periksa kembali input Anda',
                    'violations' => count($violations)
                ], 422);
            }

            return back()->withErrors([
                'content_filter' => 'Konten mengandung kata-kata yang tidak pantas. Silakan periksa kembali input Anda.'
            ])->withInput();
        }

        return $next($request);
    }

    protected function isExemptRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();
        if (!$routeName) return false;

        foreach ($this->exemptRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }
        return false;
    }

    protected function scanRequestContent(Request $request): array
    {
        $violations = [];
        $allInput = $request->all();

        foreach ($allInput as $key => $value) {
            if (is_string($value) && $this->shouldScanField($key) && strlen($value) > 3) {
                try {
                    if (ContentGuardFacade::hasBadWords($value)) {
                        $violations[] = [
                            'field' => $key,
                            'length' => strlen($value),
                            'sample' => substr($value, 0, 50) . (strlen($value) > 50 ? '...' : ''),
                            'cleaned_sample' => substr(ContentGuardFacade::sanitize($value), 0, 50)
                        ];
                    }
                } catch (\Exception $e) {
                    // Log ContentGuard errors but don't block request
                    Log::warning('ContentGuard error', [
                        'field' => $key,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $violations;
    }

    protected function shouldScanField(string $fieldName): bool
    {
        $fieldLower = strtolower($fieldName);
        
        foreach ($this->protectedFields as $field) {
            if (str_contains($fieldLower, $field)) {
                return true;
            }
        }
        
        return false;
    }
}
