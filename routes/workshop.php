<?php

use App\Http\Controllers\Workshop\AiScanController;
use App\Http\Controllers\Workshop\ServiceBookingController;
use App\Http\Controllers\Workshop\WorkOrderTrackingController;
use Illuminate\Support\Facades\Route;

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
    Route::middleware('role:customer')->group(function () {
        // Service Booking
        Route::get('/workshop/book', [ServiceBookingController::class, 'create'])
            ->name('workshop.booking.create');
        Route::get('/workshop/booking', [ServiceBookingController::class, 'create'])
            ->name('workshop.booking');
        Route::post('/workshop/book', [ServiceBookingController::class, 'store'])
            ->name('workshop.booking.store');
        Route::get('/workshop/my-bookings', [ServiceBookingController::class, 'myBookings'])
            ->name('workshop.booking.index');
        Route::get('/workshop/bookings/{booking}', [ServiceBookingController::class, 'show'])
            ->name('workshop.booking.show');
        Route::get('/workshop/bookings/{booking}/receipt', [ServiceBookingController::class, 'receipt'])
            ->name('workshop.booking.receipt');

        // Service History
        Route::get('/workshop/history', [ServiceBookingController::class, 'history'])
            ->name('workshop.history');

        // AI Sound Scan (customer initiated)
        Route::get('/workshop/ai-scan', [AiScanController::class, 'create'])
            ->name('workshop.ai-scan.create');
        Route::post('/workshop/ai-scan', [AiScanController::class, 'store'])
            ->name('workshop.ai-scan.store');
        Route::get('/workshop/ai-scan/{aiScan}', [AiScanController::class, 'show'])
            ->name('workshop.ai-scan.show');
    });

    // Work Order Tracking (customer view)
    Route::get('/workshop/tracking/{workOrder}', [WorkOrderTrackingController::class, 'show'])
        ->name('workshop.tracking.show');
    Route::post('/workshop/tracking/parts/{item}', [WorkOrderTrackingController::class, 'decidePart'])
        ->name('workshop.tracking.parts.decide');
    Route::post('/workshop/tracking/{workOrder}/pay-online', [WorkOrderTrackingController::class, 'payOnline'])
        ->name('workshop.tracking.pay-online');
    Route::get('/workshop/tracking/{workOrder}/timeline', [WorkOrderTrackingController::class, 'timeline'])
        ->name('workshop.tracking.timeline');
});
