<?php

declare(strict_types=1);

use App\Enums\SalesOrderType;
use App\Enums\WorkOrderStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Fase 4 audit — alur fitur POS (Super Admin & Kasir):
 * transaksi ritel, pembayaran work order, kwitansi, poin loyalitas.
 */
test('transaksi POS ritel membuat sales order paid, mengurangi stok, mencatat movement, payment & invoice', function () {
    $cashier = User::factory()->cashier()->create();
    $part = SparePart::factory()->active()->create(['stock' => 10, 'price_idr' => 100_000]);

    $response = $this->actingAs($cashier)
        ->post(route('admin.pos.store'), [
            'items' => [
                ['spare_part_id' => $part->id, 'qty' => 2],
            ],
            'payment_method' => 'cash',
        ]);

    $salesOrder = SalesOrder::where('type', SalesOrderType::Pos)->latest('id')->first();
    expect($salesOrder)->not->toBeNull('Transaksi POS seharusnya tersimpan');
    $response->assertRedirect(route('admin.pos.receipt', $salesOrder));

    expect($salesOrder->status->value ?? $salesOrder->status)->toBe('paid');
    // subtotal 200.000; PPN 11% = 22.000 → total 222.000
    expect((int) $salesOrder->subtotal_idr)->toBe(200_000);
    expect((int) $salesOrder->total_idr)->toBe(222_000);

    expect((int) $part->fresh()->stock)->toBe(8);
    expect(StockMovement::where('spare_part_id', $part->id)->where('type', 'out')->exists())->toBeTrue();

    expect(Payment::where('paymentable_type', SalesOrder::class)->where('paymentable_id', $salesOrder->id)->exists())->toBeTrue();
    expect(Invoice::where('invoiceable_type', SalesOrder::class)->where('invoiceable_id', $salesOrder->id)->exists())->toBeTrue();

    // Kwitansi & cetak kwitansi bisa dibuka
    $this->actingAs($cashier)->get(route('admin.pos.receipt', $salesOrder))->assertOk();
    $this->actingAs($cashier)->get(route('admin.pos.print', $salesOrder))->assertOk();
});

test('transaksi POS untuk customer terdaftar memberi poin loyalitas 1 poin per Rp 10.000', function () {
    $cashier = User::factory()->cashier()->create();
    $customerUser = Customer::factory()->create(['loyalty_points' => 0])->user;
    $customer = $customerUser->customer;
    $part = SparePart::factory()->active()->create(['stock' => 10, 'price_idr' => 150_000]);

    $this->actingAs($cashier)
        ->post(route('admin.pos.store'), [
            'items' => [
                ['spare_part_id' => $part->id, 'qty' => 1],
            ],
            'customer_id' => $customer->id,
            'payment_method' => 'card',
        ]);

    $customer->refresh();
    expect((int) $customer->loyalty_points)->toBe(15, '150.000 / 10.000 = 15 poin');
    expect($customer->loyaltyTransactions()->where('type', 'earning')->exists())->toBeTrue();
});

test('stok tidak cukup ditolak dan tidak ada transaksi tersimpan', function () {
    $cashier = User::factory()->cashier()->create();
    $part = SparePart::factory()->active()->create(['stock' => 2, 'price_idr' => 100_000]);

    $response = $this->actingAs($cashier)
        ->post(route('admin.pos.store'), [
            'items' => [
                ['spare_part_id' => $part->id, 'qty' => 99],
            ],
            'payment_method' => 'cash',
        ]);

    $response->assertStatus(302);
    expect(session('error'))->not->toBeNull('Harus ada pesan error stok tidak cukup');
    expect(SalesOrder::where('type', SalesOrderType::Pos)->exists())->toBeFalse();
    expect((int) $part->fresh()->stock)->toBe(2, 'Stok tidak boleh berubah');
});

test('pencarian produk POS mengembalikan JSON hasil pencocokan nama/sku', function () {
    $cashier = User::factory()->cashier()->create();
    SparePart::factory()->active()->create(['name' => 'Kampas Rem Brembo', 'sku' => 'SP-BREMBO']);

    $response = $this->actingAs($cashier)
        ->getJson(route('admin.pos.search', ['search' => 'Brembo']));

    $response->assertOk();
    $json = $response->json();
    expect($json)->toBeArray();
    expect(collect($json)->contains(fn ($p) => (string) str($p['sku'] ?? '')->startsWith('SP-BREMBO')))->toBeTrue();
});

test('pembayaran work order di kasir: sales order + invoice dibuat, WO delivered, idempotent', function () {
    $cashier = User::factory()->cashier()->create();
    $workOrder = WorkOrder::factory()->create([
        'status' => WorkOrderStatus::Completed,
        'total_jasa_idr' => 2_000_000,
        'total_part_idr' => 500_000,
        'total_idr' => 2_500_000,
    ]);

    $response = $this->actingAs($cashier)
        ->post(route('admin.pos.work-orders.pay', $workOrder), [
            'payment_method' => 'transfer',
        ]);

    $salesOrder = SalesOrder::where('type', SalesOrderType::Service)->latest('id')->first();
    expect($salesOrder)->not->toBeNull('Pembayaran WO seharusnya membuat sales order servis');
    $response->assertRedirect(route('admin.pos.receipt', $salesOrder));

    // subtotal 2.500.000 - 0 + PPN 11% = 2.775.000
    expect((int) $salesOrder->total_idr)->toBe(2_775_000);
    expect(Invoice::where('invoiceable_id', $salesOrder->id)->count())->toBe(1);
    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::Delivered, 'WO lunas langsung statusnya delivered');

    // Bayar kedua → idempotent, tidak membuat SalesOrder/invoice baru
    $this->actingAs($cashier)
        ->post(route('admin.pos.work-orders.pay', $workOrder), [
            'payment_method' => 'transfer',
        ]);

    expect(SalesOrder::where('type', SalesOrderType::Service)->count())->toBe(1);
    expect(Invoice::where('invoiceable_id', $salesOrder->id)->count())->toBe(1);
});
