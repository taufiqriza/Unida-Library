<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/staff.php'));
            Route::middleware('api')->prefix('api/v1')->group(base_path('routes/api_v1.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        
        // Security middleware - applied globally
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\ContentSecurityFilter::class);
        $middleware->append(\App\Http\Middleware\BlockSuspiciousUserAgents::class);
        
        // Web group middleware
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\ContentFilter::class);
        
        // Register named middleware aliases
        $middleware->alias([
            'throttle.login' => \App\Http\Middleware\BruteForceProtection::class,
            'honeypot' => \App\Http\Middleware\HoneypotProtection::class,
            'admin.ip' => \App\Http\Middleware\AdminIpWhitelist::class,
            'content.filter' => \App\Http\Middleware\ContentFilter::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
