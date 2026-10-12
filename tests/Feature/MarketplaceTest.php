<?php


use App\Enums\AppointmentStatus;
use App\Enums\CarUnitStatus;
use App\Enums\OfferStatus;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\User;


beforeEach(function () {
    $this->customer = User::factory()->create(['role' => 'customer']);
    Customer::factory()->create(['user_id' => $this->customer->id]);


    $this->car = CarUnit::factory()->create([
        'status' => CarUnitStatus::Available,
        'price_sell_idr' => 1000000000,
    ]);
});


test('public can view car catalog', function () {
    $response = $this->get(route('marketplace.index'));


    $response->assertStatus(200);
    $response->assertViewIs('marketplace.index');
    $response->assertViewHas('cars');
});


test('public can view car detail', function () {
    $response = $this->get(route('marketplace.show', $this->car->slug));


    $response->assertStatus(200);
    $response->assertViewIs('marketplace.show');
    $response->assertViewHas('car');
});


test('public can filter cars by brand', function () {
    $response = $this->get(route('marketplace.index', ['brand' => $this->car->brand]));


    $response->assertStatus(200);
});


test('public can sort cars by price', function () {
    $response = $this->get(route('marketplace.index', ['sort' => 'price_asc']));


    $response->assertStatus(200);
});


test('customer can book test drive', function () {
    $scheduledAt = now()->addDays(3)->format('Y-m-d H:i:s');


    $response = $this->actingAs($this->customer)
        ->post(route('marketplace.test-drive.store', $this->car->slug), [
            'scheduled_at' => $scheduledAt,
            'notes' => 'Test drive request',
        ]);


    $response->assertRedirect();


    $this->assertDatabaseHas('appointments', [
        'customer_id' => $this->customer->customer->id,
        'car_unit_id' => $this->car->id,
        'type' => 'test_drive',
        'status' => AppointmentStatus::Pending->value,
    ]);
});


test('customer cannot book test drive in the past', function () {
    $scheduledAt = now()->subDays(1)->format('Y-m-d H:i:s');


    $response = $this->actingAs($this->customer)
        ->post(route('marketplace.test-drive.store', $this->car->slug), [
            'scheduled_at' => $scheduledAt,
        ]);


    $response->assertSessionHasErrors('scheduled_at');
});


test('customer cannot book test drive for unavailable car', function () {
    $this->car->update(['status' => CarUnitStatus::Sold]);


    $response = $this->actingAs($this->customer)
        ->post(route('marketplace.test-drive.store', $this->car->slug), [
            'scheduled_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);


    $response->assertSessionHasErrors('car_unit');
});


test('customer can make offer', function () {
    $response = $this->actingAs($this->customer)
        ->post(route('marketplace.offer.store', $this->car->slug), [
            'amount_idr' => 900000000,
            'note' => 'Saya tertarik dengan unit ini',
        ]);


    $response->assertRedirect();


    $this->assertDatabaseHas('offers', [
        'customer_id' => $this->customer->customer->id,
        'car_unit_id' => $this->car->id,
        'amount_idr' => 900000000,
        'status' => OfferStatus::Pending->value,
    ]);
});


test('customer cannot make offer on unavailable car', function () {
    $this->car->update(['status' => CarUnitStatus::Sold]);


    $response = $this->actingAs($this->customer)
        ->post(route('marketplace.offer.store', $this->car->slug), [
            'amount_idr' => 900000000,
        ]);


    $response->assertSessionHasErrors('car_unit');
});


test('customer can view their offers', function () {
    // Create an offer first
    \App\Models\Offer::factory()->create([
        'customer_id' => $this->customer->customer->id,
        'car_unit_id' => $this->car->id,
    ]);


    $response = $this->actingAs($this->customer)
        ->get(route('customer.offers.index'));


    $response->assertStatus(200);
    $response->assertViewHas('offers');
});


test('sales can accept offer', function () {
    $salesUser = User::factory()->create(['role' => 'sales']);


    $offer = \App\Models\Offer::factory()->create([
        'customer_id' => $this->customer->customer->id,
        'car_unit_id' => $this->car->id,
        'amount_idr' => 900000000,
        'status' => OfferStatus::Pending,
    ]);


    $response = $this->actingAs($salesUser)
        ->post(route('admin.offers.accept', $offer));


    $response->assertRedirect();


    $this->assertDatabaseHas('offers', [
        'id' => $offer->id,
        'status' => OfferStatus::Accepted->value,
    ]);


    // Car should be reserved
    $this->assertDatabaseHas('car_units', [
        'id' => $this->car->id,
        'status' => CarUnitStatus::Reserved->value,
    ]);


    // Sales order should be created
    $this->assertDatabaseHas('sales_orders', [
        'customer_id' => $this->customer->customer->id,
        'type' => 'car_sales',
        'total_idr' => 900000000,
    ]);
});


test('sales can reject offer', function () {
    $salesUser = User::factory()->create(['role' => 'sales']);


    $offer = \App\Models\Offer::factory()->create([
        'customer_id' => $this->customer->customer->id,
        'car_unit_id' => $this->car->id,
        'status' => OfferStatus::Pending,
    ]);


    $response = $this->actingAs($salesUser)
        ->post(route('admin.offers.reject', $offer), [
            'reason' => 'Harga terlalu rendah',
        ]);


    $response->assertRedirect();


    $this->assertDatabaseHas('offers', [
        'id' => $offer->id,
        'status' => OfferStatus::Rejected->value,
    ]);
});


test('unauthenticated user cannot make offer', function () {
    $response = $this->post(route('marketplace.offer.store', $this->car->slug), [
        'amount_idr' => 900000000,
    ]);


    $response->assertRedirect(route('login'));
});


test('unauthenticated user cannot book test drive', function () {
    $response = $this->post(route('marketplace.test-drive.store', $this->car->slug), [
        'scheduled_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
    ]);


    $response->assertRedirect(route('login'));
});



