<?php

declare(strict_types=1);

use App\Enums\AiScanStatus;
use App\Enums\CarUnitStatus;
use App\Enums\OfferStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\AiScan;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\User;
use App\Models\Voucher;
use App\Services\AiScanService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

function bugFixCustomer(): Customer
{
    return Customer::factory()->create(['user_id' => User::factory()->customer()->create()->id]);
}

function bugFixPartOrder(Customer $customer): SalesOrder
{
    return SalesOrder::factory()->create([
        'customer_id' => $customer->id,
        'type' => SalesOrderType::SparePart,
        'status' => SalesOrderStatus::Pending,
        'total_idr' => 111_000,
        'paid_idr' => 0,
    ]);
}

test('voucher berbayar poin tidak bisa diklaim gratis', function () {
    $customer = bugFixCustomer();
    $voucher = Voucher::factory()->active()->create(['points_cost' => 150]);

    $this->actingAs($customer->user)
        ->post(route('customer.vouchers.claim', $voucher))
        ->assertSessionHas('error');

    expect($customer->voucherRedemptions()->count())->toBe(0);
});

test('voucher gratis hanya bisa diklaim sekali per customer', function () {
    $customer = bugFixCustomer();
    $voucher = Voucher::factory()->active()->create(['points_cost' => 0]);

    $this->actingAs($customer->user)->post(route('customer.vouchers.claim', $voucher));
    $this->actingAs($customer->user)
        ->post(route('customer.vouchers.claim', $voucher))
        ->assertSessionHas('error');

    expect($customer->voucherRedemptions()->count())->toBe(1);
});

test('customer tidak bisa mengubah atau menghapus item keranjang orang lain', function () {
    $part = SparePart::factory()->active()->create(['stock' => 10]);
    $item = Cart::create(['customer_id' => bugFixCustomer()->id])
        ->items()->create(['spare_part_id' => $part->id, 'qty' => 1, 'price_idr' => 1000]);
    $intruder = bugFixCustomer()->user;

    $this->actingAs($intruder)->patch(route('parts.cart.update', $item), ['qty' => 5])->assertForbidden();
    $this->actingAs($intruder)->delete(route('parts.cart.destroy', $item))->assertForbidden();
    $this->actingAs($intruder)->putJson("/api/v1/cart/items/{$item->id}", ['qty' => 5])->assertForbidden();
    $this->actingAs($intruder)->deleteJson("/api/v1/cart/items/{$item->id}")->assertForbidden();

    expect($item->fresh()->qty)->toBe(1);
});

test('staf tanpa profil customer tidak bisa membuka pesanan customer', function () {
    $order = bugFixPartOrder(bugFixCustomer());

    $this->actingAs(User::factory()->mechanic()->create())
        ->get(route('parts.orders.show', $order))
        ->assertForbidden();
});

test('kasir melunasi pesanan online dan invoice tidak dobel', function () {
    $order = bugFixPartOrder(bugFixCustomer());
    Invoice::create([
        'invoiceable_type' => SalesOrder::class,
        'invoiceable_id' => $order->id,
        'number' => 'INV-PART-DRAFT',
        'status' => 'draft',
        'total_idr' => $order->total_idr,
    ]);
    $cashier = User::factory()->cashier()->create();

    $this->actingAs($cashier)->get(route('admin.pos.index'))->assertSee($order->code);
    $this->actingAs($cashier)
        ->post(route('admin.pos.part-orders.pay', $order), ['payment_method' => 'transfer'])
        ->assertRedirect(route('admin.pos.receipt', $order));
    $this->actingAs($cashier)
        ->post(route('admin.pos.part-orders.pay', $order), ['payment_method' => 'transfer'])
        ->assertSessionHas('error');

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Paid)
        ->and($order->payments()->count())->toBe(1)
        ->and(Invoice::where('invoiceable_id', $order->id)->pluck('status')->map->value->all())->toBe(['paid']);
});

test('offer kedaluwarsa dijadwalkan sehingga mobil tersedia kembali', function () {
    $offer = Offer::factory()->create(['status' => OfferStatus::Pending, 'expires_at' => now()->subDay()]);
    $offer->carUnit->update(['status' => CarUnitStatus::Reserved]);

    $event = collect(app(Schedule::class)->events())->firstOrFail(fn ($event) => $event->description === 'offers:expire');
    $event->run(app());

    expect($offer->fresh()->status)->toBe(OfferStatus::Expired)
        ->and($offer->carUnit->fresh()->status)->toBe(CarUnitStatus::Available);
});

test('hasil AI yang perlu review ditandai manual review', function () {
    Storage::fake('public');
    Storage::disk('public')->put('ai-scans/audio/a.wav', 'x');
    $scan = AiScan::factory()->create(['audio_path' => 'ai-scans/audio/a.wav']);
    Http::fake(['*/predict' => Http::response([
        'success' => true,
        'result' => ['predicted_class' => 'low oil', 'confidence' => 0.4, 'needs_manual_review' => true],
    ])]);

    app(AiScanService::class)->process($scan);

    expect($scan->fresh()->status)->toBe(AiScanStatus::ManualReview);
});
