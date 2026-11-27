<?php

declare(strict_types=1);

use App\Enums\SalesOrderStatus;
use App\Enums\WorkOrderStatus;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/** @return array{0: WorkOrder, 1: User, 2: User} work order, pemilik, kepala mekanik */
function serviceInProgress(): array
{
    $owner = User::factory()->customer()->create();
    $customer = Customer::factory()->create(['user_id' => $owner->id, 'loyalty_points' => 0]);
    $mechanic = User::factory()->mechanic()->create();
    $workOrder = WorkOrder::factory()->inProgress()->create([
        'customer_vehicle_id' => CustomerVehicle::factory()->create(['customer_id' => $customer->id])->id,
        'assigned_mechanic_id' => $mechanic->id,
        'total_jasa_idr' => 0,
        'total_part_idr' => 0,
        'total_idr' => 0,
    ]);

    return [$workOrder, $owner, $mechanic];
}

test('usulan part mengubah status menunggu dan customer memilih sendiri dari portal', function () {
    [$workOrder, $owner, $mechanic] = serviceInProgress();
    $part = SparePart::factory()->active()->create(['stock' => 5, 'price_idr' => 200_000]);

    $this->actingAs($mechanic)->post(route('admin.work-orders.part-items.store', $workOrder), ['spare_part_id' => $part->id, 'qty' => 1]);
    $item = $workOrder->items()->firstOrFail();
    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::WaitingPartConfirmation);

    $this->actingAs(User::factory()->inventory()->create())
        ->post(route('admin.part-requests.check', $item), ['install_fee_idr' => 60_000]);
    $this->actingAs($owner)->get(route('workshop.tracking.show', $workOrder))->assertSee('Konfirmasi Suku Cadang Tambahan');
    $this->actingAs(User::factory()->customer()->create())
        ->post(route('workshop.tracking.parts.decide', $item), ['decision' => 'buy'])
        ->assertForbidden();

    // Biaya jasa pasang tidak bisa diubah customer
    $this->actingAs($owner)->post(route('workshop.tracking.parts.decide', $item), ['decision' => 'bring_own', 'install_fee_idr' => 0]);

    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::InProgress)
        ->and($workOrder->fresh()->total_idr)->toBe(60_000)
        ->and($part->fresh()->stock)->toBe(5);
});

test('tagihan terbit saat lolos QC dan bisa dibayar transfer lalu mobil diserahkan', function () {
    Storage::fake('local');
    [$workOrder, $owner, $mechanic] = serviceInProgress();
    $cashier = User::factory()->cashier()->create();
    $sales = User::factory()->sales()->create();

    $this->actingAs($mechanic)->post(route('admin.work-orders.service-items.store', $workOrder), ['description' => 'Tune up', 'qty' => 1, 'unit_price_idr' => 1_000_000]);
    $this->actingAs($mechanic)->patch(route('admin.work-orders.status', $workOrder), ['status' => 'quality_control']);
    $this->actingAs($mechanic)->post(route('admin.work-orders.complete', $workOrder));

    $bill = SalesOrder::where('work_order_id', $workOrder->id)->firstOrFail();
    expect($bill->status)->toBe(SalesOrderStatus::Pending)
        ->and($bill->total_idr)->toBe(1_110_000);

    $this->actingAs($owner)->post(route('workshop.tracking.pay-online', $workOrder))
        ->assertRedirect(route('parts.checkout.payment', $bill));
    $this->actingAs($owner)->post(route('parts.orders.proof', $bill), [
        'proof' => UploadedFile::fake()->image('bukti.png'),
        'sender_name' => 'Pemilik',
        'destination_bank' => 'BCA',
        'amount_idr' => 1_110_000,
    ])->assertRedirect(route('workshop.tracking.show', $workOrder));

    // Kasir tidak boleh menagih dua kali selagi bukti menunggu verifikasi
    $this->actingAs($cashier)->post(route('admin.pos.work-orders.pay', $workOrder), ['payment_method' => 'cash'])->assertSessionHas('error');
    $this->actingAs($sales)->post(route('admin.work-orders.deliver', $workOrder))->assertSessionHas('error');

    $this->actingAs($cashier)->post(route('admin.payment-verifications.approve', Payment::firstOrFail()));
    $this->actingAs($sales)->post(route('admin.work-orders.deliver', $workOrder));

    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::Delivered)
        ->and($workOrder->fresh()->paid_at)->not->toBeNull()
        ->and($bill->fresh()->status)->toBe(SalesOrderStatus::Paid)
        ->and($bill->invoice->status->value)->toBe('paid')
        ->and($owner->customer->fresh()->loyalty_points)->toBe(100)
        ->and(SalesOrder::where('work_order_id', $workOrder->id)->count())->toBe(1);
});
