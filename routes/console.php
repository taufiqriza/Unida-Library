<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate scheduled tasks daily at 8:00 AM
Schedule::command('tasks:generate')->dailyAt('08:00');

// Process queue jobs every 5 minutes (cron-based, resource efficient)
Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=600 --memory=128')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Sync journal articles daily at 3:00 AM (Atom feed - artikel terbaru)
Schedule::command('journals:sync')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Full scrape weekly on Sunday at 2:00 AM (semua artikel dari archive)
Schedule::command('journals:scrape')
    ->weeklyOn(0, '02:00')
    ->withoutOverlapping()
    ->runInBackground();

// Sync from UNIDA Repository weekly on Saturday at 2:00 AM
Schedule::command('repo:sync')
    ->weeklyOn(6, '02:00')
    ->withoutOverlapping()
    ->runInBackground();
