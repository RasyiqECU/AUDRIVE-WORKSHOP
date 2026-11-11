<?php

use App\Models\Customer;
use App\Models\User;
use App\Services\Notification\NotificationService;

beforeEach(function () {
    $this->service = app(NotificationService::class);
});

function roleUser(string $role): User
{
    return User::factory()->create(['role' => $role]);
}

// ── Matriks relevansi: notifikasi tidak boleh "nyasar" ──

test('payment_success never reaches mechanic despite being requested', function () {
    $mechanic = roleUser('mechanic');
    $cashier = roleUser('cashier');
    $sales = roleUser('sales');
    $superAdmin = roleUser('super-admin');

    $this->service->notifyRoles(['mechanic', 'cashier', 'sales', 'super-admin'], 'payment_success', [
        'title' => 'Pembayaran Berhasil',
        'message' => 'Pembayaran invoice customer berhasil diproses.',
    ]);

    expect($mechanic->notifications()->where('data->type', 'payment_success')->count())->toBe(0)
        ->and($cashier->notifications()->where('data->type', 'payment_success')->count())->toBe(1)
        ->and($sales->notifications()->where('data->type', 'payment_success')->count())->toBe(1)
        ->and($superAdmin->notifications()->where('data->type', 'payment_success')->count())->toBe(1);
});

test('order_created never reaches sales', function () {
    $sales = roleUser('sales');
    $inventory = roleUser('inventory');
    $superAdmin = roleUser('super-admin');

    $this->service->notifyRoles(['sales', 'inventory', 'super-admin'], 'order_created', []);

    expect($sales->notifications()->where('data->type', 'order_created')->count())->toBe(0)
        ->and($inventory->notifications()->where('data->type', 'order_created')->count())->toBe(1)
        ->and($superAdmin->notifications()->where('data->type', 'order_created')->count())->toBe(1);
});

test('customer reminders never reach internal staff roles', function () {
    $staff = collect(['mechanic', 'manager', 'super-admin'])
        ->map(fn (string $role) => roleUser($role));

    $this->service->notifyRoles(['mechanic', 'manager', 'super-admin'], 'service_reminder', []);

    foreach ($staff as $user) {
        expect($user->notifications()->where('data->type', 'service_reminder')->count())->toBe(0);
    }
});

test('approval_requested only reaches manager and super admin', function () {
    $manager = roleUser('manager');
    $mechanic = roleUser('mechanic');
    $cashier = roleUser('cashier');
    $superAdmin = roleUser('super-admin');

    $this->service->notifyRoles(['manager', 'mechanic', 'cashier', 'super-admin'], 'approval_requested', []);

    expect($manager->notifications()->where('data->type', 'approval_requested')->count())->toBe(1)
        ->and($superAdmin->notifications()->where('data->type', 'approval_requested')->count())->toBe(1)
        ->and($mechanic->notifications()->count())->toBe(0)
        ->and($cashier->notifications()->count())->toBe(0);
});

// ── Isolasi antar customer ──

test('notifications are isolated between two customers', function () {
    $userA = roleUser('customer');
    $userB = roleUser('customer');
    Customer::factory()->create(['user_id' => $userA->id]);
    Customer::factory()->create(['user_id' => $userB->id]);

    $this->service->send($userA, 'payment_success', [
        'title' => 'Pembayaran Berhasil',
        'message' => 'Pembayaran milik Customer A.',
        'order_code' => 'SO-A',
    ]);

    $this->service->send($userB, 'service_booking_created', [
        'title' => 'Booking Servis Baru',
        'message' => 'Booking milik Customer B.',
    ]);

    expect($userA->notifications()->get())->toHaveCount(1)
        ->and($userA->notifications()->where('data->order_code', 'SO-A')->count())->toBe(1)
        ->and($userB->notifications()->where('data->type', 'payment_success')->count())->toBe(0)
        ->and($userB->notifications()->where('data->type', 'service_booking_created')->count())->toBe(1);
});

// ── API matriks ──

test('allowed roles for exposes the relevance matrix', function () {
    expect($this->service->allowedRolesFor('payment_success'))
        ->toBe(['cashier', 'sales', 'manager', 'super-admin'])
        ->and($this->service->allowedRolesFor('service_reminder'))
        ->toBe([])
        ->and($this->service->allowedRolesFor('unknown_type'))
        ->toBeNull();
});
