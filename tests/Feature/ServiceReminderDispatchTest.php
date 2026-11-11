<?php

use App\Jobs\SendServiceReminderJob;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->customerUser = User::factory()->create(['role' => 'customer']);
    $this->customer = Customer::factory()->create(['user_id' => $this->customerUser->id]);
    $this->vehicle = CustomerVehicle::factory()->create(['customer_id' => $this->customer->id]);
});

test('command dispatches reminder for vehicle that was never serviced', function () {
    Queue::fake();

    $this->artisan('notifications:dispatch-service-reminders');

    Queue::assertPushed(SendServiceReminderJob::class, fn ($job) => true);
});

test('reminder job sends service_reminder notification including vehicle_id', function () {
    (new SendServiceReminderJob($this->vehicle->id))->handle(app(NotificationService::class));

    expect($this->customerUser->fresh()->notifications()->count())->toBe(1);

    $notification = $this->customerUser->notifications()->first();
    expect(data_get($notification->data, 'type'))->toBe('service_reminder')
        ->and(data_get($notification->data, 'vehicle_id'))->toBe($this->vehicle->id);
});

test('command skips vehicle serviced within the interval', function () {
    ServiceBooking::factory()->create([
        'customer_id' => $this->customer->id,
        'customer_vehicle_id' => $this->vehicle->id,
        'preferred_date' => now()->subDays(30),
        'status' => 'completed',
    ]);

    Queue::fake();
    $this->artisan('notifications:dispatch-service-reminders');

    Queue::assertNotPushed(SendServiceReminderJob::class);
    expect($this->customerUser->fresh()->notifications()->count())->toBe(0);
});

test('command sends reminder for vehicle serviced beyond the interval', function () {
    ServiceBooking::factory()->create([
        'customer_id' => $this->customer->id,
        'customer_vehicle_id' => $this->vehicle->id,
        'preferred_date' => now()->subDays(200),
        'status' => 'completed',
    ]);

    Queue::fake();
    $this->artisan('notifications:dispatch-service-reminders');

    Queue::assertPushed(SendServiceReminderJob::class);
});

test('command dedupes: no second reminder while previous one is unread', function () {
    // Pengiriman pertama (queue sync): job dieksekusi langsung dan menulis notifikasi
    $this->artisan('notifications:dispatch-service-reminders');

    expect($this->customerUser->fresh()->notifications()->count())->toBe(1);

    // Pengiriman kedua harus terdedupe
    Queue::fake();
    $this->artisan('notifications:dispatch-service-reminders');

    Queue::assertNotPushed(SendServiceReminderJob::class);
    expect($this->customerUser->fresh()->notifications()->count())->toBe(1);
});

test('dry run reports due vehicles without dispatching', function () {
    Queue::fake();

    $this->artisan('notifications:dispatch-service-reminders', ['--dry-run' => true]);

    Queue::assertNotPushed(SendServiceReminderJob::class);
    expect($this->customerUser->fresh()->notifications()->count())->toBe(0);
});

test('inactive customer is skipped', function () {
    $this->customerUser->update(['is_active' => false]);

    Queue::fake();
    $this->artisan('notifications:dispatch-service-reminders');

    Queue::assertNotPushed(SendServiceReminderJob::class);
});
