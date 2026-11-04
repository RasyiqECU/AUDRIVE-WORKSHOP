<?php

declare(strict_types=1);

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\User;
use App\Support\Terbilang;

beforeEach(function () {
    $this->customerUser = User::factory()->create(['role' => 'customer']);
    $this->customer = Customer::factory()->create(['user_id' => $this->customerUser->id]);

    $this->otherCustomerUser = User::factory()->create(['role' => 'customer']);
    $this->otherCustomer = Customer::factory()->create(['user_id' => $this->otherCustomerUser->id]);

    $this->cashier = User::factory()->create(['role' => 'cashier']);
    $this->superAdmin = User::factory()->create(['role' => 'super-admin']);

    // Create sales order and invoice
    $this->salesOrder = SalesOrder::create([
        'code' => 'SO-TEST-001',
        'customer_id' => $this->customer->id,
        'type' => SalesOrderType::CarSales,
        'status' => SalesOrderStatus::Paid,
        'subtotal_idr' => 5000000000,
        'discount_idr' => 100000000,
        'tax_idr' => 0,
        'total_idr' => 4900000000,
        'paid_idr' => 4900000000,
        'created_by' => $this->cashier->id,
    ]);

    $this->invoice = Invoice::create([
        'invoiceable_type' => SalesOrder::class,
        'invoiceable_id' => $this->salesOrder->id,
        'number' => 'INV-CAR-202611-TEST01',
        'status' => 'paid',
        'due_at' => now()->addDays(7),
        'total_idr' => 4900000000,
        'notes' => 'Test Invoice Note',
    ]);
});

test('terbilang helper accurately spells out indonesian currency words', function () {
    expect(Terbilang::make(0))->toBe('Nol Rupiah');
    expect(Terbilang::make(1500000))->toBe('Satu Juta Lima Ratus Ribu Rupiah');
    expect(Terbilang::make(7000000000))->toBe('Tujuh Miliar Rupiah');
});

test('customer can view their invoices index and summary', function () {
    $response = $this->actingAs($this->customerUser)
        ->get(route('customer.invoices.index'));

    $response->assertStatus(200);
    $response->assertSee('TAGIHAN & INVOICE SAYA', false);
    $response->assertSee('INV-CAR-202611-TEST01');
    $response->assertSee('Rp 4.900.000.000');
});

test('customer can view own invoice detail and proof of payment', function () {
    $response = $this->actingAs($this->customerUser)
        ->get(route('customer.invoices.show', $this->invoice));

    $response->assertStatus(200);
    $response->assertSee('INVOICE & BUKTI BAYAR', false);
    $response->assertSee('INV-CAR-202611-TEST01');
    $response->assertSee('LUNAS / VERIFIED PAID');
});

test('customer cannot access another customers invoice', function () {
    $response = $this->actingAs($this->otherCustomerUser)
        ->get(route('customer.invoices.show', $this->invoice));

    $response->assertStatus(403);
});

test('customer can download invoice pdf', function () {
    $response = $this->actingAs($this->customerUser)
        ->get(route('customer.invoices.pdf', $this->invoice));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('cashier and super admin can view admin invoices index with filters', function () {
    $response = $this->actingAs($this->cashier)
        ->get(route('admin.invoices.index', ['status' => 'paid']));

    $response->assertStatus(200);
    $response->assertSee('DAFTAR INVOICE & BUKTI BAYAR', false);
    $response->assertSee('INV-CAR-202611-TEST01');
});

test('cashier can view invoice detail and download pdf', function () {
    $response = $this->actingAs($this->cashier)
        ->get(route('admin.invoices.show', $this->invoice));

    $response->assertStatus(200);
    $response->assertSee('DETAIL INVOICE & BUKTI BAYAR', false);

    $pdfResponse = $this->actingAs($this->cashier)
        ->get(route('admin.invoices.pdf', $this->invoice));

    $pdfResponse->assertStatus(200);
    expect($pdfResponse->headers->get('content-type'))->toContain('application/pdf');
});

test('super admin can access all overhauled report views with presets and filters', function () {
    // 1. Index
    $rIndex = $this->actingAs($this->superAdmin)
        ->get(route('admin.reports.index', ['preset' => 'this_month']));
    $rIndex->assertStatus(200);
    $rIndex->assertSee('LAPORAN & ANALITIK EKSEKUTIF', false);

    // 2. Sales Report
    $rSales = $this->actingAs($this->superAdmin)
        ->get(route('admin.reports.sales', ['preset' => 'all']));
    $rSales->assertStatus(200);
    $rSales->assertSee('LAPORAN RESMI PENJUALAN SUPERCAR');
    $rSales->assertSee('REKAPITULASI TOTAL PENJUALAN');

    // 3. Workshop Report
    $rWorkshop = $this->actingAs($this->superAdmin)
        ->get(route('admin.reports.workshop', ['status' => 'all']));
    $rWorkshop->assertStatus(200);
    $rWorkshop->assertSee('LAPORAN RESMI BENGKEL & WORK ORDER', false);

    // 4. Inventory Report
    $rInventory = $this->actingAs($this->superAdmin)
        ->get(route('admin.reports.inventory', ['stock_status' => 'all']));
    $rInventory->assertStatus(200);
    $rInventory->assertSee('LAPORAN RESMI VALUASI SUKU CADANG & STOK', false);

    // 5. Finance Report
    $rFinance = $this->actingAs($this->superAdmin)
        ->get(route('admin.reports.finance', ['preset' => 'this_month']));
    $rFinance->assertStatus(200);
    $rFinance->assertSee('LAPORAN RESMI FINANSIAL & ARUS KAS', false);
});
