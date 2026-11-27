<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Enums\WorkOrderStatus;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\WarrantyClaim;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;

function deliveredService(int $daysAgo = 5, ?string $partSource = null): WorkOrderItem
{
    $vehicle = CustomerVehicle::factory()->create(['customer_id' => Customer::factory()->create(['user_id' => User::factory()->customer()])->id]);
    $booking = ServiceBooking::factory()->create([
        'customer_id' => $vehicle->customer_id,
        'customer_vehicle_id' => $vehicle->id,
        'odometer_km' => 10_000,
    ]);
    $workOrder = WorkOrder::factory()->create([
        'service_booking_id' => $booking->id,
        'customer_vehicle_id' => $vehicle->id,
        'status' => WorkOrderStatus::Delivered,
        'paid_at' => now()->subDays($daysAgo),
        'delivered_at' => now()->subDays($daysAgo),
    ]);

    return WorkOrderItem::create([
        'work_order_id' => $workOrder->id,
        'type' => 'service',
        'description' => 'Ganti kampas rem',
        'qty' => 1,
        'unit_price_idr' => 500_000,
        'subtotal_idr' => 500_000,
        'part_source' => $partSource,
    ]);
}

function claimPayload(WorkOrderItem $item, int $currentKm = 10_300): array
{
    return [
        'number' => $item->workOrder->code,
        'work_order_item_id' => $item->id,
        'current_km' => $currentKm,
        'complaint' => 'Rem bunyi lagi',
    ];
}

test('alur klaim garansi: sales → mekanik → manager → inventory → QC → nota Rp 0 → serah terima', function () {
    $item = deliveredService();
    $sales = User::factory()->sales()->create();
    $mechanic = User::factory()->mechanic()->create();
    $manager = User::factory()->manager()->create();
    $inventory = User::factory()->inventory()->create();
    $cashier = User::factory()->cashier()->create();
    $part = SparePart::factory()->active()->create(['stock' => 3]);

    $this->actingAs($sales)->post(route('admin.warranty-claims.store'), claimPayload($item))->assertSessionHasNoErrors();
    $claim = WarrantyClaim::firstOrFail();

    $this->actingAs($mechanic)->post(route('admin.warranty-claims.diagnose', $claim), ['is_covered' => 1, 'diagnosis' => 'Pemasangan kurang kencang']);
    $this->actingAs($manager)->post(route('admin.warranty-claims.decide', $claim), ['decision' => 'approve']);
    $repair = $claim->fresh()->repairWorkOrder;
    expect($repair->is_warranty)->toBeTrue()
        ->and($repair->assigned_mechanic_id)->toBe($mechanic->id);

    $this->actingAs($inventory)->post(route('admin.warranty-claims.parts', $claim), ['spare_part_id' => $part->id, 'qty' => 1]);
    expect($part->fresh()->stock)->toBe(2)
        ->and(StockMovement::where('type', StockMovementType::Warranty)->count())->toBe(1);

    $this->actingAs($mechanic)->patch(route('admin.work-orders.status', $repair), ['status' => 'in_progress']);
    $this->actingAs($mechanic)->patch(route('admin.work-orders.status', $repair), ['status' => 'quality_control']);
    $this->actingAs($mechanic)->post(route('admin.work-orders.complete', $repair));
    $this->actingAs($cashier)->get(route('admin.pos.index'))->assertSee($repair->code);
    $this->actingAs($cashier)->post(route('admin.pos.work-orders.pay', $repair), ['payment_method' => 'cash'])->assertSessionHasNoErrors();
    $this->actingAs($sales)->post(route('admin.work-orders.deliver', $repair));

    expect($repair->fresh()->status)->toBe(WorkOrderStatus::Delivered)
        ->and($repair->fresh()->total_idr)->toBe(0)
        ->and($claim->fresh()->statusLabel())->toBe('Selesai & diserahkan');
    $this->actingAs($item->workOrder->vehicle->customer->user)
        ->get(route('customer.warranty.index'))
        ->assertOk()
        ->assertSee($claim->code);
});

test('klaim ditolak bila di luar masa, di luar kilometer, atau part bawaan customer', function (Closure $item, int $currentKm) {
    $this->actingAs(User::factory()->sales()->create())
        ->post(route('admin.warranty-claims.store'), claimPayload($item(), $currentKm))
        ->assertSessionHas('error');

    expect(WarrantyClaim::count())->toBe(0);
})->with([
    'lewat 30 hari' => [fn () => deliveredService(31), 10_300],
    'lewat 1.000 km' => [fn () => deliveredService(), 11_001],
    'part bawaan customer' => [fn () => deliveredService(partSource: 'customer'), 10_300],
]);

test('klaim ganda diblokir selama klaim sebelumnya masih berjalan', function () {
    $item = deliveredService();
    $sales = User::factory()->sales()->create();

    $this->actingAs($sales)->post(route('admin.warranty-claims.store'), claimPayload($item));
    $this->actingAs($sales)->post(route('admin.warranty-claims.store'), claimPayload($item))->assertSessionHas('error');

    expect(WarrantyClaim::count())->toBe(1);
});

test('penolakan klaim wajib disertai alasan dan tidak membuat work order', function () {
    $claim = WarrantyClaim::factory()->create(['status' => 'diagnosed']);
    $manager = User::factory()->manager()->create();

    $this->actingAs($manager)
        ->post(route('admin.warranty-claims.decide', $claim), ['decision' => 'reject'])
        ->assertSessionHasErrors('rejection_reason');
    $this->actingAs($manager)
        ->post(route('admin.warranty-claims.decide', $claim), ['decision' => 'reject', 'rejection_reason' => 'Akibat benturan']);

    expect($claim->fresh()->status)->toBe('rejected')
        ->and($claim->fresh()->repair_work_order_id)->toBeNull();
});
