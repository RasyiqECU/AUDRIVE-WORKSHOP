<?php

declare(strict_types=1);

use App\Enums\SalesOrderStatus;
use App\Enums\WorkOrderStatus;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\ApprovalService;
use App\Services\ServiceBillingService;

function completedWorkOrder(): WorkOrder
{
    return WorkOrder::factory()->create([
        'status' => WorkOrderStatus::Completed,
        'total_jasa_idr' => 1_000_000,
        'total_part_idr' => 0,
        'total_idr' => 1_000_000,
    ]);
}

test('kasir tidak bisa memberi diskon tanpa persetujuan manager', function () {
    $workOrder = completedWorkOrder();

    $this->actingAs(User::factory()->cashier()->create())
        ->post(route('admin.pos.work-orders.pay', $workOrder), ['payment_method' => 'cash', 'discount_idr' => 100_000])
        ->assertSessionHasErrors('discount_idr');

    expect(SalesOrder::where('status', SalesOrderStatus::Paid)->count())->toBe(0);
});

test('diskon yang disetujui manager dipakai saat kasir menagih', function () {
    $workOrder = completedWorkOrder();
    $cashier = User::factory()->cashier()->create();
    $bill = app(ServiceBillingService::class)->issue($workOrder, $cashier->id);
    $approvals = app(ApprovalService::class);

    $request = $approvals->createRequest('discount', ['sales_order_id' => $bill->id, 'amount_idr' => 110_000], $cashier);
    $approvals->approve($request, User::factory()->manager()->create());
    $this->actingAs($cashier)->post(route('admin.pos.work-orders.pay', $workOrder), ['payment_method' => 'cash']);

    expect($bill->fresh()->status)->toBe(SalesOrderStatus::Paid)
        ->and($bill->fresh()->discount_idr)->toBe(110_000)
        ->and($bill->fresh()->paid_idr)->toBe(1_000_000);
});
