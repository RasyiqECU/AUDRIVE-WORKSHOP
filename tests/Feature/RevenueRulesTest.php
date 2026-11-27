<?php

declare(strict_types=1);

use App\Enums\CarUnitStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;

function pendingCarOrder(CarUnit $car, array $attributes = []): SalesOrder
{
    $order = SalesOrder::factory()->create($attributes + [
        'type' => SalesOrderType::CarSales,
        'status' => SalesOrderStatus::Pending,
        'subtotal_idr' => 1_000_000,
        'discount_idr' => 0,
        'shipping_fee_idr' => 50_000,
        'total_idr' => 1_050_000,
    ]);
    $order->items()->create(['item_type' => 'car', 'car_unit_id' => $car->id, 'description' => 'Unit', 'qty' => 1, 'unit_price_idr' => 1_000_000, 'subtotal_idr' => 1_000_000]);

    return $order;
}

test('omzet titip jual hanya komisi, biaya servis, dan towing', function () {
    $consigned = CarUnit::factory()->create(['ownership' => 'consignment', 'commission_idr' => 30_000, 'repair_cost_idr' => 5_000, 'status' => CarUnitStatus::Available]);
    $dealer = CarUnit::factory()->create(['ownership' => 'dealer']);

    $sold = pendingCarOrder($consigned);
    $sold->update(['status' => SalesOrderStatus::Paid]);
    $own = pendingCarOrder($dealer);
    $own->update(['status' => SalesOrderStatus::Paid]);

    expect($sold->fresh()->revenue_idr)->toBe(85_000)
        ->and($own->fresh()->revenue_idr)->toBe(1_050_000);
});

test('omzet dicatat pada bulan lunas, bukan bulan pesanan', function () {
    $this->travelTo(now()->startOfMonth()->subDays(2));
    $order = pendingCarOrder(CarUnit::factory()->create(['ownership' => 'dealer']));
    $this->travelBack();

    expect(app(DashboardService::class)->getOwnerDashboardData(Request::create('/admin'))['totalRevenue'])->toBe(0);

    $order->update(['status' => SalesOrderStatus::Paid]);

    expect($order->fresh()->paid_at->isSameMonth(now()))->toBeTrue()
        ->and(app(DashboardService::class)->getOwnerDashboardData(Request::create('/admin'))['totalRevenue'])->toBe(1_050_000);

    // Pengembalian dana yang membatalkan pelunasan menghapus omzetnya
    $order->update(['status' => SalesOrderStatus::Pending]);
    expect($order->fresh()->paid_at)->toBeNull()->and($order->fresh()->revenue_idr)->toBe(0);
});

test('serah terima mencatat sales dan rekap memisahkan penjualan mandiri', function () {
    $sales = User::factory()->sales()->create(['name' => 'Sari Sales']);
    $order = pendingCarOrder(CarUnit::factory()->create(['ownership' => 'dealer']), ['customer_id' => Customer::factory()]);
    $order->update(['status' => SalesOrderStatus::Paid]);
    SalesOrder::factory()->create(['type' => SalesOrderType::SparePart, 'status' => SalesOrderStatus::Paid, 'total_idr' => 700]);

    $this->actingAs($sales)->post(route('admin.car-handovers.store', $order))->assertSessionHas('success');
    expect($order->fresh()->sales_id)->toBe($sales->id);

    $this->actingAs(User::factory()->manager()->create())->get(route('admin.reports.sales', ['preset' => 'all']))
        ->assertOk()->assertSee('Sari Sales')->assertSee('Penjualan Mandiri / Online');
    $this->actingAs($sales)->get(route('admin.reports.sales', ['preset' => 'all']))
        ->assertOk()->assertDontSee('Penjualan Mandiri / Online');
});
