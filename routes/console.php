<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reminder servis berkala — jalan tiap hari jam 08:00
Schedule::command('notifications:dispatch-service-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();
