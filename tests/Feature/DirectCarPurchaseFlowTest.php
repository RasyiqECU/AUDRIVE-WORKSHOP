<?php

use App\Enums\CarUnitStatus;
use App\Enums\OfferStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\PosService;

beforeEach(function () {
    $this->customerUser = User::factory()->create(['role' => 'customer']);
    $this->customer = Customer::factory()->create(['user_id' => $this->customerUser->id]);

    $this->car = CarUnit::factory()->create([
        'status' => CarUnitStatus::Available,
        'price_sell_idr' => 1_250_000_000,
        'brand' => 'Porsche',
        'model' => '911 GT3 RS',
        'year' => 2023,
    ]);

    $this->salesUser = User::factory()->create(['role' => 'sales']);
    $this->cashierUser = User::factory()->create(['role' => 'cashier']);
});

test('customer can directly order available car without entering custom price and car becomes reserved', function () {
    $response = $this->actingAs($this->customerUser)
        ->post(route('marketplace.offer.store', $this->car->slug), [
            'note' => 'Pembayaran tunai via transfer BCA',
        ]);

    $response->assertRedirect(route('marketplace.show', $this->car->slug));
    $response->assertSessionHas('success');

    // Order record created with car sell price
    $this->assertDatabaseHas('offers', [
        'customer_id' => $this->customer->id,
        'car_unit_id' => $this->car->id,
        'amount_idr' => 1_250_000_000,
        'status' => OfferStatus::Pending->value,
        'note' => 'Pembayaran tunai via transfer BCA',
    ]);

    // Unit status becomes Reserved immediately
    $this->assertDatabaseHas('car_units', [
        'id' => $this->car->id,
        'status' => CarUnitStatus::Reserved->value,
    ]);
});

test('customer cannot order a car that is already reserved or sold', function () {
    $this->car->update(['status' => CarUnitStatus::Reserved]);

    $response = $this->actingAs($this->customerUser)
        ->post(route('marketplace.offer.store', $this->car->slug), [
            'note' => 'Saya mau beli unit ini',
        ]);

    $response->assertSessionHasErrors('car_unit');

    $this->car->update(['status' => CarUnitStatus::Sold]);

    $response = $this->actingAs($this->customerUser)
        ->post(route('marketplace.offer.store', $this->car->slug), [
            'note' => 'Saya mau beli unit ini',
        ]);

    $response->assertSessionHasErrors('car_unit');
});

test('sales can see incoming order in Pesanan Masuk index and sidebar', function () {
    Offer::create([
        'car_unit_id' => $this->car->id,
        'customer_id' => $this->customer->id,
        'amount_idr' => 1_250_000_000,
        'status' => OfferStatus::Pending,
        'note' => 'Pesan unit warna merah',
    ]);

    $response = $this->actingAs($this->salesUser)
        ->get(route('admin.offers.index'));

    $response->assertStatus(200);
    $response->assertSee('PESANAN MOBIL MASUK');
    $response->assertSee('Pesanan Masuk');
    $response->assertSee('Porsche 911 GT3 RS');
});

test('sales accepts order creates sales order and keeps unit reserved', function () {
    $offer = Offer::create([
        'car_unit_id' => $this->car->id,
        'customer_id' => $this->customer->id,
        'amount_idr' => 1_250_000_000,
        'status' => OfferStatus::Pending,
    ]);

    // Unit is reserved
    $this->car->update(['status' => CarUnitStatus::Reserved]);

    $response = $this->actingAs($this->salesUser)
        ->post(route('admin.offers.accept', $offer));

    $response->assertRedirect(route('admin.offers.show', $offer));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('offers', [
        'id' => $offer->id,
        'status' => OfferStatus::Accepted->value,
        'reviewed_by' => $this->salesUser->id,
    ]);

    $this->assertDatabaseHas('car_units', [
        'id' => $this->car->id,
        'status' => CarUnitStatus::Reserved->value,
    ]);

    $this->assertDatabaseHas('sales_orders', [
        'customer_id' => $this->customer->id,
        'type' => SalesOrderType::CarSales->value,
        'total_idr' => 1_250_000_000,
        'status' => SalesOrderStatus::Pending->value,
    ]);
});

test('when sales order is paid at cashier POS car status becomes Sold', function () {
    $this->car->update(['status' => CarUnitStatus::Reserved]);

    $offer = Offer::create([
        'car_unit_id' => $this->car->id,
        'customer_id' => $this->customer->id,
        'amount_idr' => 1_250_000_000,
        'status' => OfferStatus::Pending,
    ]);

    // Accept order through sales
    $this->actingAs($this->salesUser)->post(route('admin.offers.accept', $offer));

    $salesOrder = SalesOrder::where('type', SalesOrderType::CarSales)
        ->where('notes', 'like', "%offer #{$offer->id}%")
        ->firstOrFail();

    // Cashier pays the car sales order
    $posService = app(PosService::class);
    $posService->payCarSalesOrder($salesOrder, 0, 'transfer', $this->cashierUser->id);

    // Car unit status MUST be Sold
    $this->assertDatabaseHas('car_units', [
        'id' => $this->car->id,
        'status' => CarUnitStatus::Sold->value,
    ]);

    expect($this->car->fresh()->status)->toBe(CarUnitStatus::Sold);
});

test('if sales rejects order car status reverts back to Available', function () {
    // Customer ordered, car became Reserved
    $this->car->update(['status' => CarUnitStatus::Reserved]);

    $offer = Offer::create([
        'car_unit_id' => $this->car->id,
        'customer_id' => $this->customer->id,
        'amount_idr' => 1_250_000_000,
        'status' => OfferStatus::Pending,
    ]);

    $response = $this->actingAs($this->salesUser)
        ->post(route('admin.offers.reject', $offer), [
            'reason' => 'Unit dialihkan ke saluran prioritas',
        ]);

    $response->assertRedirect(route('admin.offers.show', $offer));

    $this->assertDatabaseHas('offers', [
        'id' => $offer->id,
        'status' => OfferStatus::Rejected->value,
    ]);

    // Car unit status reverted to Available
    $this->assertDatabaseHas('car_units', [
        'id' => $this->car->id,
        'status' => CarUnitStatus::Available->value,
    ]);
    expect($this->car->fresh()->status)->toBe(CarUnitStatus::Available);
});

test('if customer cancels order car status reverts back to Available', function () {
    $this->car->update(['status' => CarUnitStatus::Reserved]);

    $offer = Offer::create([
        'car_unit_id' => $this->car->id,
        'customer_id' => $this->customer->id,
        'amount_idr' => 1_250_000_000,
        'status' => OfferStatus::Pending,
    ]);

    $response = $this->actingAs($this->customerUser)
        ->delete(route('marketplace.offer.destroy', $offer));

    $response->assertRedirect();

    $this->assertDatabaseMissing('offers', [
        'id' => $offer->id,
    ]);

    // Car unit status reverted to Available
    $this->assertDatabaseHas('car_units', [
        'id' => $this->car->id,
        'status' => CarUnitStatus::Available->value,
    ]);
    expect($this->car->fresh()->status)->toBe(CarUnitStatus::Available);
});
