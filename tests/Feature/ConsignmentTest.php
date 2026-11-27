<?php

declare(strict_types=1);

use App\Enums\CarUnitStatus;
use App\Enums\OfferStatus;
use App\Enums\SalesOrderStatus;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\SalesOrder;
use App\Models\User;

function consignmentCustomer(): Customer
{
    return Customer::factory()->create(['user_id' => User::factory()->customer()]);
}

function submitConsignment(Customer $seller): CarUnit
{
    test()->actingAs($seller->user)
        ->post(route('marketplace.sell.store'), [
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2021,
            'transmission' => 'automatic',
            'mileage_km' => 30_000,
            'asking_price_idr' => 400_000_000,
            'condition_defects' => 'Baret di bumper depan',
            'condition_additions' => 'Velg 18 inci',
            'seller_warranty_info' => 'Garansi pabrik habis',
        ])
        ->assertSessionHasNoErrors();

    return CarUnit::where('seller_customer_id', $seller->id)->firstOrFail();
}

function approvePayload(): array
{
    return ['min_price_idr' => 380_000_000, 'price_sell_idr' => 400_000_000, 'commission_idr' => 10_000_000];
}

test('alur titip jual: ajukan → disetujui sales → dipesan → dibayar → penjual dibayar', function () {
    $seller = consignmentCustomer();
    $buyer = consignmentCustomer();
    $sales = User::factory()->sales()->create();
    $cashier = User::factory()->cashier()->create();
    $car = submitConsignment($seller);

    // Belum disetujui: tidak tampil ke publik
    expect($car->status)->toBe(CarUnitStatus::InInspection)
        ->and($car->condition_label)->toBe('Bekas - Titip Jual');
    $this->actingAs($buyer->user)->get(route('marketplace.show', $car->slug))->assertNotFound();
    $this->getJson("/api/v1/cars/{$car->id}")->assertNotFound();

    $this->actingAs($sales)->post(route('admin.consignments.approve', $car), approvePayload())->assertSessionHasNoErrors();
    $this->actingAs($buyer->user)
        ->get(route('marketplace.show', $car->slug))
        ->assertOk()
        ->assertSee('Baret di bumper depan')
        ->assertSee('dari penjual');

    // Pembelian sama seperti mobil lain
    $this->actingAs($buyer->user)->post(route('marketplace.offer.store', $car->slug), ['amount_idr' => 400_000_000]);
    $offer = Offer::where('car_unit_id', $car->id)->firstOrFail();
    $this->actingAs($sales)->post(route('admin.offers.accept', $offer));
    $order = SalesOrder::where('customer_id', $buyer->id)->firstOrFail();
    $this->actingAs($cashier)->post(route('admin.pos.car-sales.pay', $order), ['payment_method' => 'transfer']);
    expect($order->fresh()->status)->toBe(SalesOrderStatus::Paid)
        ->and($car->fresh()->status)->toBe(CarUnitStatus::Sold);

    $this->actingAs($cashier)->get(route('admin.pos.index'))->assertSee('Pembayaran ke Penjual Titip Jual');
    $this->actingAs($cashier)->post(route('admin.pos.consignments.payout', $car));
    $this->actingAs($cashier)->post(route('admin.pos.consignments.payout', $car))->assertSessionHas('error');
    expect($car->fresh()->seller_payout_idr)->toBe(390_000_000);
});

test('penjual tidak bisa memesan mobilnya sendiri dan hanya bisa menarik sebelum dipesan', function () {
    $seller = consignmentCustomer();
    $car = submitConsignment($seller);
    $this->actingAs(User::factory()->sales()->create())->post(route('admin.consignments.approve', $car), approvePayload());

    $this->actingAs($seller->user)
        ->post(route('marketplace.offer.store', $car->slug), ['amount_idr' => 400_000_000])
        ->assertSessionHasErrors('car_unit');
    $this->actingAs(consignmentCustomer()->user)->post(route('marketplace.sell.withdraw', $car->slug))->assertForbidden();

    $car->update(['status' => CarUnitStatus::Reserved]);
    $this->actingAs($seller->user)->post(route('marketplace.sell.withdraw', $car->slug))->assertSessionHas('error');
    expect($car->fresh()->status)->toBe(CarUnitStatus::Reserved);
});

test('tawaran di bawah harga minimum butuh persetujuan penjual', function () {
    $sales = User::factory()->sales()->create();
    $car = submitConsignment(consignmentCustomer());
    $this->actingAs($sales)->post(route('admin.consignments.approve', $car), approvePayload());
    $this->actingAs(consignmentCustomer()->user)->post(route('marketplace.offer.store', $car->slug), ['amount_idr' => 300_000_000]);
    $offer = Offer::where('car_unit_id', $car->id)->firstOrFail();

    $this->actingAs($sales)->post(route('admin.offers.accept', $offer))->assertSessionHas('error');
    expect($offer->fresh()->status)->toBe(OfferStatus::Pending);

    $this->actingAs($sales)->post(route('admin.offers.accept', $offer), ['seller_approved' => 1]);
    expect($offer->fresh()->status)->toBe(OfferStatus::Accepted);
});

test('filter marketplace memisahkan mobil baru dan bekas', function () {
    CarUnit::factory()->create(['model' => 'ModelBaru', 'condition' => 'new', 'status' => CarUnitStatus::Available]);
    CarUnit::factory()->create(['model' => 'ModelBekas', 'condition' => 'used', 'status' => CarUnitStatus::Available]);

    $this->get(route('marketplace.index', ['condition' => 'new']))
        ->assertSee('ModelBaru')
        ->assertDontSee('ModelBekas');
});
