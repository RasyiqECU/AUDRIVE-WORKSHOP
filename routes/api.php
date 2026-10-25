<?php


use App\Http\Controllers\Api\V1\AiScanController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\MarketplaceController;
use App\Http\Controllers\Api\V1\SparePartController;
use App\Http\Controllers\Api\V1\WorkshopController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| Base path: /api/v1
| Authentication: Laravel Sanctum token
|
*/


Route::prefix('v1')->name('api.v1.')
    ->middleware('throttle:30,1')
    ->group(function () {


    // ── Public Routes ──
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is healthy',
            'data' => [
                'version' => '1.0.0',
                'timestamp' => now()->toISOString(),
            ],
        ]);
    })->name('health');


    // ── Auth Routes (Public) ──
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');


    // ── Marketplace Routes (Public) ──
    Route::get('/cars', [MarketplaceController::class, 'cars'])->name('cars.index');
    Route::get('/cars/{id}', [MarketplaceController::class, 'carDetail'])->name('cars.show');


    // ── Spare Parts Routes (Public) ──
    Route::get('/spare-parts', [SparePartController::class, 'index'])->name('spare-parts.index');
    Route::get('/spare-parts/{id}', [SparePartController::class, 'show'])->name('spare-parts.show');
    Route::get('/spare-part-categories', [SparePartController::class, 'categories'])->name('spare-parts.categories');


    // ── Protected Routes (Auth Required) ──
    Route::middleware('auth:sanctum')->group(function () {


        // Auth
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');


        // Marketplace
        Route::post('/test-drives', [MarketplaceController::class, 'storeTestDrive'])->name('test-drives.store');
        Route::post('/offers', [MarketplaceController::class, 'storeOffer'])->name('offers.store');
        Route::get('/offers', [MarketplaceController::class, 'offers'])->name('offers.index');


        // Workshop
        Route::get('/service-bookings', [WorkshopController::class, 'serviceBookings'])->name('service-bookings.index');
        Route::post('/service-bookings', [WorkshopController::class, 'storeServiceBooking'])->name('service-bookings.store');
        Route::get('/service-bookings/{id}', [WorkshopController::class, 'serviceBookingDetail'])->name('service-bookings.show');
        Route::get('/work-orders/{id}', [WorkshopController::class, 'workOrderDetail'])->name('work-orders.show');


        // AI Scans
        Route::get('/ai-scans', [AiScanController::class, 'index'])->name('ai-scans.index');
        Route::post('/ai-scans', [AiScanController::class, 'store'])->name('ai-scans.store');
        Route::get('/ai-scans/{id}', [AiScanController::class, 'show'])->name('ai-scans.show');


        // Cart
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/items', [CartController::class, 'storeItem'])->name('cart.items.store');
        Route::put('/cart/items/{id}', [CartController::class, 'updateItem'])->name('cart.items.update');
        Route::delete('/cart/items/{id}', [CartController::class, 'destroyItem'])->name('cart.items.destroy');
        Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');


        // Customer
        Route::prefix('customer')->name('customer.')->group(function () {
            Route::get('/vehicles', [CustomerController::class, 'vehicles'])->name('vehicles.index');
            Route::post('/vehicles', [CustomerController::class, 'storeVehicle'])->name('vehicles.store');
            Route::get('/loyalty', [CustomerController::class, 'loyalty'])->name('loyalty');
        });
    });
});



