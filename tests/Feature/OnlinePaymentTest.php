<?php

declare(strict_types=1);

use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\User;
use App\Services\PaymentVerificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

function buyer(): Customer
{
    return Customer::factory()->create(['user_id' => User::factory()->customer(), 'loyalty_points' => 0]);
}

function placeOrder(Customer $customer, SparePart $part, int $qty = 2, string $channel = 'online_transfer'): SalesOrder
{
    Cart::firstOrCreate(['customer_id' => $customer->id])
        ->items()->create(['spare_part_id' => $part->id, 'qty' => $qty, 'price_idr' => $part->price_idr]);

    test()->actingAs($customer->user)->post(route('parts.checkout.store'), [
        'shipping_method' => 'delivery',
        'shipping_address' => 'Jl. Uji No. 1',
        'payment_channel' => $channel,
    ]);

    return SalesOrder::where('customer_id', $customer->id)->latest('id')->firstOrFail();
}

function uploadProof(SalesOrder $order, ?int $amount = null, ?UploadedFile $file = null)
{
    return test()->actingAs($order->customer->user)->post(route('parts.orders.proof', $order), [
        'proof' => $file ?? UploadedFile::fake()->image('bukti.jpg'),
        'sender_name' => 'Pengirim Uji',
        'destination_bank' => 'BCA',
        'amount_idr' => $amount ?? $order->total_idr,
    ]);
}

function stockPart(int $stock = 5): SparePart
{
    return SparePart::factory()->active()->create(['stock' => $stock, 'price_idr' => 100_000]);
}

test('checkout mengunci stok tanpa memotong stok fisik dan menambah ongkir', function () {
    $part = stockPart(5);

    $order = placeOrder(buyer(), $part, 2);

    expect($part->fresh()->stock)->toBe(5)
        ->and($part->fresh()->booked_stock)->toBe(2)
        ->and($part->fresh()->available_stock)->toBe(3)
        ->and($order->total_idr)->toBe(200_000 + 22_000 + 50_000)
        ->and($order->payment_status)->toBe(PaymentStatus::WaitingUpload->value)
        ->and($order->reserved_until)->not->toBeNull()
        ->and($order->customer->fresh()->loyalty_points)->toBe(0);
});

test('stok yang sedang dikunci tidak bisa dipesan orang lain', function () {
    $part = stockPart(3);
    placeOrder(buyer(), $part, 3);
    $other = buyer();
    Cart::create(['customer_id' => $other->id])->items()->create(['spare_part_id' => $part->id, 'qty' => 1, 'price_idr' => 100_000]);

    $this->actingAs($other->user)
        ->post(route('parts.checkout.store'), ['shipping_method' => 'pickup'])
        ->assertSessionHas('error');

    expect(SalesOrder::where('customer_id', $other->id)->exists())->toBeFalse()
        ->and($part->fresh()->booked_stock)->toBe(3);
});

test('alur transfer: unggah → ditolak → unggah ulang → disetujui → disiapkan → diserahkan', function () {
    $part = stockPart(5);
    $order = placeOrder(buyer(), $part, 2);
    $cashier = User::factory()->cashier()->create();
    $inventory = User::factory()->inventory()->create();

    uploadProof($order)->assertSessionHasNoErrors();
    $payment = Payment::firstOrFail();
    Storage::disk('local')->assertExists($payment->proof_path);
    expect($order->fresh()->payment_status)->toBe(PaymentStatus::WaitingVerification->value);

    $this->actingAs($cashier)->get(route('admin.payment-verifications.index'))->assertOk()->assertSee($order->code);
    $this->actingAs($cashier)->post(route('admin.payment-verifications.reject', $payment), [])->assertSessionHasErrors('rejection_reason');
    $this->actingAs($cashier)->post(route('admin.payment-verifications.reject', $payment), ['rejection_reason' => 'Bukti buram']);
    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Rejected->value);
    $this->actingAs($order->customer->user)->get(route('parts.checkout.payment', $order))->assertSee('Bukti buram');

    uploadProof($order->fresh());
    $second = Payment::latest('id')->firstOrFail();
    $this->actingAs($cashier)->post(route('admin.payment-verifications.approve', $second));
    $this->actingAs($cashier)->post(route('admin.payment-verifications.approve', $second))->assertSessionHas('error');

    $order->refresh();
    expect($order->status)->toBe(SalesOrderStatus::Paid)
        ->and($order->paid_idr)->toBe($order->total_idr)
        ->and($order->invoice->status->value)->toBe('paid')
        ->and($part->fresh()->stock)->toBe(3)
        ->and($part->fresh()->booked_stock)->toBe(0)
        ->and($order->customer->fresh()->loyalty_points)->toBe(20);

    $this->actingAs($inventory)->post(route('admin.fulfilments.hand-over', $order))->assertSessionHas('error');
    $this->actingAs($inventory)->post(route('admin.fulfilments.prepare', $order));
    $this->actingAs($inventory)->post(route('admin.fulfilments.hand-over', $order));
    expect($order->fresh()->handed_over_at)->not->toBeNull()
        ->and($order->fresh()->status)->toBe(SalesOrderStatus::Paid);
});

test('nominal kurang tidak bisa disetujui', function () {
    $order = placeOrder(buyer(), stockPart());
    uploadProof($order, 1_000);

    $this->actingAs(User::factory()->cashier()->create())
        ->post(route('admin.payment-verifications.approve', Payment::firstOrFail()))
        ->assertSessionHas('error');

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Pending);
});

test('bukti transfer yang tidak valid ditolak', function (UploadedFile $file) {
    $order = placeOrder(buyer(), stockPart());

    uploadProof($order, file: $file)->assertSessionHasErrors('proof');

    expect(Payment::count())->toBe(0);
})->with([
    'bukan gambar' => [fn () => UploadedFile::fake()->create('virus.php', 10, 'application/x-php')],
    'pdf' => [fn () => UploadedFile::fake()->create('bukti.pdf', 10, 'application/pdf')],
    'lebih dari 2 MB' => [fn () => UploadedFile::fake()->image('besar.jpg')->size(3000)],
]);

test('bukti hanya bisa diunggah pemilik dan dilihat pemilik atau kasir', function () {
    $order = placeOrder(buyer(), stockPart());
    uploadProof($order);
    $payment = Payment::firstOrFail();
    $stranger = buyer()->user;

    $this->actingAs($stranger)->get(route('payments.proof', $payment))->assertForbidden();
    $this->actingAs(User::factory()->sales()->create())->get(route('payments.proof', $payment))->assertForbidden();
    $this->actingAs($stranger)->post(route('parts.orders.proof', $order), [])->assertForbidden();
    $this->actingAs($order->customer->user)->get(route('payments.proof', $payment))->assertOk();
    $this->actingAs(User::factory()->cashier()->create())->get(route('payments.proof', $payment))->assertOk();
});

test('order lewat batas waktu kedaluwarsa dan stok kembali, kecuali yang menunggu verifikasi', function () {
    $part = stockPart(10);
    $abandoned = placeOrder(buyer(), $part, 2);
    $waiting = placeOrder(buyer(), $part, 3);
    uploadProof($waiting);

    $this->travel(25)->hours();
    $expired = app(PaymentVerificationService::class)->expireOverdue();
    app(PaymentVerificationService::class)->expireOverdue();

    expect($expired)->toBe(1)
        ->and($abandoned->fresh()->status)->toBe(SalesOrderStatus::Expired)
        ->and($waiting->fresh()->status)->toBe(SalesOrderStatus::Pending)
        ->and($part->fresh()->booked_stock)->toBe(3)
        ->and($part->fresh()->stock)->toBe(10);
});

test('pesanan bayar di kasir memotong stok saat kasir menandai lunas', function () {
    $part = stockPart(5);
    $order = placeOrder(buyer(), $part, 2, 'cashier');
    $cashier = User::factory()->cashier()->create();

    $this->actingAs($cashier)->post(route('admin.pos.part-orders.pay', $order), ['payment_method' => 'cash']);

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Paid)
        ->and($part->fresh()->stock)->toBe(3)
        ->and($part->fresh()->booked_stock)->toBe(0);
});

test('penjualan kasir tidak bisa memakai stok yang sedang dikunci', function () {
    $part = stockPart(3);
    placeOrder(buyer(), $part, 3);

    $this->actingAs(User::factory()->cashier()->create())
        ->post(route('admin.pos.store'), ['items' => [['spare_part_id' => $part->id, 'qty' => 1]], 'payment_method' => 'cash'])
        ->assertSessionHas('error');

    expect($part->fresh()->stock)->toBe(3);
});
