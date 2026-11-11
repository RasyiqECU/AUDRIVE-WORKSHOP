<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Fase 3 audit: visibilitas menu sidebar per role + anti "menu nyasar".
 *
 * Sidebar yang sudah dikonfirmasi user adalah KEBENARAN FINAL dan tidak
 * boleh diubah oleh test ini — test hanya mengunci kondisinya. Label di bawah
 * diambil persis dari resources/views/components/layout/admin-sidebar.blade.php.
 */
function sidebarStaffUser(string $role): User
{
    return match ($role) {
        'super-admin' => User::factory()->superAdmin()->create(),
        default => User::factory()->{$role}()->create(),
    };
}

// Menu label sidebar yang HARUS tampil / HARUS TIDAK tampil per role.
$menuMatrix = [
    'super-admin' => [
        'show' => [
            'Dashboard Executive', 'Unit Supercar', 'Pesanan Masuk', 'Jadwal Test Drive', 'Voucher & Promo',
            'Booking Servis', 'Work Order &amp; Antrean', 'AI Sound Scan', 'POS Kasir', 'Invoice & Bukti Bayar',
            'Katalog Spare Part', 'Stok & Movement', 'Purchase Order', 'Pesan Customer', 'Tiket Layanan',
            'Program Loyalitas', 'Database Pelanggan', 'Laporan Performa', 'Pusat Approval', 'Audit Trail',
            'Manajemen Pengguna', 'Konfigurasi Sistem',
        ],
        'not' => ['Ajukan Approval', 'Executive Dashboard', 'Work Order Saya', 'Laporan Penjualan', 'Laporan Shift', 'Laporan Inventori'],
    ],
    'owner' => [
        'show' => ['Executive Dashboard'],
        'not' => [
            'Unit Supercar', 'Pesanan Masuk', 'Jadwal Test Drive', 'Voucher & Promo',
            'Booking Servis', 'Work Order & Antrean', 'AI Sound Scan', 'POS Kasir', 'Invoice & Bukti Bayar',
            'Katalog Spare Part', 'Stok & Movement', 'Purchase Order', 'Pesan Customer', 'Tiket Layanan',
            'Program Loyalitas', 'Database Pelanggan', 'Laporan Performa', 'Pusat Approval', 'Audit Trail',
            'Manajemen Pengguna', 'Konfigurasi Sistem', 'Ajukan Approval',
        ],
    ],
    'manager' => [
        'show' => ['Dashboard Manager', 'Work Order &amp; Antrean', 'Laporan Performa', 'Pusat Approval', 'Audit Trail'],
        'not' => [
            'Unit Supercar', 'Pesanan Masuk', 'Jadwal Test Drive', 'Voucher & Promo',
            'Booking Servis', 'AI Sound Scan', 'POS Kasir', 'Invoice & Bukti Bayar',
            'Katalog Spare Part', 'Stok & Movement', 'Purchase Order', 'Pesan Customer', 'Tiket Layanan',
            'Program Loyalitas', 'Database Pelanggan', 'Manajemen Pengguna', 'Konfigurasi Sistem', 'Ajukan Approval',
        ],
    ],
    'sales' => [
        'show' => [
            'Dashboard Sales', 'Unit Supercar', 'Pesanan Masuk', 'Jadwal Test Drive', 'Voucher & Promo',
            'Pesan Customer', 'Tiket Layanan', 'Ajukan Approval', 'Laporan Penjualan',
        ],
        'not' => [
            'Booking Servis', 'Work Order & Antrean', 'AI Sound Scan', 'POS Kasir', 'Invoice & Bukti Bayar',
            'Katalog Spare Part', 'Stok & Movement', 'Purchase Order', 'Program Loyalitas', 'Database Pelanggan',
            'Laporan Performa', 'Pusat Approval', 'Audit Trail', 'Manajemen Pengguna', 'Konfigurasi Sistem',
        ],
    ],
    'cashier' => [
        'show' => [
            'Dashboard Cashier', 'Booking Servis', 'Work Order &amp; Antrean', 'POS Kasir', 'Invoice & Bukti Bayar',
            'Ajukan Approval', 'Laporan Shift',
        ],
        'not' => [
            'Unit Supercar', 'Pesanan Masuk', 'Jadwal Test Drive', 'Voucher & Promo', 'AI Sound Scan',
            'Katalog Spare Part', 'Stok & Movement', 'Purchase Order', 'Pesan Customer', 'Tiket Layanan',
            'Program Loyalitas', 'Database Pelanggan', 'Laporan Performa', 'Pusat Approval', 'Audit Trail',
            'Manajemen Pengguna', 'Konfigurasi Sistem',
        ],
    ],
    'inventory' => [
        'show' => ['Dashboard Inventory', 'Katalog Spare Part', 'Stok & Movement', 'Purchase Order', 'Ajukan Approval', 'Laporan Inventori'],
        'not' => [
            'Unit Supercar', 'Pesanan Masuk', 'Jadwal Test Drive', 'Voucher & Promo', 'Booking Servis',
            'Work Order & Antrean', 'AI Sound Scan', 'POS Kasir', 'Invoice & Bukti Bayar',
            'Pesan Customer', 'Tiket Layanan', 'Program Loyalitas', 'Database Pelanggan',
            'Laporan Performa', 'Pusat Approval', 'Audit Trail', 'Manajemen Pengguna', 'Konfigurasi Sistem',
        ],
    ],
    'mechanic' => [
        'show' => ['Dashboard Mechanic', 'Work Order Saya', 'AI Sound Scan', 'Katalog Spare Part'],
        'not' => [
            'Unit Supercar', 'Pesanan Masuk', 'Jadwal Test Drive', 'Voucher & Promo', 'Booking Servis',
            'Work Order & Antrean', 'POS Kasir', 'Invoice & Bukti Bayar', 'Stok & Movement', 'Purchase Order',
            'Pesan Customer', 'Tiket Layanan', 'Program Loyalitas', 'Database Pelanggan', 'Ajukan Approval',
            'Laporan Performa', 'Pusat Approval', 'Audit Trail', 'Manajemen Pengguna', 'Konfigurasi Sistem',
        ],
    ],
];

test('menu sidebar setiap role sesuai matriks final', function (string $role) use ($menuMatrix) {
    $user = sidebarStaffUser($role);
    $html = $this->actingAs($user)->get(route('admin.dashboard'))->getContent();

    preg_match('/<nav id="admin-sidebar-nav".*?<\/nav>/s', $html, $nav);
    $navHtml = $nav[0] ?? '';
    expect($navHtml)->not->toBeEmpty("[$role] elemen nav sidebar tidak ditemukan");

    foreach ($menuMatrix[$role]['show'] as $label) {
        expect(str_contains($navHtml, $label))->toBeTrue("[$role] menu seharusnya TAMPIL tapi tidak ada di sidebar: $label");
    }

    foreach ($menuMatrix[$role]['not'] as $label) {
        expect(str_contains($navHtml, $label))->toBeFalse("[$role] menu seharusnya TERSEMBUNYI tapi tampil di sidebar: $label");
    }
})->with([
    ['super-admin'],
    ['owner'],
    ['manager'],
    ['sales'],
    ['cashier'],
    ['inventory'],
    ['mechanic'],
]);

test('setiap link di sidebar menghasilkan 200 untuk role masing-masing (anti menu nyasar)', function (string $role) {
    $user = sidebarStaffUser($role);
    $html = $this->actingAs($user)->get(route('admin.dashboard'))->getContent();

    preg_match('/<nav id="admin-sidebar-nav".*?<\/nav>/s', $html, $nav);
    $navHtml = $nav[0] ?? '';

    preg_match_all('/href="([^"]+)"/', $navHtml, $matches);
    $hrefs = array_unique($matches[1] ?? []);

    expect(count($hrefs))->toBeGreaterThan(0, "[$role] sidebar tanpa link apapun");

    foreach ($hrefs as $href) {
        if (str_contains($href, 'logout')) {
            continue; // logout berupa form POST, bukan GET
        }

        $response = $this->actingAs($user)->get($href);
        expect($response->status())->toBe(200, "[$role] link sidebar menuju status {$response->status()}: $href");
    }
})->with([
    ['super-admin'],
    ['owner'],
    ['manager'],
    ['sales'],
    ['cashier'],
    ['inventory'],
    ['mechanic'],
]);
