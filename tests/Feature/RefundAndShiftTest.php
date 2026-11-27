<?php

declare(strict_types=1);

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\ApprovalService;
use App\Services\CheckoutService;
use App\Services\DashboardService;
use Illuminate\Http\Request;

function paidAtCashier(User $cashier, int $total = 100_000): SalesOrder
{
    $order = SalesOrder::factory()->create([
        'customer_id' => Customer::factory(),
        'type' => SalesOrderType::SparePart,
        'status' => SalesOrderStatus::Pending,
        'subtotal_idr' => $total,
        'discount_idr' => 0,
        'tax_idr' => 0,
        'total_idr' => $total,
        'paid_idr' => 0,
    ]);
    test()->actingAs($cashier);
    app(CheckoutService::class)->processPayment($order, 'cash', $total);

    return $order->fresh();
}

function refund(SalesOrder $order, int $amount): void
{
    $approvals = app(ApprovalService::class);
    $request = $approvals->createRequest('refund', ['payment_id' => $order->payments()->value('id'), 'amount_idr' => $amount], User::factory()->cashier()->create());
    $approvals->approve($request, User::factory()->manager()->create());
}

test('refund sebagian mengurangi omzet tanpa membatalkan pelunasan', function () {
    $order = paidAtCashier(User::factory()->cashier()->create());
    $paidAt = $order->paid_at;

    $this->travel(40)->days();
    refund($order, 10_000);

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Paid)
        ->and($order->fresh()->revenue_idr)->toBe(90_000)
        ->and($order->fresh()->paid_at->equalTo($paidAt))->toBeTrue()
        ->and($order->fresh()->progressLabel())->toContain('Refund Sebagian');
});

test('refund penuh mengubah status menjadi dikembalikan', function () {
    $order = paidAtCashier(User::factory()->cashier()->create());

    refund($order, 100_000);

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Refunded)
        ->and($order->fresh()->revenue_idr)->toBe(0)
        ->and($order->fresh()->invoice->status->value)->toBe('cancelled');
});

test('laporan shift menghitung uang yang diterima kasir itu saja', function () {
    $cashier = User::factory()->cashier()->create();
    paidAtCashier($cashier, 100_000);
    paidAtCashier($cashier, 50_000);
    paidAtCashier(User::factory()->cashier()->create(), 7_000);

    $this->actingAs($cashier)->get(route('admin.reports.finance', ['preset' => 'today']))
        ->assertOk()
        ->assertViewHas('totalIncome', 150_000)
        ->assertSee('Uang Saya Terima');
});

test('daftar transaksi owner mengikuti tanggal lunas', function () {
    $this->travelTo(now()->startOfMonth()->subDays(2));
    $order = SalesOrder::factory()->create(['status' => SalesOrderStatus::Pending]);
    $this->travelBack();
    $order->update(['status' => SalesOrderStatus::Paid]);

    $list = app(DashboardService::class)->getOwnerDashboardData(Request::create('/admin'))['recentTransactions'];

    expect($list->pluck('id'))->toContain($order->id);
});
