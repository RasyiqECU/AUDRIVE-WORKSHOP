<?php

declare(strict_types=1);

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\Notification\Channels\WhatsAppNotificationChannel;
use App\Services\PaymentVerificationService;
use App\Services\WarrantyClaimService;
use Illuminate\Support\Facades\Log;

function qaCustomer(): Customer
{
    return Customer::factory()->create(['user_id' => User::factory()->customer()]);
}

function qaOrder(Customer $customer, array $attributes = []): SalesOrder
{
    return SalesOrder::factory()->create($attributes + [
        'customer_id' => $customer->id,
        'type' => SalesOrderType::SparePart,
        'status' => SalesOrderStatus::Pending,
        'subtotal_idr' => 100_000,
        'discount_idr' => 0,
        'tax_idr' => 11_000,
        'shipping_fee_idr' => 50_000,
        'total_idr' => 161_000,
        'paid_idr' => 0,
    ]);
}

test('invoice menampilkan ongkir dan rekening dari pengaturan', function () {
    $customer = qaCustomer();
    $invoice = app(InvoiceService::class)->ensureInvoiceForSalesOrder(qaOrder($customer));

    $this->actingAs($customer->user)->get(route('customer.invoices.show', $invoice))
        ->assertOk()
        ->assertSee('Ongkos Kirim')
        ->assertSee('8830 1928 3829 001')
        ->assertDontSee('588-099-8800');
    $this->actingAs($customer->user)->get(route('customer.invoices.pdf', $invoice))->assertOk();
});

test('pesanan kedaluwarsa tidak lagi dihitung sebagai tagihan', function () {
    $customer = qaCustomer();
    $order = qaOrder($customer, ['reserved_until' => now()->subHour()]);
    $invoice = app(InvoiceService::class)->ensureInvoiceForSalesOrder($order);

    app(PaymentVerificationService::class)->expireOverdue();

    expect($invoice->fresh()->status->value)->toBe('cancelled');
    $this->actingAs($customer->user)->get(route('customer.invoices.index'))
        ->assertOk()
        ->assertViewHas('totalPending', 0)
        ->assertSee('Dibatalkan');
    $this->actingAs($customer->user)->get(route('parts.orders.index'))->assertSee('Kedaluwarsa');
});

test('garansi mobil dihitung dari serah terima, bukan dari booking fee', function () {
    $order = qaOrder(qaCustomer(), ['type' => SalesOrderType::CarSales, 'status' => SalesOrderStatus::Paid, 'handed_over_at' => now()->subDays(2)]);
    Payment::create(['paymentable_type' => SalesOrder::class, 'paymentable_id' => $order->id, 'method' => 'cash', 'status' => 'success', 'amount_idr' => 1, 'paid_at' => now()->subDays(20)]);

    $start = app(WarrantyClaimService::class)->referenceDate(['type' => 'car', 'order' => $order->fresh(), 'workOrder' => null]);

    expect($start->toDateString())->toBe(now()->subDays(2)->toDateString());
});

test('halaman yang dulu error 500 sekarang berjalan', function () {
    $customer = qaCustomer();

    $this->actingAs($customer->user)->get(route('parts.checkout'))->assertRedirect(route('parts.index'));
    $this->actingAs($customer->user)->get(route('parts.search', ['search' => 'oli']))->assertOk();

    foreach (['workshop.booking.index', 'workshop.history', 'workshop.ai-scan.create'] as $route) {
        $this->actingAs(User::factory()->sales()->create())->get(route($route))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($customer->user)->get(route($route))->assertOk();
    }
});

test('notifikasi WhatsApp tidak gagal bila data tidak lengkap', function () {
    Log::spy();
    $sent = app(WhatsAppNotificationChannel::class)
        ->send(User::factory()->customer()->create(), 'payment_success', ['title' => 'x']);

    expect($sent)->toBeTrue();
    Log::shouldNotHaveReceived('error');
});
