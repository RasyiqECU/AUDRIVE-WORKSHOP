<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\Review;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Voucher;
use App\Services\Notification\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Fase 4 audit — Portal Customer:
 * kendaraan, tiket bantuan, tukar poin loyalitas, klaim voucher, review, notifikasi.
 */
function portalUser(array $customerAttrs = []): array
{
    $user = User::factory()->customer()->create();
    $customer = Customer::factory()->create(array_merge(['user_id' => $user->id], $customerAttrs));

    $user->refresh();

    return ['user' => $user, 'customer' => $customer];
}

test('customer menambah, mengubah, menghapus kendaraannya sendiri', function () {
    ['user' => $user, 'customer' => $customer] = portalUser();

    $this->actingAs($user)
        ->post(route('customer.vehicles.store'), [
            'brand' => 'Porsche',
            'model' => '911 Carrera',
            'year' => 2024,
            'color' => 'Hitam',
            'plate_number' => 'PO 911 AA',
        ])
        ->assertRedirect(route('customer.vehicles.index'));

    $vehicle = $customer->vehicles()->first();
    expect($vehicle)->not->toBeNull('Kendaraan harus tersimpan milik customer');
    expect($vehicle->brand)->toBe('Porsche');

    // Update kendaraan sendiri
    $this->actingAs($user)
        ->patch(route('customer.vehicles.update', $vehicle), [
            'brand' => 'Porsche',
            'model' => '911 Turbo',
            'year' => 2024,
        ])
        ->assertRedirect(route('customer.vehicles.index'));
    expect($vehicle->fresh()->model)->toBe('911 Turbo');

    // Delete kendaraan sendiri
    $this->actingAs($user)
        ->delete(route('customer.vehicles.destroy', $vehicle))
        ->assertRedirect(route('customer.vehicles.index'));
    expect(CustomerVehicle::find($vehicle->id))->toBeNull();
});

test('customer tidak boleh akses kendaraan & tiket milik customer lain', function () {
    ['user' => $user] = portalUser();
    $otherVehicle = CustomerVehicle::factory()->create();
    $otherTicket = SupportTicket::factory()->open()->create(['customer_id' => Customer::factory()->create()->id]);

    $this->actingAs($user)->get(route('customer.vehicles.edit', $otherVehicle))->assertStatus(403);
    $this->actingAs($user)->get(route('customer.tickets.show', $otherTicket))->assertStatus(403);
});

test('customer membuat tiket bantuan lalu membalas threadnya', function () {
    ['user' => $user, 'customer' => $customer] = portalUser();

    $this->actingAs($user)
        ->post(route('customer.tickets.store'), [
            'subject' => 'Rem bunyi nyaring',
            'priority' => 'high',
            'description' => 'Kampas rem saya berbunyi nyaring saat pengereman.',
        ])
        ->assertRedirect(route('customer.tickets.index'));

    $ticket = $customer->supportTickets()->first();
    expect($ticket)->not->toBeNull('Tiket harus tersimpan');
    expect($ticket->status->value ?? $ticket->status)->toBe('open');

    // Customer membalas tiket
    $this->actingAs($user)
        ->post(route('customer.tickets.reply', $ticket), [
            'message' => 'Berikut video bunyinya, mohon dicek.',
        ])
        ->assertStatus(302);
    expect($ticket->messages()->where('message', 'like', '%video bunyinya%')->exists())->toBeTrue();
});

test('customer menukar poin loyalitas dengan voucher dan gagal bila poin kurang', function () {
    ['user' => $user, 'customer' => $customer] = portalUser(['loyalty_points' => 200]);
    $voucher = Voucher::factory()->create([
        'is_active' => true,
        'points_cost' => 150,
        'max_usage' => 10,
        'used_count' => 0,
    ]);

    // Redeem sukses
    $this->actingAs($user)->from(route('customer.loyalty.index'))
        ->post(route('customer.loyalty.redeem'), ['voucher_id' => $voucher->id])
        ->assertStatus(302);

    expect((int) $customer->fresh()->loyalty_points)->toBe(50);
    expect($customer->voucherRedemptions()->where('voucher_id', $voucher->id)->exists())->toBeTrue();

    // Redeem kedua: poin cuma 50 → ditolak, tidak bertambah transaksi
    $before = $customer->loyaltyTransactions()->count();
    $this->actingAs($user)->from(route('customer.loyalty.index'))
        ->post(route('customer.loyalty.redeem'), ['voucher_id' => $voucher->id])
        ->assertStatus(302);
    expect(session('error'))->not->toBeNull('Redeem poin kurang harus error');
    expect((int) $customer->fresh()->loyalty_points)->toBe(50);
    expect($customer->loyaltyTransactions()->count())->toBe($before);
});

test('customer klaim voucher aktif dan gagal bila voucher habis', function () {
    ['user' => $user, 'customer' => $customer] = portalUser();

    $voucher = Voucher::factory()->create(['is_active' => true, 'max_usage' => 5, 'used_count' => 4]);

    $this->actingAs($user)->from(route('customer.vouchers.index'))
        ->post(route('customer.vouchers.claim', $voucher))
        ->assertStatus(302);

    expect((int) $voucher->fresh()->used_count)->toBe(5);
    expect($customer->voucherRedemptions()->count())->toBe(1);

    // Voucher sudah habis kuota → klaim ditolak
    $this->actingAs($user)->from(route('customer.vouchers.index'))
        ->post(route('customer.vouchers.claim', $voucher))
        ->assertStatus(302);
    expect(session('error'))->not->toBeNull('Voucher habis kuota harus error');
    expect($customer->voucherRedemptions()->count())->toBe(1, 'Tidak boleh ada klaim dobel');
    expect((int) $voucher->fresh()->used_count)->toBe(5);
});

test('customer mengirim ulasan dan statusnya approved', function () {
    ['user' => $user] = portalUser();

    $this->actingAs($user)
        ->post(route('customer.reviews.store'), [
            'rating' => 5,
            'comment' => 'Pelayanan sangat profesional dan hasil puas.',
        ])
        ->assertRedirect(route('customer.reviews.index'));

    $customer = $user->customer;
    $review = Review::where('reviewer_id', $customer->id)->first();
    expect($review)->not->toBeNull('Review harus tersimpan');
    expect($review->status)->toBe('approved');
    expect((int) $review->rating)->toBe(5);
});

test('notifikasi: menandai satu dibaca dan menandai semua dibaca', function () {
    ['user' => $user] = portalUser();

    /** @var NotificationService $notificationService */
    $notificationService = app(NotificationService::class);
    $notificationService->send($user, 'work_order_delivered', ['title' => 'A', 'message' => 'x']);
    $notificationService->send($user, 'payment_success', ['title' => 'B', 'message' => 'y']);

    expect($user->unreadNotifications()->count())->toBe(2);

    $first = $user->unreadNotifications()->first();
    $this->actingAs($user)->from(route('customer.notifications.index'))
        ->patch(route('customer.notifications.read', $first->id))
        ->assertStatus(302);
    expect($user->unreadNotifications()->count())->toBe(1);

    $this->actingAs($user)->from(route('customer.notifications.index'))
        ->post(route('customer.notifications.read-all'))
        ->assertStatus(302);
    expect($user->unreadNotifications()->count())->toBe(0);
});
