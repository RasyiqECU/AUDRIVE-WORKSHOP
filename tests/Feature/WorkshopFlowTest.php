<?php

declare(strict_types=1);

use App\Enums\WorkOrderStatus;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Fase 4 audit — alur fitur Workshop:
 * Customer booking → kasir konfirmasi → convert ke WO → manager assign mekanik
 * → mekanik item jasa/perekenspart → complete → kasir deliver.
 * Termasuk proteksi policy antar-role.
 */
function bookingFixture(array $overrides = []): array
{
    $customerUser = Customer::factory()->create()->user;

    $vehicle = CustomerVehicle::factory()->create([
        'customer_id' => $customerUser->customer->id,
    ]);

    $booking = ServiceBooking::factory()->pending()->create([
        'customer_id' => $customerUser->customer->id,
        'customer_vehicle_id' => $vehicle->id,
    ] + $overrides);

    return ['customerUser' => $customerUser, 'vehicle' => $vehicle, 'booking' => $booking];
}

test('flow penuh: check-in → WO → usul part → cek gudang → konfirmasi → QC → bayar → serah terima', function () {
    ['booking' => $booking] = bookingFixture();
    $sales = User::factory()->sales()->create();
    $mechanic = User::factory()->mechanic()->create();
    $inventory = User::factory()->inventory()->create();
    $cashier = User::factory()->cashier()->create();
    $part = SparePart::factory()->active()->create(['stock' => 10, 'price_idr' => 250_000]);

    // 1. Sales check-in
    $this->actingAs($sales)->post(route('admin.service-bookings.check-in', $booking), [
        'odometer_km' => 12_000,
        'body_condition' => 'Baret halus di pintu kiri',
    ]);
    expect($booking->fresh()->status->value)->toBe('checked_in');

    // 2. Kepala Mekanik membuat WO dan menugaskan mekanik
    $this->actingAs($mechanic)->post(route('admin.service-bookings.convert', $booking), [
        'mechanic_id' => $mechanic->id,
        'estimated_completion_at' => now()->addDay()->toDateTimeString(),
    ]);
    $workOrder = WorkOrder::where('service_booking_id', $booking->id)->firstOrFail();
    expect($workOrder->status)->toBe(WorkOrderStatus::Queued)
        ->and($workOrder->assigned_mechanic_id)->toBe($mechanic->id);

    // 3. Mekanik mengerjakan, menambah jasa, mengusulkan part (stok belum berkurang)
    $this->actingAs($mechanic)->patch(route('admin.work-orders.status', $workOrder), ['status' => 'in_progress']);
    $this->actingAs($mechanic)->post(route('admin.work-orders.service-items.store', $workOrder), [
        'description' => 'Servis rutin berkala',
        'qty' => 1,
        'unit_price_idr' => 1_000_000,
    ]);
    $this->actingAs($mechanic)->post(route('admin.work-orders.part-items.store', $workOrder), [
        'spare_part_id' => $part->id,
        'qty' => 2,
    ]);
    $item = $workOrder->items()->where('type', 'part')->firstOrFail();
    expect($item->approval_status)->toBe('pending')
        ->and($workOrder->fresh()->total_idr)->toBe(1_000_000)
        ->and($part->fresh()->stock)->toBe(10);

    // Selama menunggu konfirmasi, tidak bisa masuk QC
    $this->actingAs($mechanic)->patch(route('admin.work-orders.status', $workOrder), ['status' => 'quality_control']);
    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::WaitingPartConfirmation);

    // 4. Sales tidak bisa mencatat sebelum gudang mengecek
    $this->actingAs($sales)->post(route('admin.part-requests.decide', $item), ['decision' => 'buy'])->assertSessionHas('error');
    $this->actingAs($inventory)->post(route('admin.part-requests.check', $item));
    $this->actingAs($sales)->get(route('admin.part-requests.index'))->assertOk()->assertSee('wa.me', false);
    $this->actingAs($sales)->post(route('admin.part-requests.decide', $item), ['decision' => 'buy']);
    expect($workOrder->fresh()->total_idr)->toBe(1_500_000)
        ->and($part->fresh()->stock)->toBe(8)
        ->and(StockMovement::where('spare_part_id', $part->id)->where('type', 'out')->exists())->toBeTrue();

    // 5. Mekanik tidak bisa menyelesaikan sendiri; Manager gagal-kan lalu loloskan QC
    $this->actingAs($mechanic)->patch(route('admin.work-orders.status', $workOrder), ['status' => 'quality_control']);
    $this->actingAs(User::factory()->manager()->create())->post(route('admin.work-orders.complete', $workOrder))->assertForbidden();
    $this->actingAs($mechanic)->patch(route('admin.work-orders.status', $workOrder), ['status' => 'in_progress']);
    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::InProgress);

    // Pembayaran ditolak sebelum lolos QC
    $this->actingAs($cashier)->post(route('admin.pos.work-orders.pay', $workOrder), ['payment_method' => 'cash'])->assertSessionHas('error');

    $this->actingAs($mechanic)->patch(route('admin.work-orders.status', $workOrder), ['status' => 'quality_control']);
    $this->actingAs($mechanic)->post(route('admin.work-orders.complete', $workOrder));
    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::Completed)
        ->and($booking->fresh()->status->value)->toBe('completed');

    // 6. Serah terima wajib lunas
    $this->actingAs($sales)->post(route('admin.work-orders.deliver', $workOrder))->assertSessionHas('error');
    $this->actingAs($cashier)->post(route('admin.pos.work-orders.pay', $workOrder), ['payment_method' => 'cash']);
    $this->actingAs($sales)->post(route('admin.work-orders.deliver', $workOrder));

    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::Delivered)
        ->and($workOrder->fresh()->delivered_at)->not->toBeNull();
});

test('part bawaan customer hanya ditagih jasa pasang dan part yang ditolak tidak ditagih', function () {
    $workOrder = WorkOrder::factory()->inProgress()->create(['total_idr' => 0, 'total_part_idr' => 0, 'total_jasa_idr' => 0]);
    $mechanic = User::factory()->mechanic()->create();
    $workOrder->update(['assigned_mechanic_id' => $mechanic->id]);
    $sales = User::factory()->sales()->create();
    $parts = SparePart::factory()->active()->count(2)->create(['stock' => 5]);

    foreach ($parts as $part) {
        $this->actingAs($mechanic)->post(route('admin.work-orders.part-items.store', $workOrder), ['spare_part_id' => $part->id, 'qty' => 1]);
    }
    $workOrder->items()->update(['inventory_checked_at' => now()]);
    [$own, $skipped] = $workOrder->items()->orderBy('id')->get()->all();

    $this->actingAs($sales)->post(route('admin.part-requests.decide', $own), ['decision' => 'bring_own', 'install_fee_idr' => 75_000]);
    $this->actingAs($sales)->post(route('admin.part-requests.decide', $skipped), ['decision' => 'skip'])->assertSessionHasErrors('note');
    $this->actingAs($sales)->post(route('admin.part-requests.decide', $skipped), ['decision' => 'skip', 'note' => 'Nanti saja']);

    expect($workOrder->fresh()->total_idr)->toBe(75_000)
        ->and($workOrder->fresh()->total_part_idr)->toBe(0)
        ->and($own->fresh()->part_source)->toBe('customer')
        ->and($skipped->fresh()->approval_status)->toBe('rejected')
        ->and($parts->sum(fn ($part) => $part->fresh()->stock))->toBe(10);
});

test('menghapus item part yang sudah disetujui mengembalikan stok dan mencatat movement reversal', function () {
    $workOrder = WorkOrder::factory()->inProgress()->state(fn () => ['total_idr' => 0, 'total_part_idr' => 0, 'total_jasa_idr' => 0])->create();
    $part = SparePart::factory()->active()->create(['stock' => 5]);
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)->post(route('admin.work-orders.part-items.store', $workOrder), ['spare_part_id' => $part->id, 'qty' => 3]);
    $item = $workOrder->items()->firstOrFail();
    $this->actingAs($superAdmin)->post(route('admin.part-requests.check', $item));
    $this->actingAs($superAdmin)->post(route('admin.part-requests.decide', $item), ['decision' => 'buy']);
    expect($part->fresh()->stock)->toBe(2);

    $this->actingAs($superAdmin)->delete(route('admin.work-orders.items.destroy', $item))->assertStatus(302);

    expect($part->fresh()->stock)->toBe(5)
        ->and($workOrder->items()->count())->toBe(0);
});

test('sales membuat booking walk-in untuk customer dan kendaraan baru', function () {
    $this->actingAs(User::factory()->sales()->create())
        ->post(route('admin.service-bookings.store'), [
            'name' => 'Budi Walkin',
            'email' => 'budi.walkin@example.com',
            'phone' => '081200000001',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'year' => 2020,
            'service_type' => 'regular_service',
        ])
        ->assertSessionHasNoErrors();

    $booking = ServiceBooking::latest('id')->firstOrFail();
    expect($booking->customer->user->name)->toBe('Budi Walkin')
        ->and($booking->vehicle->model)->toBe('Avanza')
        ->and($booking->status->value)->toBe('confirmed');
});

test('mekanik tidak bisa deliver, kasir tidak bisa update status, mekanik lihat WO hanya milik sendiri', function () {
    $mechanic = User::factory()->mechanic()->create();
    $cashier = User::factory()->cashier()->create();
    $otherMechanic = User::factory()->mechanic()->create();

    $workOrder = WorkOrder::factory()->inProgress()->create();
    $workOrder->update(['assigned_mechanic_id' => $otherMechanic->id]);

    // Mekanik tidak berwenang deliver (route hanya super-admin & kasir)
    $this->actingAs($mechanic)
        ->post(route('admin.work-orders.deliver', $workOrder))
        ->assertStatus(403);

    // Kasir tidak boleh update status work order (policy update)
    $this->actingAs($cashier)
        ->patch(route('admin.work-orders.status', $workOrder), ['status' => 'delivered'])
        ->assertStatus(403);

    // Mekanik hanya melihat WO yang di-assign ke dirinya
    $this->actingAs($mechanic)
        ->get(route('admin.work-orders.show', $workOrder))
        ->assertStatus(403);
});
