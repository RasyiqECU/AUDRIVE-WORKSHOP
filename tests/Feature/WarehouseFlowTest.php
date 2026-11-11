<?php

declare(strict_types=1);

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Fase 4 audit — alur fitur Gudang (Super Admin & Inventory):
 * katalog spare part CRUD, penyesuaian stok, PO draft → dikirim → diterima / dibatalkan.
 */
test('inventory membuat spare part baru dan menyesuaikan stok dengan movement adjustment', function () {
    $inventory = User::factory()->inventory()->create();
    $category = SparePartCategory::factory()->create();

    // Membuat master data
    $this->actingAs($inventory)
        ->post(route('admin.spare-parts.store'), [
            'sku' => 'SP-TEST-001',
            'name' => 'Kampas Rem Brembo R93',
            'category_id' => $category->id,
            'brand' => 'Brembo',
            'price_idr' => 850_000,
            'stock' => 25,
            'low_stock_threshold' => 5,
        ])
        ->assertStatus(302);

    $part = SparePart::where('sku', 'SP-TEST-001')->first();
    expect($part)->not->toBeNull('Spare part seharusnya tersimpan');
    expect((int) $part->stock)->toBe(25);

    // Penyesuaian stok 25 → 50, movement Adjustment qty 45
    $this->actingAs($inventory)
        ->post(route('admin.inventory.adjust-stock', $part), [
            'new_stock' => 50,
            'notes' => 'Koreksi hasil stock opname',
        ])
        ->assertStatus(302);

    expect((int) $part->fresh()->stock)->toBe(50);

    $movement = StockMovement::where('spare_part_id', $part->id)->where('type', 'adjustment')->first();
    expect($movement)->not->toBeNull('Penyesuaian stok harus mencatat movement adjustment');
    expect((int) $movement->qty)->toBe(25);

    // Halaman movements & low-stock bisa dibuka
    $this->actingAs($inventory)->withSession([])->from(route('admin.inventory.edit', $part))
        ->post(route('admin.inventory.adjust-stock', $part), ['new_stock' => 50]) // sama → error, tetap 302
        ->assertStatus(302);
    expect((int) $part->fresh()->stock)->toBe(50, 'Stok tidak boleh berubah jika sama');

    $this->actingAs($inventory)->get(route('admin.inventory.movements', $part))->assertOk();
    $this->actingAs($inventory)->get(route('admin.inventory.low-stock'))->assertOk();
});

test('flow PO penuh: draft → dikirim → diterima sebagian → diterima penuh', function () {
    $inventory = User::factory()->inventory()->create();
    $part = SparePart::factory()->active()->create(['stock' => 10]);
    $item = null;

    // 1. Buat PO draft
    $this->actingAs($inventory)
        ->post(route('admin.purchase-orders.store'), [
            'supplier_name' => 'PT Brembo Indonesia',
            'notes' => 'Pengadaan suku cadang performa',
            'items' => [
                ['spare_part_id' => $part->id, 'qty' => 10, 'cost_idr' => 500_000],
            ],
        ])
        ->assertStatus(302);

    $po = PurchaseOrder::latest('id')->first();
    expect($po)->not->toBeNull();
    expect($po->status)->toBe(PurchaseOrderStatus::Draft);
    expect((int) $po->total_idr)->toBe(5_000_000);
    expect((int) $po->created_by)->toBe($inventory->id);

    // 2. Kirim ke supplier
    $this->actingAs($inventory)->post(route('admin.purchase-orders.send', $po))->assertStatus(302);
    expect($po->fresh()->status)->toBe(PurchaseOrderStatus::Sent);
    expect($po->fresh()->ordered_at)->not->toBeNull();

    $item = PurchaseOrderItem::where('purchase_order_id', $po->id)->first();

    // 3. Terima sebagian (4 dari 10) → stok naik 4
    $this->actingAs($inventory)
        ->post(route('admin.purchase-orders.receive', $po), [
            'items' => [$item->id => ['received_qty' => 4]],
        ]);
    expect($po->fresh()->status)->toBe(PurchaseOrderStatus::PartiallyReceived);
    expect((int) $part->fresh()->stock)->toBe(14);

    // Qty melebihi pesanan ditolak
    $this->actingAs($inventory)->from(route('admin.purchase-orders.show', $po))
        ->post(route('admin.purchase-orders.receive', $po), [
            'items' => [$item->id => ['received_qty' => 10]],
        ])
        ->assertStatus(302);
    expect((int) $part->fresh()->stock)->toBe(14);

    // 4. Terima sisa (6) → Received
    $this->actingAs($inventory)->from(route('admin.purchase-orders.show', $po))
        ->post(route('admin.purchase-orders.receive', $po), [
            'items' => [$item->id => ['received_qty' => 6]],
        ]);
    expect($po->fresh()->status)->toBe(PurchaseOrderStatus::Received);
    expect((int) $part->fresh()->stock)->toBe(20);
    expect($po->fresh()->received_at)->not->toBeNull();

    // PO sudah diterima tidak bisa dibatalkan
    $response = $this->actingAs($inventory)->from(route('admin.purchase-orders.show', $po))
        ->post(route('admin.purchase-orders.cancel', $po));
    expect(session('error'))->not->toBeNull('PO received harus error saat dicancel');
    expect($po->fresh()->status)->toBe(PurchaseOrderStatus::Received);
});

test('PO draft bisa dibatalkan dan PO belum dikirim tidak bisa diterima', function () {
    $inventory = User::factory()->inventory()->create();
    $part = SparePart::factory()->active()->create(['stock' => 0]);

    $this->actingAs($inventory)
        ->post(route('admin.purchase-orders.store'), [
            'supplier_name' => 'Bosch Racing Supply',
            'items' => [
                ['spare_part_id' => $part->id, 'qty' => 5, 'cost_idr' => 400_000],
            ],
        ]);

    $po = PurchaseOrder::latest('id')->first();

    // PO belum dikirim → penerimaan ditolak, stok tidak berubah
    $this->actingAs($inventory)->from(route('admin.purchase-orders.show', $po))
        ->post(route('admin.purchase-orders.receive', $po), [
            'items' => [PurchaseOrderItem::where('purchase_order_id', $po->id)->first()->id => ['received_qty' => 5]],
        ]);
    expect($po->fresh()->status)->toBe(PurchaseOrderStatus::Draft);
    expect((int) $part->fresh()->stock)->toBe(0);

    // Cancel draft → Cancelled
    $this->actingAs($inventory)->from(route('admin.purchase-orders.show', $po))
        ->post(route('admin.purchase-orders.cancel', $po))
        ->assertStatus(302);
    expect($po->fresh()->status)->toBe(PurchaseOrderStatus::Cancelled);
});
