<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Marketplace\CarListingController;
use App\Http\Controllers\Marketplace\TestDriveController;
use App\Http\Controllers\Marketplace\OfferController;
use App\Http\Controllers\Marketplace\InspectionController;

/*
|--------------------------------------------------------------------------
| Marketplace Routes
|--------------------------------------------------------------------------
| Public-facing car marketplace.
*/

// Public listings (no auth required)
Route::get('/marketplace', [CarListingController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/search', [CarListingController::class, 'search'])->name('marketplace.search');
Route::get('/marketplace/{car:slug}', [CarListingController::class, 'show'])->name('marketplace.show');
Route::get('/marketplace/{car:slug}/inspection', [InspectionController::class, 'show'])->name('marketplace.inspection');

// Authenticated marketplace actions
Route::middleware('auth')->group(function () {
    // Test Drive Booking
    Route::get('/marketplace/{car:slug}/test-drive', [TestDriveController::class, 'create'])
        ->name('marketplace.test-drive.create');
    Route::post('/marketplace/{car:slug}/test-drive', [TestDriveController::class, 'store'])
        ->name('marketplace.test-drive.store');

    // Offers / Negotiation
    Route::post('/marketplace/{car:slug}/offer', [OfferController::class, 'store'])
        ->name('marketplace.offer.store');
    Route::get('/marketplace/offers', [OfferController::class, 'index'])
        ->name('marketplace.offer.index');
    Route::patch('/marketplace/offers/{offer}', [OfferController::class, 'update'])
        ->name('marketplace.offer.update');
    Route::delete('/marketplace/offers/{offer}', [OfferController::class, 'destroy'])
        ->name('marketplace.offer.destroy');

    // Sell car to dealer
    Route::get('/sell', [CarListingController::class, 'sellCreate'])->name('marketplace.sell.create');
    Route::post('/sell', [CarListingController::class, 'sellStore'])->name('marketplace.sell.store');
});