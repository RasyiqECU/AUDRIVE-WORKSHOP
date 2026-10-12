<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Workshop\ServiceBookingController;
use App\Http\Controllers\Workshop\WorkOrderTrackingController;

/*
|--------------------------------------------------------------------------
| Workshop Routes
|--------------------------------------------------------------------------
| Customer-facing workshop / bengkel booking & tracking.
*/

// Public workshop info
Route::get('/workshop', [ServiceBookingController::class, 'publicIndex'])
    ->name('workshop.index');

// Authenticated workshop actions
Route::middleware('auth')->group(function () {
    // Service Booking
    Route::get('/workshop/book', [ServiceBookingController::class, 'create'])
        ->name('workshop.booking.create');
    Route::post('/workshop/book', [ServiceBookingController::class, 'store'])
        ->name('workshop.booking.store');
    Route::get('/workshop/my-bookings', [ServiceBookingController::class, 'myBookings'])
        ->name('workshop.booking.index');
    Route::get('/workshop/bookings/{booking}', [ServiceBookingController::class, 'show'])
        ->name('workshop.booking.show');
    Route::get('/workshop/bookings/{booking}/receipt', [ServiceBookingController::class, 'receipt'])
        ->name('workshop.booking.receipt');

    // Work Order Tracking (customer view)
    Route::get('/workshop/tracking/{workOrder}', [WorkOrderTrackingController::class, 'show'])
        ->name('workshop.tracking.show');
    Route::get('/workshop/tracking/{workOrder}/timeline', [WorkOrderTrackingController::class, 'timeline'])
        ->name('workshop.tracking.timeline');

    // Service History
    Route::get('/workshop/history', [ServiceBookingController::class, 'history'])
        ->name('workshop.history');

    // AI Sound Scan (customer initiated)
    Route::get('/workshop/ai-scan', [\App\Http\Controllers\Workshop\AiScanController::class, 'create'])
        ->name('workshop.ai-scan.create');
    Route::post('/workshop/ai-scan', [\App\Http\Controllers\Workshop\AiScanController::class, 'store'])
        ->name('workshop.ai-scan.store');
    Route::get('/workshop/ai-scan/{scan}', [\App\Http\Controllers\Workshop\AiScanController::class, 'show'])
        ->name('workshop.ai-scan.show');
});