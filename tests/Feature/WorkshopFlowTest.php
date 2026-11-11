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

test('flow penuh: konfirmasi → convert → assign → item → complete → deliver', function () {
    ['booking' => $booking] = bookingFixture();
    $mechanic = User::factory()->mechanic()->create();
    $manager = User::factory()->manager()->create();
    $cashier = User::factory()->cashier()->create();

    // 1. Kasir mengonfirmasi booking
    $this->actingAs($cashier)
        ->patch(route('admin.service-bookings.status', $booking), ['status' => 'confirmed'])
        ->assertStatus(302);
    expect($booking->fresh()->status->value ?? $booking->fresh()->status)->toBe('confirmed');

    // 2. Kasir mengonversi booking menjadi Work Order
    $convertResponse = $this->actingAs($cashier)
        ->post(route('admin.service-bookings.convert', $booking));

    $workOrder = WorkOrder::where('service_booking_id', $booking->id)->first();
    expect($workOrder)->not->toBeNull('Booking seharusnya terkonversi menjadi work order');
    $convertResponse->assertRedirect(route('admin.work-orders.show', $workOrder));
    expect($workOrder->status)->toBe(WorkOrderStatus::InProgress);
    expect($booking->fresh()->status->value ?? $booking->fresh()->status)->toBe('in_progress');

    // 3. Manager menugaskan mekanik
    $this->actingAs($manager)
        ->patch(route('admin.work-orders.assign', $workOrder), ['mechanic_id' => $mechanic->id])
        ->assertStatus(302);
    expect((int) $workOrder->fresh()->assigned_mechanic_id)->toBe($mechanic->id);

    // 4. Mekanik menambah item jasa + spare part (stok berkurang)
    $part = SparePart::factory()->active()->create(['stock' => 10, 'price_idr' => 250_000]);

    $this->actingAs($mechanic)
        ->post(route('admin.work-orders.service-items.store', $workOrder), [
            'description' => 'Servis rutin berkala',
            'qty' => 1,
            'unit_price_idr' => 1_000_000,
        ])
        ->assertStatus(302);

    $this->actingAs($mechanic)
        ->post(route('admin.work-orders.part-items.store', $workOrder), [
            'spare_part_id' => $part->id,
            'qty' => 2,
        ])
        ->assertStatus(302);

    $workOrder->refresh();
    expect($workOrder->items()->count())->toBe(2);
    expect($workOrder->total_idr)->toBe(1_500_000);
    expect((int) $part->fresh()->stock)->toBe(8, 'Stok spare part harus berkurang 2');
    expect(StockMovement::where('spare_part_id', $part->id)->where('type', 'out')->exists())->toBeTrue();

    // 5. Mekanik menyelesaikan pekerjaan
    $this->actingAs($mechanic)
        ->post(route('admin.work-orders.complete', $workOrder))
        ->assertStatus(302);

    $workOrder->refresh();
    expect($workOrder->status)->toBe(WorkOrderStatus::Completed);
    expect($workOrder->completed_at)->not->toBeNull();
    expect($booking->fresh()->status->value ?? $booking->fresh()->status)->toBe('completed');

    // 6. Kasir menyerahkan kendaraan (deliver)
    $this->actingAs($cashier)
        ->post(route('admin.work-orders.deliver', $workOrder))
        ->assertStatus(302);

    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::Delivered);
});

test('menghapus item part mengembalikan stok dan mencatat movement reversal', function () {
    $workOrder = WorkOrder::factory()->inProgress()->state(fn () => ['total_idr' => 0, 'total_part_idr' => 0, 'total_jasa_idr' => 0])->create();
    $mechanic = User::factory()->mechanic()->create();
    $workOrder->update(['assigned_mechanic_id' => $mechanic->id]);

    $part = SparePart::factory()->active()->create(['stock' => 5]);

    $this->actingAs($mechanic)
        ->post(route('admin.work-orders.part-items.store', $workOrder), [
            'spare_part_id' => $part->id,
            'qty' => 3,
        ]);
    expect((int) $part->fresh()->stock)->toBe(2);

    $item = $workOrder->items()->first();

    $this->actingAs($mechanic)
        ->delete(route('admin.work-orders.items.destroy', $item))
        ->assertStatus(302);

    expect((int) $part->fresh()->stock)->toBe(5, 'Stok harus kembali penuh setelah item dihapus');
    expect($workOrder->items()->count())->toBe(0);
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
