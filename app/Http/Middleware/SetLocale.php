<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Available locales
     */
    protected array $availableLocales = ['id', 'en', 'ar'];
    
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority order for locale detection:
        // 1. URL segment (/en/, /ar/)
        // 2. Session
        // 3. Cookie
        // 4. Browser preference
        // 5. Default (id)
        
        $locale = $this->detectLocale($request);
        
        // Validate locale
        if (!in_array($locale, $this->availableLocales)) {
            $locale = config('app.locale', 'id');
        }
        
        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        // Set text direction for Arabic
        $direction = $locale === 'ar' ? 'rtl' : 'ltr';
        view()->share('locale', $locale);
        view()->share('textDirection', $direction);
        view()->share('availableLocales', $this->getLocaleOptions());
        
        return $next($request);
    }
    
    /**
     * Detect locale from various sources
     */
    protected function detectLocale(Request $request): string
    {
        // 1. Check URL segment
        $segment = $request->segment(1);
        if (in_array($segment, ['en', 'ar'])) {
            return $segment;
        }
        
        // 2. Check query parameter (for switching)
        if ($request->has('lang') && in_array($request->get('lang'), $this->availableLocales)) {
            return $request->get('lang');
        }
        
        // 3. Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }
        
        // 4. Check cookie
        if ($request->cookie('locale') && in_array($request->cookie('locale'), $this->availableLocales)) {
            return $request->cookie('locale');
        }
        
        // 5. Check browser preference
        $browserLocale = $request->getPreferredLanguage($this->availableLocales);
        if ($browserLocale) {
            return $browserLocale;
        }
        
        // 6. Default
        return config('app.locale', 'id');
    }
    
    /**
     * Get locale options for switcher
     */
    protected function getLocaleOptions(): array
    {
        return [
            'id' => [
                'name' => 'Indonesia',
                'native' => 'Bahasa Indonesia',
                'flag' => '🇮🇩',
                'dir' => 'ltr',
            ],
            'en' => [
                'name' => 'English',
                'native' => 'English',
                'flag' => '🇬🇧',
                'dir' => 'ltr',
            ],
            'ar' => [
                'name' => 'Arabic',
                'native' => 'العربية',
                'flag' => '🇸🇦',
                'dir' => 'rtl',
            ],
        ];
    }
}
