<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Auth\Events\Failed;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Rate Limiters
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function () {
                Log::warning('Rate limit exceeded for login', ['ip' => request()->ip()]);
                return response('Terlalu banyak percobaan login. Coba lagi dalam 1 menit.', 429);
            });
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('file-upload', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('export', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())->response(function () {
                return back()->with('error', 'Terlalu banyak permintaan export. Coba lagi dalam 1 menit.');
            });
        });

        // Gates
        Gate::define('manage-staff', function ($user) {
            return in_array($user->role, ['super_admin', 'admin']);
        });

        // Register Observers
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);

        // Register Event Listeners
        Event::listen(Failed::class, \App\Listeners\LogFailedLogin::class);

        // Load mail config from database
        $this->configureMailFromDatabase();
    }

    protected function configureMailFromDatabase(): void
    {
        try {
            if (!Schema::hasTable('settings')) return;

            $mailMailer = Setting::get('mail_mailer');
            
            // If using Resend from .env, don't override
            if (env('MAIL_MAILER') === 'resend') {
                config(['mail.default' => 'resend']);
                return;
            }
            
            if (!$mailMailer) return;

            $mailHost = Setting::get('mail_host');
            if (!$mailHost) return;

            config([
                'mail.default' => $mailMailer,
                'mail.mailers.smtp.host' => $mailHost,
                'mail.mailers.smtp.port' => Setting::get('mail_port', 587),
                'mail.mailers.smtp.username' => Setting::get('mail_username'),
                'mail.mailers.smtp.password' => Setting::get('mail_password'),
                'mail.mailers.smtp.encryption' => Setting::get('mail_encryption', 'tls'),
                'mail.from.address' => Setting::get('mail_from_address'),
                'mail.from.name' => Setting::get('mail_from_name', 'UNIDA Library'),
            ]);
        } catch (\Exception $e) {
            // Ignore if database not ready
        }
    }
}
