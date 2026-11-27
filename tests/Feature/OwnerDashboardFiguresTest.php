<?php

declare(strict_types=1);

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;

function ownerFigures(): array
{
    return app(DashboardService::class)->getOwnerDashboardData(Request::create('/admin'));
}

test('rincian divisi dan grafik memuat penjualan POS', function () {
    foreach ([[SalesOrderType::SparePart, 100], [SalesOrderType::Pos, 40], [SalesOrderType::Service, 7]] as [$type, $total]) {
        SalesOrder::factory()->create(['type' => $type, 'status' => SalesOrderStatus::Paid, 'total_idr' => $total]);
    }

    $data = ownerFigures();

    expect($data['partRevenue'])->toBe(140)
        ->and($data['carRevenue'] + $data['workshopRevenue'] + $data['partRevenue'])->toBe($data['totalRevenue'])
        ->and(array_sum(array_column($data['trendData'], 'part_rev')))->toBe(140);
});

test('pengeluaran hanya menghitung barang purchase order yang sudah diterima', function () {
    $part = SparePart::factory()->create();
    $creator = User::factory()->create(['role' => 'inventory']);

    foreach ([['received', 10], ['partially_received', 3], ['sent', 0], ['draft', 0], ['cancelled', 0]] as [$status, $received]) {
        $order = PurchaseOrder::create([
            'supplier_name' => 'Pemasok',
            'status' => $status,
            'total_idr' => 1_000,
            'received_at' => $status === 'received' ? now() : null,
            'created_by' => $creator->id,
        ]);
        $order->items()->create(['spare_part_id' => $part->id, 'qty' => 10, 'cost_idr' => 100, 'subtotal_idr' => 1_000, 'received_qty' => $received]);
    }

    expect(ownerFigures()['totalPurchases'])->toBe(1_300);
});
