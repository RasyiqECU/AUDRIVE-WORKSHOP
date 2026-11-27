<?php

declare(strict_types=1);

use App\Enums\CarUnitStatus;
use App\Enums\SalesOrderStatus;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\PaymentVerificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

function carBuyer(): Customer
{
    return Customer::factory()->create(['user_id' => User::factory()->customer()]);
}

function bookCar(Customer $customer, CarUnit $car, string $fulfillment = 'self_pickup'): SalesOrder
{
    test()->actingAs($customer->user)->post(route('marketplace.booking.store', $car), [
        'payment_channel' => 'online_transfer',
        'fulfillment_method' => $fulfillment,
        'shipping_address' => 'Jl. Uji No. 2',
    ])->assertSessionHasNoErrors();

    return SalesOrder::where('customer_id', $customer->id)->latest('id')->firstOrFail();
}

function transferFor(SalesOrder $order): Payment
{
    $order->refresh();
    test()->actingAs($order->customer->user)->post(route('parts.orders.proof', $order), [
        'proof' => UploadedFile::fake()->image('bukti.jpg'),
        'sender_name' => 'Pembeli',
        'destination_bank' => 'BCA',
        'amount_idr' => $order->amountDue(),
    ])->assertSessionHasNoErrors();

    return Payment::latest('id')->firstOrFail();
}

function listedCar(): CarUnit
{
    return CarUnit::factory()->create(['status' => CarUnitStatus::Available, 'price_sell_idr' => 300_000_000]);
}

test('booking fee mengunci unit, pelunasan menjual unit, sales mencatat serah terima', function () {
    $car = listedCar();
    $order = bookCar(carBuyer(), $car, 'delivery');
    $cashier = User::factory()->cashier()->create();
    expect($order->total_idr)->toBe(300_750_000)
        ->and($order->amountDue())->toBe(5_000_000)
        ->and($car->fresh()->status)->toBe(CarUnitStatus::Available);

    $proof = transferFor($order);
    $this->actingAs($cashier)->post(route('admin.payment-verifications.approve', $proof))->assertSessionHas('success');
    expect($car->fresh()->status)->toBe(CarUnitStatus::Reserved)
        ->and($order->fresh()->status)->toBe(SalesOrderStatus::Pending)
        ->and($order->fresh()->amountDue())->toBe(295_750_000);

    $proof = transferFor($order);
    $this->actingAs($cashier)->post(route('admin.payment-verifications.approve', $proof))->assertSessionHas('success');
    expect($order->fresh()->status)->toBe(SalesOrderStatus::Paid)
        ->and($car->fresh()->status)->toBe(CarUnitStatus::Sold)
        ->and(Payment::where('paymentable_id', $order->id)->pluck('purpose')->all())->toBe(['booking_fee', 'settlement']);

    $this->actingAs(User::factory()->sales()->create())->post(route('admin.car-handovers.store', $order));
    expect($order->fresh()->handed_over_at)->not->toBeNull();
});

test('booking fee kedua atas unit yang sama tidak bisa disetujui', function () {
    $car = listedCar();
    $first = bookCar(carBuyer(), $car);
    $second = bookCar(carBuyer(), $car);
    $cashier = User::factory()->cashier()->create();
    $firstProof = transferFor($first);
    $secondProof = transferFor($second);

    $this->actingAs($cashier)->post(route('admin.payment-verifications.approve', $firstProof));
    $this->actingAs($cashier)->post(route('admin.payment-verifications.approve', $secondProof))->assertSessionHas('error');

    expect($second->fresh()->paid_idr)->toBe(0)
        ->and($second->fresh()->status)->toBe(SalesOrderStatus::Pending);
});

test('pelunasan yang terlewat membuka kembali unit', function () {
    $car = listedCar();
    $order = bookCar(carBuyer(), $car);
    $proof = transferFor($order);
    $this->actingAs(User::factory()->cashier()->create())->post(route('admin.payment-verifications.approve', $proof))->assertSessionHas('success');

    $this->travel(8)->days();
    app(PaymentVerificationService::class)->expireOverdue();

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Expired)
        ->and($car->fresh()->status)->toBe(CarUnitStatus::Available);
});

test('booking fee di kasir mengunci unit dan pelunasan menagih sisanya', function () {
    $car = listedCar();
    $order = bookCar(carBuyer(), $car);
    $cashier = User::factory()->cashier()->create();

    $this->actingAs($cashier)->post(route('admin.pos.car-sales.deposit', $order), ['payment_method' => 'cash']);
    $this->actingAs($cashier)->post(route('admin.pos.car-sales.pay', $order), ['payment_method' => 'card']);

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Paid)
        ->and(Payment::where('paymentable_id', $order->id)->sum('amount_idr'))->toEqual(300_000_000)
        ->and($car->fresh()->status)->toBe(CarUnitStatus::Sold);
});
