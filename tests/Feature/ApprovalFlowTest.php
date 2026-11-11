<?php

use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Models\ApprovalRequest;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\ApprovalService;

function inventoryUser(): User
{
    return User::factory()->create(['role' => 'inventory']);
}

function makeApproval(string $type, array $payload, ?User $requester = null): ApprovalRequest
{
    return app(ApprovalService::class)->createRequest($type, $payload, $requester ?? inventoryUser());
}

beforeEach(function () {
    $this->manager = User::factory()->create(['role' => 'manager']);
    $this->cashier = User::factory()->create(['role' => 'cashier']);
    $this->sales = User::factory()->create(['role' => 'sales']);
});

// ── Pengajuan ──

test('staff can submit approval request and manager gets notified', function () {
    $order = SalesOrder::factory()->create(['total_idr' => 10_000_000]);

    $response = $this->actingAs($this->cashier)
        ->post(route('admin.approvals.store'), [
            'type' => 'discount',
            'sales_order_id' => $order->id,
            'amount_idr' => 1_500_000,
            'notes' => 'Diskon komplain customer, sudah dikoordinasi.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $approval = ApprovalRequest::query()->latest('id')->first();
    expect($approval->type)->toBe('discount')
        ->and($approval->status->value)->toBe('pending')
        ->and($approval->payload['sales_order_id'])->toBe($order->id)
        ->and($approval->payload['amount_idr'])->toBe(1_500_000);

    // Notifikasi approval_requested masuk ke Manager (bukan ke Cashier)
    expect($this->manager->fresh()->notifications()->where('data->type', 'approval_requested')->count())->toBe(1)
        ->and($this->cashier->fresh()->notifications()->where('data->type', 'approval_requested')->count())->toBe(0);
});

test('request without valid target combination is rejected', function () {
    $response = $this->actingAs(inventoryUser())
        ->post(route('admin.approvals.store'), [
            'type' => 'stock_adjustment',
            'notes' => 'opname',
        ]);

    $response->assertSessionHas('error');
    expect(ApprovalRequest::count())->toBe(0);
});

// ── Eksekusi saat disetujui ──

test('approved discount request reduces sales order total', function () {
    $order = SalesOrder::factory()->create([
        'subtotal_idr' => 10_000_000,
        'discount_idr' => 0,
        'tax_idr' => 0,
        'total_idr' => 10_000_000,
    ]);
    $approval = makeApproval('discount', ['sales_order_id' => $order->id, 'amount_idr' => 2_000_000]);

    $this->actingAs($this->manager)
        ->post(route('admin.approvals.approve', $approval), ['notes' => 'ok'])
        ->assertRedirect(route('admin.approvals.index'));

    $order->refresh();
    expect($approval->fresh()->status->value)->toBe('approved')
        ->and($order->discount_idr)->toBe(2_000_000)
        ->and($order->total_idr)->toBe(8_000_000);
});

test('approved refund request creates refund payment and reverses paid balance', function () {
    $order = SalesOrder::factory()->create([
        'status' => 'paid',
        'payment_status' => 'paid',
        'total_idr' => 5_000_000,
        'paid_idr' => 5_000_000,
    ]);
    $payment = Payment::factory()->success()->create([
        'paymentable_type' => SalesOrder::class,
        'paymentable_id' => $order->id,
        'amount_idr' => 5_000_000,
    ]);
    $approval = makeApproval('refund', ['payment_id' => $payment->id, 'amount_idr' => 5_000_000]);

    $this->actingAs($this->manager)
        ->post(route('admin.approvals.approve', $approval), ['notes' => 'batal bulan lalu'])
        ->assertRedirect();

    $order->refresh();
    $payment->refresh();

    $refund = Payment::where('method', 'refund')->where('paymentable_id', $order->id)->first();

    expect($refund)->not->toBeNull()
        ->and($refund->amount_idr)->toBe(5_000_000)
        ->and($refund->status)->toBe(PaymentStatus::Refunded)
        ->and($payment->status)->toBe(PaymentStatus::Refunded)
        ->and($order->paid_idr)->toBe(0)
        ->and($order->status->value)->toBe('pending');
});

test('approved price adjustment updates spare part price', function () {
    $part = SparePart::factory()->create(['price_idr' => 1_000_000]);
    $approval = makeApproval('price_adjustment', [
        'spare_part_id' => $part->id,
        'new_price_idr' => 1_250_000,
        'old_price_idr' => 1_000_000,
    ]);

    $this->actingAs($this->manager)
        ->post(route('admin.approvals.approve', $approval), ['notes' => null])
        ->assertRedirect();

    expect((int) $part->fresh()->price_idr)->toBe(1_250_000);
});

test('approved stock adjustment updates stock and records movement', function () {
    $part = SparePart::factory()->create(['stock' => 5]);
    $approval = makeApproval('stock_adjustment', [
        'spare_part_id' => $part->id,
        'new_stock' => 12,
        'old_stock' => 5,
    ]);

    $this->actingAs($this->manager)
        ->post(route('admin.approvals.approve', $approval), ['notes' => 'hasil opname'])
        ->assertRedirect();

    $part->refresh();
    expect($part->stock)->toBe(12);

    $movement = StockMovement::where('spare_part_id', $part->id)->where('type', StockMovementType::Adjustment->value)->first();
    expect($movement)->not->toBeNull()
        ->and($movement->qty)->toBe(7)
        ->and($movement->created_by)->toBe($this->manager->id);
});

// ── Penolakan & guard ──

test('reject records reason without side effects', function () {
    $part = SparePart::factory()->create(['stock' => 8]);
    $approval = makeApproval('stock_adjustment', [
        'spare_part_id' => $part->id,
        'new_stock' => 100,
        'old_stock' => 8,
    ]);

    $this->actingAs($this->manager)
        ->post(route('admin.approvals.reject', $approval), ['notes' => 'belum ada bukti opname'])
        ->assertRedirect();

    expect($approval->fresh()->status->value)->toBe('rejected')
        ->and($approval->fresh()->reason)->toBe('belum ada bukti opname')
        ->and((int) $part->fresh()->stock)->toBe(8);
});

test('requester cannot approve their own request', function () {
    $order = SalesOrder::factory()->create([
        'subtotal_idr' => 10_000_000,
        'discount_idr' => 0,
        'tax_idr' => 0,
        'total_idr' => 10_000_000,
    ]);
    $approval = makeApproval('discount', ['sales_order_id' => $order->id, 'amount_idr' => 1_000_000], $this->manager);

    $this->actingAs($this->manager)
        ->post(route('admin.approvals.approve', $approval), ['notes' => 'self'])
        ->assertRedirect(route('admin.approvals.index'))
        ->assertSessionHas('error');

    expect($approval->fresh()->status->value)->toBe('pending')
        ->and($order->fresh()->discount_idr)->toBe(0);
});

test('decided request cannot be approved twice', function () {
    $part = SparePart::factory()->create(['price_idr' => 100_000]);
    $approval = makeApproval('price_adjustment', ['spare_part_id' => $part->id, 'new_price_idr' => 90_000]);

    app(ApprovalService::class)->approve($approval, $this->manager, 'first time');

    $this->actingAs($this->manager)
        ->post(route('admin.approvals.approve', $approval), ['notes' => 'second time'])
        ->assertSessionHas('error');

    expect((int) $part->fresh()->price_idr)->toBe(90_000);
});

// ── Halaman ──

test('approval request form renders for requester roles and hides irrelevant types', function () {
    $this->actingAs($this->cashier)
        ->get(route('admin.approvals.create'))
        ->assertOk();
});

test('approval detail page renders for manager', function () {
    $approval = makeApproval('discount', ['sales_order_id' => 1, 'amount_idr' => 500_000]);

    $this->actingAs($this->manager)
        ->get(route('admin.approvals.show', $approval))
        ->assertOk();
});
