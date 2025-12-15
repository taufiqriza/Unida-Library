<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Gates
        Gate::define('manage-staff', function ($user) {
            return in_array($user->role, ['super_admin', 'admin']);
        });

        // Register Observers
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);

        // Load mail config from database
        $this->configureMailFromDatabase();
    }

    protected function configureMailFromDatabase(): void
    {
        try {
            if (!Schema::hasTable('settings')) return;

            $mailHost = Setting::get('mail_host');
            if (!$mailHost) return;

            config([
                'mail.default' => Setting::get('mail_mailer', 'smtp'),
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
