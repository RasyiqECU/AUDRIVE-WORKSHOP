<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SparePart\CatalogController;
use App\Http\Controllers\SparePart\CartController;
use App\Http\Controllers\SparePart\CheckoutController;
use App\Http\Controllers\SparePart\OrderController;
use App\Http\Controllers\SparePart\QuoteRequestController;

/*
|--------------------------------------------------------------------------
| Spare Parts Routes
|--------------------------------------------------------------------------
| E-commerce untuk spare part.
*/

// Public catalog
Route::get('/spare-parts', [CatalogController::class, 'index'])->name('parts.index');
Route::get('/spare-parts/search', [CatalogController::class, 'search'])->name('parts.search');
Route::get('/spare-parts/categories/{category:slug}', [CatalogController::class, 'byCategory'])->name('parts.category');
Route::get('/spare-parts/{sparePart:slug}', [CatalogController::class, 'show'])->name('parts.show');
Route::get('/spare-parts/{sparePart:slug}/compatibility', [CatalogController::class, 'compatibility'])->name('parts.compatibility');

// Authenticated shopping
Route::middleware('auth')->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('parts.cart');
    Route::post('/cart', [CartController::class, 'store'])->name('parts.cart.store');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('parts.cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('parts.cart.destroy');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('parts.checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('parts.checkout.store');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'payment'])->name('parts.checkout.payment');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('parts.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('parts.orders.show');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('parts.orders.invoice');

    // Quote Request (for rare parts)
    Route::get('/quote-request', [QuoteRequestController::class, 'create'])->name('parts.quote.create');
    Route::post('/quote-request', [QuoteRequestController::class, 'store'])->name('parts.quote.store');
});