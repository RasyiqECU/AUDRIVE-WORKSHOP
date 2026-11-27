<?php

use App\Services\OfferService;
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

// Kembalikan unit mobil ke Tersedia bila pesanan (offer) pending sudah kedaluwarsa
Schedule::call(fn () => app(OfferService::class)->checkExpiredOffers())
    ->name('offers:expire')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

// Reminder servis berkala — jalan tiap hari jam 08:00
Schedule::command('notifications:dispatch-service-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();
