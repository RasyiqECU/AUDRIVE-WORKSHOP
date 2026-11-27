<?php

use App\Http\Controllers\Admin\AdminWorkOrderController;
use App\Http\Controllers\Admin\AiScanReviewController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\ConsignmentController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\PartRequestController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceBookingController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SparePartController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\WarrantyClaimController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Prefix: /admin
| Middleware: auth, verified, role:...
*/

// ── Dashboard & Notifikasi (Semua Staff Internal) ──
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('notifications', [DashboardController::class, 'notifications'])->name('notifications.index');
Route::patch('notifications/{notification}/read', [DashboardController::class, 'markRead'])->name('notifications.read');
Route::post('notifications/read-all', [DashboardController::class, 'markAllRead'])->name('notifications.read-all');

// ── Unit Supercar (Super Admin: Full; Manager: View; Sales: CRUD tanpa hapus) ──
Route::middleware('role:super-admin|sales')->group(function () {
    Route::get('cars/create', [CarController::class, 'create'])->name('cars.create');
    Route::post('cars', [CarController::class, 'store'])->name('cars.store');
    Route::get('cars/{car}/edit', [CarController::class, 'edit'])->name('cars.edit');
    Route::match(['put', 'patch'], 'cars/{car}', [CarController::class, 'update'])->name('cars.update');
    Route::patch('cars/{car}/status', [CarController::class, 'updateStatus'])->name('cars.status');
    Route::get('cars/{car}/inspection', [CarController::class, 'inspection'])->name('cars.inspection');
    Route::post('cars/{car}/inspection', [CarController::class, 'storeInspection'])->name('cars.inspection.store');
    Route::post('cars/{car}/consignment/approve', [ConsignmentController::class, 'approve'])->name('consignments.approve');
    Route::post('cars/{car}/consignment/reject', [ConsignmentController::class, 'reject'])->name('consignments.reject');
    Route::post('cars/{car}/featured', [CarController::class, 'setFeatured'])->name('cars.featured');
    Route::delete('cars/{car}/featured', [CarController::class, 'removeFeatured'])->name('cars.featured.remove');
    Route::post('cars/{car}/photos', [CarController::class, 'uploadPhotos'])->name('cars.photos.store');
    Route::delete('cars/{car}/photos/{photo}', [CarController::class, 'deletePhoto'])->name('cars.photos.destroy');
    Route::post('cars/{car}/photos/{photo}/primary', [CarController::class, 'setPrimaryPhoto'])->name('cars.photos.primary');
    Route::post('cars/{car}/photos/{photo}/replace', [CarController::class, 'replacePhoto'])->name('cars.photos.replace');
});
Route::middleware('role:super-admin|sales')->group(function () {
    Route::get('cars', [CarController::class, 'index'])->name('cars.index');
    Route::get('cars/{car}', [CarController::class, 'show'])->name('cars.show');
});
Route::delete('cars/{car}', [CarController::class, 'destroy'])->name('cars.destroy')->middleware('role:super-admin');

// ── Pesanan Masuk / Offer Mobil (Super Admin, Manager, Sales) ──
Route::middleware('role:super-admin|sales')->group(function () {
    Route::resource('offers', OfferController::class)->only(['index', 'show']);
    Route::post('offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::post('offers/{offer}/reject', [OfferController::class, 'reject'])->name('offers.reject');
});

// ── Jadwal Test Drive (Super Admin & Manager: View; Sales: Manage status) ──
Route::middleware('role:super-admin|sales')->group(function () {
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
});
Route::middleware('role:super-admin|sales')->group(function () {
    Route::resource('appointments', AppointmentController::class)->only(['index', 'show']);
});

// ── Point of Sales (POS / Kasir) (HANYA Super Admin & Kasir) ──
Route::middleware('role:super-admin|cashier')->group(function () {
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos', [PosController::class, 'store'])->name('pos.store');
    Route::post('pos/work-orders/{workOrder}/pay', [PosController::class, 'payWorkOrder'])->name('pos.work-orders.pay');
    Route::post('pos/car-sales/{salesOrder}/pay', [PosController::class, 'payCarSales'])->name('pos.car-sales.pay');
    Route::post('pos/part-orders/{salesOrder}/pay', [PosController::class, 'payPartOrder'])->name('pos.part-orders.pay');
    Route::post('pos/consignments/{car}/payout', [ConsignmentController::class, 'payout'])->name('pos.consignments.payout');
    Route::get('pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::get('pos/{salesOrder}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('pos/{salesOrder}/print', [PosController::class, 'printReceipt'])->name('pos.print');
});

// ── Invoices & Bukti Pembayaran (Super Admin, Manager, Kasir, Sales) ──
Route::middleware('role:super-admin|cashier|sales')->group(function () {
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'download'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
});

// ── Booking Servis (Sales: walk-in & check-in; Manager: buat WO; Kasir: status) ──
// Walk-in & check-in harus didefinisikan sebelum wildcard service-bookings/{serviceBooking}
Route::middleware('role:super-admin|sales')->group(function () {
    Route::get('service-bookings/create', [ServiceBookingController::class, 'create'])->name('service-bookings.create');
    Route::post('service-bookings', [ServiceBookingController::class, 'store'])->name('service-bookings.store');
    Route::post('service-bookings/{serviceBooking}/check-in', [ServiceBookingController::class, 'checkIn'])->name('service-bookings.check-in');
});
Route::middleware('role:super-admin|cashier|sales')->group(function () {
    Route::patch('service-bookings/{serviceBooking}/status', [ServiceBookingController::class, 'updateStatus'])->name('service-bookings.status');
});
Route::post('service-bookings/{serviceBooking}/convert', [AdminWorkOrderController::class, 'convertBooking'])
    ->name('service-bookings.convert')
    ->middleware('role:super-admin|mechanic');
Route::middleware('role:super-admin|cashier|sales|mechanic')->group(function () {
    Route::get('service-bookings', [ServiceBookingController::class, 'index'])->name('service-bookings.index');
    Route::get('service-bookings/{serviceBooking}', [ServiceBookingController::class, 'show'])->name('service-bookings.show');
});

// ── Work Orders & Antrean (Super Admin, Manager, Kasir, Sales, Mekanik) ──
// Create WO: Super Admin & Manager (harus didefinisikan sebelum wildcard work-orders/{workOrder})
Route::get('work-orders/create', [AdminWorkOrderController::class, 'create'])
    ->name('work-orders.create')
    ->middleware('role:super-admin|mechanic');
// Serah terima: Super Admin, Kasir, Sales (hanya setelah lunas)
Route::post('work-orders/{workOrder}/deliver', [AdminWorkOrderController::class, 'deliver'])
    ->name('work-orders.deliver')
    ->middleware('role:super-admin|cashier|sales');
// Lolos QC: Super Admin & Manager
Route::post('work-orders/{workOrder}/complete', [AdminWorkOrderController::class, 'complete'])
    ->name('work-orders.complete')
    ->middleware('role:super-admin|mechanic');
// Assign Mekanik: Super Admin & Manager ONLY
Route::patch('work-orders/{workOrder}/assign', [AdminWorkOrderController::class, 'assignMechanic'])
    ->name('work-orders.assign')
    ->middleware('role:super-admin|mechanic');
// Hapus WO: Super Admin ONLY
Route::delete('work-orders/{workOrder}', [AdminWorkOrderController::class, 'destroy'])
    ->name('work-orders.destroy')
    ->middleware('role:super-admin');
Route::middleware('role:super-admin|cashier|sales|mechanic')->group(function () {
    Route::get('work-orders', [AdminWorkOrderController::class, 'index'])->name('work-orders.index');
    Route::get('work-orders/{workOrder}', [AdminWorkOrderController::class, 'show'])->name('work-orders.show');
});
Route::middleware('role:super-admin|cashier|mechanic')->group(function () {
    Route::patch('work-orders/{workOrder}/status', [AdminWorkOrderController::class, 'updateStatus'])->name('work-orders.status');
    Route::post('work-orders/{workOrder}/service-items', [AdminWorkOrderController::class, 'addServiceItem'])->name('work-orders.service-items.store');
    Route::post('work-orders/{workOrder}/part-items', [AdminWorkOrderController::class, 'addPartItem'])->name('work-orders.part-items.store');
    Route::delete('work-order-items/{item}', [AdminWorkOrderController::class, 'removeItem'])->name('work-orders.items.destroy');
});

// ── Klaim Garansi (Sales buat → Mekanik diagnosa → Manager putuskan → Inventory keluarkan part) ──
Route::middleware('role:super-admin|sales')->group(function () {
    Route::get('warranty-claims/create', [WarrantyClaimController::class, 'create'])->name('warranty-claims.create');
    Route::post('warranty-claims', [WarrantyClaimController::class, 'store'])->name('warranty-claims.store');
});
Route::middleware('role:super-admin|sales|mechanic|manager|inventory')->group(function () {
    Route::get('warranty-claims', [WarrantyClaimController::class, 'index'])->name('warranty-claims.index');
    Route::get('warranty-claims/{warrantyClaim}', [WarrantyClaimController::class, 'show'])->name('warranty-claims.show');
});
Route::post('warranty-claims/{warrantyClaim}/diagnose', [WarrantyClaimController::class, 'diagnose'])
    ->name('warranty-claims.diagnose')
    ->middleware('role:super-admin|mechanic');
Route::post('warranty-claims/{warrantyClaim}/decide', [WarrantyClaimController::class, 'decide'])
    ->name('warranty-claims.decide')
    ->middleware('role:super-admin|manager');
Route::post('warranty-claims/{warrantyClaim}/parts', [WarrantyClaimController::class, 'issuePart'])
    ->name('warranty-claims.parts')
    ->middleware('role:super-admin|inventory');

// ── Usulan Suku Cadang (Gudang cek stok/harga; Sales catat keputusan customer) ──
Route::get('part-requests', [PartRequestController::class, 'index'])
    ->name('part-requests.index')
    ->middleware('role:super-admin|inventory|sales');
Route::post('part-requests/{item}/check', [PartRequestController::class, 'check'])
    ->name('part-requests.check')
    ->middleware('role:super-admin|inventory');
Route::post('part-requests/{item}/decide', [PartRequestController::class, 'decide'])
    ->name('part-requests.decide')
    ->middleware('role:super-admin|sales');

// ── AI Sound Diagnostic (Super Admin, Manager, Mekanik) ──
Route::middleware('role:super-admin|mechanic')->group(function () {
    Route::get('ai-scans', [AiScanReviewController::class, 'index'])->name('ai-scans.index');
    Route::get('ai-scans/{aiScan}', [AiScanReviewController::class, 'show'])->name('ai-scans.show');
    Route::patch('ai-scans/{aiScan}/review', [AiScanReviewController::class, 'review'])->name('ai-scans.review');
});

// ── Katalog Suku Cadang (View: Super Admin, Manager, Inventory, Mekanik; CRUD: Super Admin & Inventory) ──
Route::middleware('role:super-admin|inventory')->group(function () {
    Route::get('spare-parts/create', [SparePartController::class, 'create'])->name('spare-parts.create');
    Route::post('spare-parts', [SparePartController::class, 'store'])->name('spare-parts.store');
    Route::get('spare-parts/{sparePart}/edit', [SparePartController::class, 'edit'])->name('spare-parts.edit');
    Route::match(['put', 'patch'], 'spare-parts/{sparePart}', [SparePartController::class, 'update'])->name('spare-parts.update');
    Route::delete('spare-parts/{sparePart}', [SparePartController::class, 'destroy'])->name('spare-parts.destroy');
    Route::delete('spare-parts/{sparePart}/image', [SparePartController::class, 'deleteImage'])->name('spare-parts.image.destroy');
});
Route::middleware('role:super-admin|inventory|mechanic')->group(function () {
    Route::get('spare-parts', [SparePartController::class, 'index'])->name('spare-parts.index');
    Route::get('spare-parts/{sparePart}', [SparePartController::class, 'show'])->name('spare-parts.show');
});

// ── Stok & Movement (Super Admin, Manager, Inventory) ──
Route::middleware('role:super-admin|inventory')->group(function () {
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('inventory/{sparePart}/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::post('inventory/{sparePart}/adjust-stock', [InventoryController::class, 'adjustStock'])
        ->name('inventory.adjust-stock')
        ->middleware('role:super-admin|inventory');
});

// Edit & perubahan stok: hanya Super Admin & Inventory (Manager hanya melihat)
Route::middleware('role:super-admin|inventory')->group(function () {
    Route::get('inventory/{sparePart}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::match(['put', 'patch'], 'inventory/{sparePart}', [InventoryController::class, 'update'])->name('inventory.update');
});

// ── Purchase Order (Super Admin, Manager, Inventory) ──
Route::middleware('role:super-admin|inventory')->group(function () {
    Route::get('purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::post('purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])->name('purchase-orders.send');
    Route::post('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
});
Route::middleware('role:super-admin|inventory')->group(function () {
    Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
});

// ── CRM / Database Pelanggan (Super Admin, Manager, Kasir — Sales tidak: data milik perusahaan) ──
Route::middleware('role:super-admin|cashier')->group(function () {
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/{customer}/vehicles', [CustomerController::class, 'vehicles'])->name('customers.vehicles');
    Route::get('customers/{customer}/history', [CustomerController::class, 'history'])->name('customers.history');
});
Route::middleware('role:super-admin')->group(function () {
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::match(['put', 'patch'], 'customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
});

// ── Pesan Masuk Customer (Super Admin, Manager, Sales) ──
Route::middleware('role:super-admin|sales')->group(function () {
    Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
    Route::post('contact-messages/{contactMessage}/reply', [ContactMessageController::class, 'reply'])->name('contact-messages.reply');
});

// ── Tiket Layanan & Komplain (Super Admin, Manager, Sales) ──
Route::middleware('role:super-admin|sales')->group(function () {
    Route::resource('tickets', TicketController::class)->only(['index', 'show']);
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
});

// ── Vouchers & Promos (Super Admin, Manager: CRUD; Sales: View) ──
Route::middleware('role:super-admin|sales')->group(function () {
    Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
    Route::get('vouchers/{voucher}', [VoucherController::class, 'show'])
        ->name('vouchers.show')
        ->where('voucher', '[0-9]+');
    Route::get('promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('promotions/{promotion}', [PromotionController::class, 'show'])
        ->name('promotions.show')
        ->where('promotion', '[0-9]+');
});
Route::middleware('role:super-admin')->group(function () {
    Route::resource('vouchers', VoucherController::class)->except(['index', 'show']);
    Route::patch('vouchers/{voucher}/toggle', [VoucherController::class, 'toggle'])->name('vouchers.toggle');
    Route::resource('promotions', PromotionController::class)->except(['index', 'show']);
    Route::patch('promotions/{promotion}/toggle', [PromotionController::class, 'toggle'])->name('promotions.toggle');
});

// ── Program Loyalitas (Super Admin, Manager, Sales, Kasir) ──
Route::middleware('role:super-admin|sales|cashier')->group(function () {
    Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::get('loyalty/history', [LoyaltyController::class, 'history'])->name('loyalty.history');
});
Route::post('loyalty/{customer}/adjust', [LoyaltyController::class, 'adjust'])
    ->name('loyalty.adjust')
    ->middleware('role:super-admin');

// ── Laporan & Analitik Performa (Super Admin, Owner, Manager, Sales, Kasir, Inventory) ──
Route::middleware('role:super-admin|owner|manager|sales|cashier|inventory')->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});
Route::get('reports/sales', [ReportController::class, 'sales'])
    ->name('reports.sales')
    ->middleware('role:super-admin|owner|manager|sales');
Route::get('reports/workshop', [ReportController::class, 'workshop'])
    ->name('reports.workshop')
    ->middleware('role:super-admin|owner|manager');
Route::get('reports/inventory', [ReportController::class, 'inventory'])
    ->name('reports.inventory')
    ->middleware('role:super-admin|owner|manager|inventory');
Route::get('reports/finance', [ReportController::class, 'finance'])
    ->name('reports.finance')
    ->middleware('role:super-admin|owner|manager|cashier');

// ── Pengajuan Approval (Staf Operasional: Sales, Kasir, Inventory) ──
// Harus didefinisikan sebelum wildcard approvals/{approval}
Route::middleware('role:super-admin|sales|cashier|inventory')->group(function () {
    Route::get('approvals/request', [ApprovalController::class, 'create'])->name('approvals.create');
    Route::post('approvals/request', [ApprovalController::class, 'store'])->name('approvals.store');
});

// ── Tata Kelola (Super Admin & Manager) ──
Route::middleware('role:super-admin|manager')->group(function () {
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('approvals/{approval}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

// ── Manajemen Pengguna (HANYA Super Admin) ──
// Perubahan role dilakukan lewat form edit user (admin.users.update)
Route::middleware('role:super-admin')->group(function () {
    Route::resource('users', UserController::class);
});

// ── Konfigurasi Sistem (HANYA Super Admin) ──
Route::middleware('role:super-admin')->group(function () {
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/reset', [SettingController::class, 'reset'])->name('settings.reset');
});
