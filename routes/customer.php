<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\VehicleController;
use App\Http\Controllers\Customer\TicketController;
use App\Http\Controllers\Customer\LoyaltyController;
use App\Http\Controllers\Customer\ReminderController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\VoucherController;
use App\Http\Controllers\Customer\AiScanController;

/*
|--------------------------------------------------------------------------
| Customer Portal Routes
|--------------------------------------------------------------------------
| Prefix: /customer
| Middleware: auth, verified, role:customer
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// AI Sound Scan
Route::resource('ai-scan', AiScanController::class)->only(['index', 'create', 'store', 'show']);

// Vehicles
Route::resource('vehicles', VehicleController::class);

// Support Tickets
Route::resource('tickets', TicketController::class);
Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

// Loyalty Program
Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
Route::get('loyalty-redirect', [LoyaltyController::class, 'index'])->name('loyalty');
Route::get('loyalty/history', [LoyaltyController::class, 'history'])->name('loyalty.history');
Route::post('loyalty/redeem', [LoyaltyController::class, 'redeem'])->name('loyalty.redeem');

// Reminders
Route::get('reminders', [ReminderController::class, 'index'])->name('reminders.index');
Route::patch('reminders/{reminder}/dismiss', [ReminderController::class, 'dismiss'])->name('reminders.dismiss');

// Reviews & Ratings
Route::get('reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');

// Vouchers & Promos
Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
Route::post('vouchers/{voucher}/claim', [VoucherController::class, 'claim'])->name('vouchers.claim');
Route::get('promotions', [VoucherController::class, 'promotions'])->name('promotions.index');

// Notifications
Route::get('notifications', [DashboardController::class, 'notifications'])->name('notifications.index');
Route::patch('notifications/{notification}/read', [DashboardController::class, 'markRead'])->name('notifications.read');
Route::post('notifications/read-all', [DashboardController::class, 'markAllRead'])->name('notifications.read-all');