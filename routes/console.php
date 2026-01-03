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

// Poll iThenticate for plagiarism results every 2 minutes
Schedule::command('plagiarism:poll')
    ->everyTwoMinutes()
    ->withoutOverlapping();

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

// Sync Kubuku E-Books daily at 2:00 AM (low traffic as recommended by Kubuku)
// Schedule is controlled by kubuku_sync_schedule setting
Schedule::command('kubuku:sync')
    ->dailyAt('02:00')
    ->when(function () {
        $schedule = \App\Models\Setting::get('kubuku_sync_schedule', 'daily');
        $enabled = \App\Models\Setting::get('kubuku_enabled', false);
        
        if (!$enabled || $schedule === 'disabled') {
            return false;
        }
        
        // For weekly, only run on Saturday
        if ($schedule === 'weekly') {
            return now()->isSaturday();
        }
        
        return true; // daily
    })
    ->withoutOverlapping()
    ->runInBackground();


// Cleanup old voice notes (older than 5 months) daily at 4:00 AM
Schedule::command('voice:cleanup')->dailyAt('04:00');

// Send loan due reminders daily at 8:00 AM
Schedule::command('loans:send-reminders')->dailyAt('08:00');

// Expire reservations that weren't picked up (every hour)
Schedule::command('reservations:expire')->hourly();
