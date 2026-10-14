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

// ── Offers / Penawaran Harga Mobil ──
Route::resource('offers', \App\Http\Controllers\Admin\OfferController::class)->only(['index', 'show']);
Route::post('offers/{offer}/accept', [\App\Http\Controllers\Admin\OfferController::class, 'accept'])->name('offers.accept');
Route::post('offers/{offer}/reject', [\App\Http\Controllers\Admin\OfferController::class, 'reject'])->name('offers.reject');

// ── Point of Sales (POS / Kasir) ──
Route::get('pos', [\App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos.index');
Route::post('pos', [\App\Http\Controllers\Admin\PosController::class, 'store'])->name('pos.store');
Route::post('pos/work-orders/{workOrder}/pay', [\App\Http\Controllers\Admin\PosController::class, 'payWorkOrder'])->name('pos.work-orders.pay');
Route::post('pos/car-sales/{salesOrder}/pay', [\App\Http\Controllers\Admin\PosController::class, 'payCarSales'])->name('pos.car-sales.pay');
Route::get('pos/search', [\App\Http\Controllers\Admin\PosController::class, 'search'])->name('pos.search');
Route::get('pos/{salesOrder}/receipt', [\App\Http\Controllers\Admin\PosController::class, 'receipt'])->name('pos.receipt');
Route::get('pos/{salesOrder}/print', [\App\Http\Controllers\Admin\PosController::class, 'printReceipt'])->name('pos.print');

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
Route::get('ai-scans/{scan}', [AiScanReviewController::class, 'show'])->name('ai-scans.show');
Route::patch('ai-scans/{scan}/review', [AiScanReviewController::class, 'review'])->name('ai-scans.review');

// ── Spare Parts & Inventory ──
Route::resource('spare-parts', SparePartController::class);
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

// ── Reports ──
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
Route::get('reports/workshop', [ReportController::class, 'workshop'])->name('reports.workshop');
Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
Route::get('reports/finance', [ReportController::class, 'finance'])->name('reports.finance');

// ── User Management ──
Route::resource('users', UserController::class);
Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');

// ── Settings ──
Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

// ── Audit Log ──
Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

// ── Approvals ──
Route::get('approvals', [\App\Http\Controllers\Admin\ApprovalController::class, 'index'])->name('approvals.index');
Route::patch('approvals/{approval}', [\App\Http\Controllers\Admin\ApprovalController::class, 'update'])->name('approvals.update');

// ── Notifications ──
Route::get('notifications', [DashboardController::class, 'notifications'])->name('notifications.index');
Route::patch('notifications/{notification}/read', [DashboardController::class, 'markRead'])->name('notifications.read');
Route::post('notifications/read-all', [DashboardController::class, 'markAllRead'])->name('notifications.read-all');