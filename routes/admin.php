<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\ServiceBookingController;
use App\Http\Controllers\Admin\AdminWorkOrderController;
use App\Http\Controllers\Admin\AiScanReviewController;
use App\Http\Controllers\Admin\SparePartController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\LoyaltyController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Prefix: /admin
| Middleware: auth, verified, role:super-admin|manager|...
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ── Cars / Marketplace Management ──
Route::resource('cars', CarController::class);
Route::patch('cars/{car}/status', [CarController::class, 'updateStatus'])->name('cars.status');
Route::get('cars/{car}/inspection', [CarController::class, 'inspection'])->name('cars.inspection');
Route::post('cars/{car}/inspection', [CarController::class, 'storeInspection'])->name('cars.inspection.store');
Route::post('cars/{car}/featured', [CarController::class, 'setFeatured'])->name('cars.featured');
Route::delete('cars/{car}/featured', [CarController::class, 'removeFeatured'])->name('cars.featured.remove');
Route::post('cars/{car}/photos', [CarController::class, 'uploadPhotos'])->name('cars.photos.store');
Route::delete('cars/{car}/photos/{photo}', [CarController::class, 'deletePhoto'])->name('cars.photos.destroy');
Route::post('cars/{car}/photos/{photo}/primary', [CarController::class, 'setPrimaryPhoto'])->name('cars.photos.primary');
Route::post('cars/{car}/photos/{photo}/replace', [CarController::class, 'replacePhoto'])->name('cars.photos.replace');

// ── Offers / Penawaran Harga Mobil ──
Route::resource('offers', \App\Http\Controllers\Admin\OfferController::class)->only(['index', 'show']);
Route::post('offers/{offer}/accept', [\App\Http\Controllers\Admin\OfferController::class, 'accept'])->name('offers.accept');
Route::post('offers/{offer}/reject', [\App\Http\Controllers\Admin\OfferController::class, 'reject'])->name('offers.reject');

// ── Test Drive / Uji Kendaraan ──
Route::resource('appointments', AppointmentController::class)->only(['index', 'show']);
Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

// ── Point of Sales (POS / Kasir) ──
Route::middleware('role:super-admin|manager|cashier')->group(function () {
    Route::get('pos', [\App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos.index');
    Route::post('pos', [\App\Http\Controllers\Admin\PosController::class, 'store'])->name('pos.store');
    Route::post('pos/work-orders/{workOrder}/pay', [\App\Http\Controllers\Admin\PosController::class, 'payWorkOrder'])->name('pos.work-orders.pay');
    Route::post('pos/car-sales/{salesOrder}/pay', [\App\Http\Controllers\Admin\PosController::class, 'payCarSales'])->name('pos.car-sales.pay');
    Route::get('pos/search', [\App\Http\Controllers\Admin\PosController::class, 'search'])->name('pos.search');
    Route::get('pos/{salesOrder}/receipt', [\App\Http\Controllers\Admin\PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('pos/{salesOrder}/print', [\App\Http\Controllers\Admin\PosController::class, 'printReceipt'])->name('pos.print');
});

// ── Workshop ──
Route::resource('service-bookings', ServiceBookingController::class);
Route::patch('service-bookings/{serviceBooking}/status', [ServiceBookingController::class, 'updateStatus'])
    ->name('service-bookings.status');
Route::post('service-bookings/{serviceBooking}/convert', [AdminWorkOrderController::class, 'convertBooking'])
    ->name('service-bookings.convert');

Route::resource('work-orders', AdminWorkOrderController::class);
Route::patch('work-orders/{workOrder}/status', [AdminWorkOrderController::class, 'updateStatus'])
    ->name('work-orders.status');
Route::post('work-orders/{workOrder}/service-items', [AdminWorkOrderController::class, 'addServiceItem'])
    ->name('work-orders.service-items.store');
Route::post('work-orders/{workOrder}/part-items', [AdminWorkOrderController::class, 'addPartItem'])
    ->name('work-orders.part-items.store');
Route::delete('work-order-items/{item}', [AdminWorkOrderController::class, 'removeItem'])
    ->name('work-orders.items.destroy');
Route::post('work-orders/{workOrder}/complete', [AdminWorkOrderController::class, 'complete'])
    ->name('work-orders.complete');
Route::post('work-orders/{workOrder}/deliver', [AdminWorkOrderController::class, 'deliver'])
    ->name('work-orders.deliver');

// ── AI Scans ──
Route::get('ai-scans', [AiScanReviewController::class, 'index'])->name('ai-scans.index');
Route::get('ai-scans/{aiScan}', [AiScanReviewController::class, 'show'])->name('ai-scans.show');
Route::patch('ai-scans/{aiScan}/review', [AiScanReviewController::class, 'review'])->name('ai-scans.review');

// ── Spare Parts & Inventory ──
Route::resource('spare-parts', SparePartController::class);
Route::delete('spare-parts/{sparePart}/image', [SparePartController::class, 'deleteImage'])->name('spare-parts.image.destroy');
Route::resource('purchase-orders', PurchaseOrderController::class);
Route::patch('purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])
    ->name('purchase-orders.status');
Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
Route::get('inventory/{sparePart}/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
Route::post('inventory/{sparePart}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
Route::resource('inventory', InventoryController::class)->except(['destroy']);

// ── CRM / Customers ──
Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update']);
Route::get('customers/{customer}/vehicles', [CustomerController::class, 'vehicles'])->name('customers.vehicles');
Route::get('customers/{customer}/history', [CustomerController::class, 'history'])->name('customers.history');

// ── Contact Messages / Pesan Masuk Customer ──
Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
Route::post('contact-messages/{contactMessage}/reply', [ContactMessageController::class, 'reply'])->name('contact-messages.reply');

// ── Support Tickets / Layanan & Komplain ──
Route::resource('tickets', TicketController::class)->only(['index', 'show']);
Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');

// ── Vouchers & Promos ──
Route::resource('vouchers', VoucherController::class);
Route::patch('vouchers/{voucher}/toggle', [VoucherController::class, 'toggle'])->name('vouchers.toggle');
Route::resource('promotions', PromotionController::class);
Route::patch('promotions/{promotion}/toggle', [PromotionController::class, 'toggle'])->name('promotions.toggle');

// ── Loyalty Program ──
Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
Route::get('loyalty/history', [LoyaltyController::class, 'history'])->name('loyalty.history');
Route::post('loyalty/{customer}/adjust', [LoyaltyController::class, 'adjust'])->name('loyalty.adjust');

// ── Reports ──
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
Route::get('reports/workshop', [ReportController::class, 'workshop'])->name('reports.workshop');
Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
Route::get('reports/finance', [ReportController::class, 'finance'])
    ->name('reports.finance')
    ->middleware('role:super-admin|manager|cashier');

// ── User Management ──
// ── Manajemen Pengguna (hanya super-admin & manager) ──
Route::resource('users', UserController::class)
    ->middleware('role:super-admin|manager');
Route::patch('users/{user}/role', [UserController::class, 'updateRole'])
    ->name('users.role')
    ->middleware('role:super-admin|manager');

// ── Settings (Khusus Super Admin) ──
Route::middleware('role:super-admin')->group(function () {
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/reset', [SettingController::class, 'reset'])->name('settings.reset');
});

// ── Audit Log ──
Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

// ── Approvals ──
Route::get('approvals', [\App\Http\Controllers\Admin\ApprovalController::class, 'index'])->name('approvals.index');
Route::patch('approvals/{approval}', [\App\Http\Controllers\Admin\ApprovalController::class, 'update'])->name('approvals.update');

// ── Notifications ──
Route::get('notifications', [DashboardController::class, 'notifications'])->name('notifications.index');
Route::patch('notifications/{notification}/read', [DashboardController::class, 'markRead'])->name('notifications.read');
Route::post('notifications/read-all', [DashboardController::class, 'markAllRead'])->name('notifications.read-all');