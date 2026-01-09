<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 * 
 * Implements comprehensive HTTP security headers to protect against
 * XSS, clickjacking, MIME sniffing, and other common attacks.
 */
class SecurityHeaders
{
    /**
     * Domains allowed in Content Security Policy
     */
    protected array $trustedDomains = [
        'self',
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
        'https://cdn.jsdelivr.net',
        'https://cdnjs.cloudflare.com',
        'https://api.qrserver.com',
        'https://accounts.google.com',
        'https://www.google.com',
        'https://www.gstatic.com',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip for file downloads and binary responses
        $contentType = $response->headers->get('Content-Type', '');
        if (str_contains($contentType, 'application/pdf') || 
            str_contains($contentType, 'application/octet-stream')) {
            return $response;
        }

        // Core Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Prevent information disclosure
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        
        // Permissions Policy (formerly Feature-Policy)
        $response->headers->set('Permissions-Policy', implode(', ', [
            'camera=(self)',
            'microphone=(self)',
            'geolocation=(self)',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()',
        ]));

        // Content Security Policy
        $csp = $this->buildContentSecurityPolicy();
        $response->headers->set('Content-Security-Policy', $csp);

        // HTTPS-only headers
        if ($request->secure() || config('app.env') === 'production') {
            // HSTS: 1 year, include subdomains, preload ready
            $response->headers->set(
                'Strict-Transport-Security', 
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Prevent caching of sensitive pages for authenticated users
        if ($request->user() || $request->user('member')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // Cross-Origin headers
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        return $response;
    }

    /**
     * Build Content Security Policy header value
     */
    protected function buildContentSecurityPolicy(): string
    {
        $directives = [
            // Default fallback
            "default-src 'self'",
            
            // Scripts: self + trusted CDNs + inline for Livewire/Alpine
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' " . 
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com " .
                "https://unpkg.com " .
                "https://www.google.com " .
                "https://www.gstatic.com " .
                "https://accounts.google.com " .
                "https://cdn.tailwindcss.com " .
                "https://www.googletagmanager.com " .
                "https://www.google-analytics.com " .
                "https://maps.googleapis.com " .
                "https://maps.gstatic.com",
            
            // Styles: self + Google Fonts + inline for Tailwind
            "style-src 'self' 'unsafe-inline' " .
                "https://fonts.googleapis.com " .
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com " .
                "https://cdn.tailwindcss.com",
            
            // Fonts
            "font-src 'self' " .
                "https://fonts.gstatic.com " .
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com " .
                "data:",
            
            // Images: allow data URIs for inline images, blob for file previews
            "img-src 'self' data: blob: https: http:",
            
            // Media: allow blob for voice note preview
            "media-src 'self' blob:",
            
            // Connect: API calls, Livewire, etc.
            "connect-src 'self' " .
                "https://api.qrserver.com " .
                "https://cdn.jsdelivr.net " .
                "https://accounts.google.com " .
                "https://www.google-analytics.com " .
                "https://region1.google-analytics.com " .
                "https://maps.googleapis.com " .
                "https://maps.gstatic.com " .
                "wss: ws:",
            
            // Frames: Google OAuth, YouTube, embedded content, blob for PDF
            "frame-src 'self' blob: " .
                "https://accounts.google.com " .
                "https://www.google.com " .
                "https://www.youtube.com " .
                "https://youtube.com",
            
            // Frame ancestors: prevent clickjacking
            "frame-ancestors 'self'",
            
            // Form submissions
            "form-action 'self' https://accounts.google.com",
            
            // Object/embed: disable Flash, etc.
            "object-src 'none'",
            
            // Base URI restriction
            "base-uri 'self'",
            
            // Upgrade insecure requests in production
            "upgrade-insecure-requests",
        ];

        return implode('; ', $directives);
    }
}
