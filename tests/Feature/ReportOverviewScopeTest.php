<?php

declare(strict_types=1);

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\SalesOrder;
use App\Models\User;

function paidOrder(User $creator, SalesOrderType $type, int $total): void
{
    SalesOrder::factory()->create([
        'type' => $type,
        'status' => SalesOrderStatus::Paid,
        'total_idr' => $total,
        'created_by' => $creator->id,
        'sales_id' => $creator->role === 'sales' ? $creator->id : null,
    ]);
}

beforeEach(function () {
    $this->sales = User::factory()->sales()->create();
    $this->cashier = User::factory()->cashier()->create();

    paidOrder($this->sales, SalesOrderType::CarSales, 5_000);
    paidOrder($this->cashier, SalesOrderType::SparePart, 700);
    paidOrder(User::factory()->create(['role' => 'super-admin']), SalesOrderType::Service, 40);
});

test('ringkasan laporan hanya menghitung data sesuai role', function () {
    $overview = fn (User $user) => $this->actingAs($user)->get(route('admin.reports.index'))->assertOk();

    $overview(User::factory()->manager()->create())->assertViewHas('totalRevenue', 5_740)->assertSee('Bengkel & Servis WO', false);
    $overview($this->sales)->assertViewHas('totalRevenue', 5_000)->assertDontSee('Bengkel & Servis WO', false)->assertDontSee('Suku Cadang & Kasir POS', false);
    $overview($this->cashier)->assertViewHas('totalRevenue', 700)->assertDontSee('Bengkel & Servis WO', false);
});

test('gudang diarahkan ke laporan inventori tanpa melihat omzet', function () {
    $this->actingAs(User::factory()->create(['role' => 'inventory']))
        ->get(route('admin.reports.index'))
        ->assertRedirect(route('admin.reports.inventory'));
});
