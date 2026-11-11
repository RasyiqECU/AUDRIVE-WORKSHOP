<?php

declare(strict_types=1);

use App\Enums\SalesOrderType;
use App\Models\AiScan;
use App\Models\Appointment;
use App\Models\ApprovalRequest;
use App\Models\CarUnit;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\Promotion;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Voucher;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Matriks kebenaran resmi akses route — diturunkan dari middleware `role:`
 * pada routes/admin.php (kebenaran final, mengikuti sidebar yang telah
 * dikonfirmasi). Route di luar daftar isian berada pada alur fitur
 * (WorkshopFlowTest, PosFlowTest, WarehouseFlowTest, CrmManagementTest).
 */
function matrixRoleUser(string $role): User
{
    return match ($role) {
        'super-admin' => User::factory()->superAdmin()->create(),
        'customer' => User::factory()->customer()->create(),
        default => User::factory()->{$role}()->create(),
    };
}

$internalRoles = ['super-admin', 'owner', 'manager', 'sales', 'cashier', 'inventory', 'mechanic'];

// Setiap grup = satu middleware group `role:` di routes/admin.php.
$groups = [
    [
        'label' => 'Dashboard & Notifikasi (semua staff internal)',
        'roles' => $internalRoles,
        'gets' => [
            ['admin.dashboard'],
            ['admin.notifications.index'],
        ],
    ],
    [
        'label' => 'Unit Supercar — CRUD tambah/edit (Super Admin & Sales)',
        'roles' => ['super-admin', 'sales'],
        'gets' => [
            ['admin.cars.create'],
            ['admin.cars.edit', fn () => CarUnit::factory()->create()],
        ],
        'mutations' => [
            ['post', 'admin.cars.store'],
            ['patch', 'admin.cars.status', fn () => CarUnit::factory()->create()],
        ],
    ],
    [
        'label' => 'Unit Supercar — view (Super Admin, Manager, Sales)',
        'roles' => ['super-admin', 'manager', 'sales'],
        'gets' => [
            ['admin.cars.index'],
            ['admin.cars.show', fn () => CarUnit::factory()->create()],
        ],
    ],
    [
        'label' => 'Unit Supercar — hapus permanen (Super Admin)',
        'roles' => ['super-admin'],
        'gets' => [],
        'mutations' => [
            ['delete', 'admin.cars.destroy', fn () => CarUnit::factory()->create()],
        ],
    ],
    [
        'label' => 'Pesanan Masuk / Offers (Super Admin, Manager, Sales)',
        'roles' => ['super-admin', 'manager', 'sales'],
        'gets' => [
            ['admin.offers.index'],
            ['admin.offers.show', fn () => Offer::factory()->pending()->create()],
        ],
    ],
    [
        'label' => 'Jadwal Test Drive — view (Super Admin, Manager, Sales)',
        'roles' => ['super-admin', 'manager', 'sales'],
        'gets' => [
            ['admin.appointments.index'],
            ['admin.appointments.show', fn () => Appointment::factory()->create()],
        ],
    ],
    [
        'label' => 'Jadwal Test Drive — ubah status (Super Admin & Sales)',
        'roles' => ['super-admin', 'sales'],
        'gets' => [],
        'mutations' => [
            ['patch', 'admin.appointments.status', fn () => Appointment::factory()->create()],
        ],
    ],
    [
        'label' => 'POS (Super Admin & Kasir)',
        'roles' => ['super-admin', 'cashier'],
        'gets' => [
            ['admin.pos.index'],
            ['admin.pos.search'],
        ],
        'mutations' => [
            ['post', 'admin.pos.store'],
            ['post', 'admin.pos.work-orders.pay', fn () => WorkOrder::factory()->create()],
        ],
    ],
    [
        'label' => 'Invoice & Bukti Bayar (Super Admin, Manager, Kasir, Sales — Sales hanya invoice mobil)',
        'roles' => ['super-admin', 'manager', 'cashier', 'sales'],
        'gets' => [
            ['admin.invoices.index'],
            ['admin.invoices.show', function (string $role) {
                if ($role === 'sales') {
                    $so = SalesOrder::factory()->create(['type' => SalesOrderType::CarSales]);

                    return Invoice::factory()->create(['invoiceable_type' => SalesOrder::class, 'invoiceable_id' => $so->id]);
                }

                return Invoice::factory()->create();
            }],
            ['admin.invoices.pdf', function (string $role) {
                if ($role === 'sales') {
                    $so = SalesOrder::factory()->create(['type' => SalesOrderType::CarSales]);

                    return Invoice::factory()->create(['invoiceable_type' => SalesOrder::class, 'invoiceable_id' => $so->id]);
                }

                return Invoice::factory()->create();
            }],
            ['admin.invoices.print', function (string $role) {
                if ($role === 'sales') {
                    $so = SalesOrder::factory()->create(['type' => SalesOrderType::CarSales]);

                    return Invoice::factory()->create(['invoiceable_type' => SalesOrder::class, 'invoiceable_id' => $so->id]);
                }

                return Invoice::factory()->create();
            }],
        ],
    ],
    [
        'label' => 'Booking Servis — ubah status & konversi WO (Super Admin & Kasir)',
        'roles' => ['super-admin', 'cashier'],
        'gets' => [],
        'mutations' => [
            ['patch', 'admin.service-bookings.status', fn () => ServiceBooking::factory()->create()],
            ['post', 'admin.service-bookings.convert', fn () => ServiceBooking::factory()->create()],
        ],
    ],
    [
        'label' => 'Booking Servis — view antrean (Super Admin, Manager, Kasir)',
        'roles' => ['super-admin', 'manager', 'cashier'],
        'gets' => [
            ['admin.service-bookings.index'],
            ['admin.service-bookings.show', fn () => ServiceBooking::factory()->create()],
        ],
    ],
    [
        'label' => 'Work Order — buat & serah terima (Super Admin & Kasir)',
        'roles' => ['super-admin', 'cashier'],
        'gets' => [
            ['admin.work-orders.create'],
        ],
        'mutations' => [
            ['post', 'admin.work-orders.store'],
            ['post', 'admin.work-orders.deliver', fn () => WorkOrder::factory()->create()],
        ],
    ],
    [
        'label' => 'Work Order — penugasan mekanik (Super Admin & Manager)',
        'roles' => ['super-admin', 'manager'],
        'gets' => [],
        'mutations' => [
            ['patch', 'admin.work-orders.assign', fn () => WorkOrder::factory()->create()],
        ],
    ],
    [
        'label' => 'Work Order — hapus (Super Admin)',
        'roles' => ['super-admin'],
        'gets' => [],
        'mutations' => [
            ['delete', 'admin.work-orders.destroy', fn () => WorkOrder::factory()->create()],
        ],
    ],
    [
        'label' => 'Work Order — view antrean (Super Admin, Manager, Kasir, Mekanik)',
        'roles' => ['super-admin', 'manager', 'cashier', 'mechanic'],
        'gets' => [
            ['admin.work-orders.index'],
        ],
    ],
    [
        'label' => 'AI Sound Scan — view & review (Super Admin, Manager, Mekanik)',
        'roles' => ['super-admin', 'manager', 'mechanic'],
        'gets' => [
            ['admin.ai-scans.index'],
        ],
        'mutations' => [
            ['patch', 'admin.ai-scans.review', fn () => AiScan::factory()->create()],
        ],
    ],
    [
        'label' => 'Katalog Spare Part — CRUD (Super Admin & Inventory)',
        'roles' => ['super-admin', 'inventory'],
        'gets' => [
            ['admin.spare-parts.create'],
            ['admin.spare-parts.edit', fn () => SparePart::factory()->create()],
        ],
        'mutations' => [
            ['post', 'admin.spare-parts.store'],
            ['patch', 'admin.spare-parts.update', fn () => SparePart::factory()->create()],
        ],
    ],
    [
        'label' => 'Katalog Spare Part — view (Super Admin, Manager, Inventory, Mekanik)',
        'roles' => ['super-admin', 'manager', 'inventory', 'mechanic'],
        'gets' => [
            ['admin.spare-parts.index'],
            ['admin.spare-parts.show', fn () => SparePart::factory()->create()],
        ],
    ],
    [
        'label' => 'Stok & Movement — view (Super Admin, Manager, Inventory)',
        'roles' => ['super-admin', 'manager', 'inventory'],
        'gets' => [
            ['admin.inventory.index'],
            ['admin.inventory.low-stock'],
            ['admin.inventory.movements', fn () => SparePart::factory()->create()],
        ],
    ],
    [
        'label' => 'Stok & Movement — edit & penyesuaian (Super Admin & Inventory)',
        'roles' => ['super-admin', 'inventory'],
        'gets' => [
            ['admin.inventory.edit', fn () => SparePart::factory()->create()],
        ],
        'mutations' => [
            ['patch', 'admin.inventory.update', fn () => SparePart::factory()->create()],
            ['post', 'admin.inventory.adjust-stock', fn () => SparePart::factory()->create()],
        ],
    ],
    [
        'label' => 'Purchase Order — pengelolaan (Super Admin & Inventory)',
        'roles' => ['super-admin', 'inventory'],
        'gets' => [
            ['admin.purchase-orders.create'],
        ],
        'mutations' => [
            ['post', 'admin.purchase-orders.store'],
            ['post', 'admin.purchase-orders.send', fn () => PurchaseOrder::factory()->create()],
            ['post', 'admin.purchase-orders.receive', fn () => PurchaseOrder::factory()->create()],
            ['post', 'admin.purchase-orders.cancel', fn () => PurchaseOrder::factory()->create()],
        ],
    ],
    [
        'label' => 'Purchase Order — view (Super Admin, Manager, Inventory)',
        'roles' => ['super-admin', 'manager', 'inventory'],
        'gets' => [
            ['admin.purchase-orders.index'],
            ['admin.purchase-orders.show', fn () => PurchaseOrder::factory()->create()],
        ],
    ],
    [
        'label' => 'Database Pelanggan — view (Super Admin, Manager, Kasir)',
        'roles' => ['super-admin', 'manager', 'cashier'],
        'gets' => [
            ['admin.customers.index'],
            ['admin.customers.show', fn () => Customer::factory()->create()],
            ['admin.customers.vehicles', fn () => Customer::factory()->create()],
            ['admin.customers.history', fn () => Customer::factory()->create()],
        ],
    ],
    [
        'label' => 'Database Pelanggan — koreksi (Super Admin & Manager)',
        'roles' => ['super-admin', 'manager'],
        'gets' => [
            ['admin.customers.edit', fn () => Customer::factory()->create()],
        ],
        'mutations' => [
            ['patch', 'admin.customers.update', fn () => Customer::factory()->create()],
        ],
    ],
    [
        'label' => 'Pesan Customer (Super Admin, Manager, Sales)',
        'roles' => ['super-admin', 'manager', 'sales'],
        'gets' => [
            ['admin.contact-messages.index'],
            ['admin.contact-messages.show', fn () => ContactMessage::create([
                'name' => 'Anonim', 'phone' => '0812', 'email' => 'anon@example.com',
                'topic' => 'konsultasi', 'message' => 'hallo', 'status' => 'unread',
            ])],
        ],
        'mutations' => [
            ['post', 'admin.contact-messages.reply', fn () => ContactMessage::create([
                'name' => 'Anonim', 'phone' => '0812', 'email' => 'anon@example.com',
                'topic' => 'konsultasi', 'message' => 'hallo', 'status' => 'unread',
            ])],
            ['delete', 'admin.contact-messages.destroy', fn () => ContactMessage::create([
                'name' => 'Anonim', 'phone' => '0812', 'email' => 'anon@example.com',
                'topic' => 'konsultasi', 'message' => 'hallo', 'status' => 'unread',
            ])],
        ],
    ],
    [
        'label' => 'Tiket Layanan (Super Admin, Manager, Sales)',
        'roles' => ['super-admin', 'manager', 'sales'],
        'gets' => [
            ['admin.tickets.index'],
            ['admin.tickets.show', fn () => SupportTicket::factory()->create()],
        ],
        'mutations' => [
            ['post', 'admin.tickets.reply', fn () => SupportTicket::factory()->create()],
            ['patch', 'admin.tickets.status', fn () => SupportTicket::factory()->create()],
        ],
    ],
    [
        'label' => 'Voucher & Promo — view (Super Admin, Manager, Sales)',
        'roles' => ['super-admin', 'manager', 'sales'],
        'gets' => [
            ['admin.vouchers.index'],
            ['admin.vouchers.show', fn () => Voucher::factory()->create()],
            ['admin.promotions.index'],
            ['admin.promotions.show', fn () => Promotion::factory()->create()],
        ],
    ],
    [
        'label' => 'Voucher & Promo — CRUD penuh (Super Admin & Manager)',
        'roles' => ['super-admin', 'manager'],
        'gets' => [
            ['admin.vouchers.create'],
            ['admin.vouchers.edit', fn () => Voucher::factory()->create()],
            ['admin.promotions.create'],
            ['admin.promotions.edit', fn () => Promotion::factory()->create()],
        ],
        'mutations' => [
            ['post', 'admin.vouchers.store'],
            ['patch', 'admin.vouchers.update', fn () => Voucher::factory()->create()],
            ['patch', 'admin.vouchers.toggle', fn () => Voucher::factory()->create()],
            ['delete', 'admin.vouchers.destroy', fn () => Voucher::factory()->create()],
            ['post', 'admin.promotions.store'],
            ['patch', 'admin.promotions.update', fn () => Promotion::factory()->create()],
            ['patch', 'admin.promotions.toggle', fn () => Promotion::factory()->create()],
            ['delete', 'admin.promotions.destroy', fn () => Promotion::factory()->create()],
        ],
    ],
    [
        'label' => 'Program Loyalitas — view & koreksi (Super Admin, Manager, Sales, Kasir)',
        'roles' => ['super-admin', 'manager', 'sales', 'cashier'],
        'gets' => [
            ['admin.loyalty.index'],
            ['admin.loyalty.history'],
        ],
    ],
    [
        'label' => 'Koreksi Poin Loyalitas (Super Admin & Manager)',
        'roles' => ['super-admin', 'manager'],
        'gets' => [],
        'mutations' => [
            ['post', 'admin.loyalty.adjust', fn () => Customer::factory()->create()],
        ],
    ],
    [
        'label' => 'Pengajuan Approval — form pengajuan (staf operasional)',
        'roles' => ['super-admin', 'manager', 'sales', 'cashier', 'inventory'],
        'gets' => [
            ['admin.approvals.create'],
        ],
        'mutations' => [
            ['post', 'admin.approvals.store'],
        ],
    ],
    [
        'label' => 'Pusat Approval & Audit Trail (Super Admin & Manager)',
        'roles' => ['super-admin', 'manager'],
        'gets' => [
            ['admin.approvals.index'],
            ['admin.approvals.show', fn () => ApprovalRequest::factory()->create()],
            ['admin.audit-logs.index'],
        ],
        'mutations' => [
            ['post', 'admin.approvals.approve', fn () => ApprovalRequest::factory()->create()],
            ['post', 'admin.approvals.reject', fn () => ApprovalRequest::factory()->create()],
        ],
    ],
    [
        'label' => 'Laporan — indeks (semua divisi, tanpa mekanik)',
        'roles' => ['super-admin', 'owner', 'manager', 'sales', 'cashier', 'inventory'],
        'gets' => [
            ['admin.reports.index'],
        ],
    ],
    [
        'label' => 'Laporan — penjualan (Super Admin, Owner, Manager, Sales)',
        'roles' => ['super-admin', 'owner', 'manager', 'sales'],
        'gets' => [
            ['admin.reports.sales'],
        ],
    ],
    [
        'label' => 'Laporan — workshop (Super Admin, Owner, Manager)',
        'roles' => ['super-admin', 'owner', 'manager'],
        'gets' => [
            ['admin.reports.workshop'],
        ],
    ],
    [
        'label' => 'Laporan — inventori (Super Admin, Owner, Manager, Inventory)',
        'roles' => ['super-admin', 'owner', 'manager', 'inventory'],
        'gets' => [
            ['admin.reports.inventory'],
        ],
    ],
    [
        'label' => 'Laporan — finansial (Super Admin, Owner, Manager, Kasir)',
        'roles' => ['super-admin', 'owner', 'manager', 'cashier'],
        'gets' => [
            ['admin.reports.finance'],
        ],
    ],
    [
        'label' => 'Manajemen Pengguna (Super Admin)',
        'roles' => ['super-admin'],
        'gets' => [
            ['admin.users.index'],
            ['admin.users.create'],
            ['admin.users.show', fn () => User::factory()->create()],
            ['admin.users.edit', fn () => User::factory()->create()],
        ],
        'mutations' => [
            ['post', 'admin.users.store'],
            ['patch', 'admin.users.update', fn () => User::factory()->create()],
            ['delete', 'admin.users.destroy', fn () => User::factory()->create()],
        ],
    ],
    [
        'label' => 'Konfigurasi Sistem (Super Admin)',
        'roles' => ['super-admin'],
        'gets' => [
            ['admin.settings.index'],
        ],
        'mutations' => [
            ['put', 'admin.settings.update'],
            ['post', 'admin.settings.reset'],
        ],
    ],
];

test('setiap role bisa membuka seluruh GET route pada group yang berhak', function () use ($groups) {
    foreach ($groups as $group) {
        foreach ($group['roles'] as $role) {
            $user = matrixRoleUser($role);

            foreach ($group['gets'] as $entry) {
                $name = $entry[0];
                $param = $entry[1] ?? null;
                $model = is_callable($param) ? $param($role) : null;
                $response = $this->actingAs($user)->get(route($name, $model));

                // 200 normal; 302 (redirect internal) juga dianggap akses granted
                expect(in_array($response->status(), [200, 302], true))->toBeTrue(
                    sprintf('[%s] role=%s GET %s → status %d', $group['label'], $role, $name, $response->status()),
                );
            }
        }
    }
});

test('role di luar grup ditolak 403 pada semua route grup termasuk customer', function () use ($groups, $internalRoles) {
    foreach ($groups as $group) {
        $denied = array_values(array_diff($internalRoles, $group['roles']));
        $denied[] = 'customer';

        $mut = $group['mutations'] ?? [];

        foreach ($denied as $role) {
            $user = matrixRoleUser($role);
            foreach ($group['gets'] as $entry) {
                $name = $entry[0];
                $param = $entry[1] ?? null;
                $model = is_callable($param) ? $param($role) : null;
                $response = $this->actingAs($user)->get(route($name, $model));

                expect($response->status())->toBe(403, sprintf('[%s] role=%s GET %s → status %d', $group['label'], $role, $name, $response->status()));
            }

            foreach ($mut as $entry) {
                $method = $entry[0];
                $name = $entry[1];
                $param = $entry[2] ?? null;
                $model = is_callable($param) ? $param() : null;
                $response = $this->actingAs($user)->{$method}(route($name, $model));

                expect($response->status())->toBe(403, sprintf('[%s] role=%s %s %s → status %d', $group['label'], $role, strtoupper($method), $name, $response->status()));
            }
        }
    }
});

test('customer ditolak 403 pada seluruh route admin mutasi', function () {
    $customer = User::factory()->customer()->create();
    $car = CarUnit::factory()->create();

    $targets = [
        ['post', 'admin.cars.store', [], []],
        ['patch', 'admin.cars.update', [$car], ['name' => 'x']],
        ['delete', 'admin.cars.destroy', [$car], []],
        ['post', 'admin.pos.store', [], []],
        ['post', 'admin.users.store', [], []],
        ['post', 'admin.work-orders.store', [], []],
    ];

    foreach ($targets as [$method, $name, $param, $data]) {
        $response = $this->actingAs($customer)->{$method}(route($name, $param[0] ?? null), $data);
        expect($response->status())->toBe(403, $name);
    }
});

/**
 * Verifikasi hasil pembersihan hasil audit: URL milik route mati di bawah ini
 * telah dibuang dari routes/admin.php (UI dan sidebar tidak pernah memakainya,
 * dan pembuatan booking hanya via portal customer / API). Konsekuensinya URL
 * tersebut mengembalikan 404 (atau 405 bila URL masih di-shadow route GET
 * yang hidup) — yang penting bukan 500 lagi, bahkan untuk Super Admin.
 */
test('pembersihan hasil audit: URL modul mati mengembalikan 404, bukan 500', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $booking = ServiceBooking::factory()->create();
    $po = PurchaseOrder::factory()->create();
    $user = User::factory()->create();

    $checks = [
        // Route dead ServiceBookingController dibuang
        ['get', '/admin/service-bookings/create'],
        ['post', '/admin/service-bookings'],
        ['get', '/admin/service-bookings/'.$booking->id.'/edit'],
        ['patch', '/admin/service-bookings/'.$booking->id],
        ['delete', '/admin/service-bookings/'.$booking->id],
        // Route dead PurchaseOrderController dibuang
        ['get', '/admin/purchase-orders/'.$po->id.'/edit'],
        ['patch', '/admin/purchase-orders/'.$po->id],
        ['delete', '/admin/purchase-orders/'.$po->id],
        // Route dead UserController::updateRole dibuang
        ['patch', '/admin/users/'.$user->id.'/role'],
    ];

    foreach ($checks as [$method, $url]) {
        $response = $this->actingAs($superAdmin)->{$method}($url);

        // 405 (Method Not Allowed) tetap diterima: artinya URL masih punya
        // route GET yang hidup (mis. GET service-bookings/index) tanpa route
        // POST — intinya bukan 500 lagi.
        expect(in_array($response->status(), [404, 405], true))->toBeTrue(
            "URL $url harus 404/405 karena route matinya sudah dibuang.",
        );
    }
});
