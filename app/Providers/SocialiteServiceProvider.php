<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class SocialiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            if (Setting::get('google_enabled')) {
                config([
                    'services.google.client_id' => Setting::get('google_client_id'),
                    'services.google.client_secret' => Setting::get('google_client_secret'),
                    'services.google.redirect' => url('/auth/google/callback'),
                ]);
            }
        });
    }
}
