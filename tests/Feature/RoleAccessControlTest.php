<?php

declare(strict_types=1);

use App\Enums\PurchaseOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('super admin has unrestricted access to all modules', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)->get(route('admin.cars.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.users.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.pos.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.settings.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.reports.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.reports.sales'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.reports.workshop'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.reports.inventory'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.reports.finance'))->assertOk();
});

test('manager has oversight view access but is blocked from pos and direct crud', function () {
    $manager = User::factory()->manager()->create();

    // View routes allowed
    $this->actingAs($manager)->get(route('admin.cars.index'))->assertOk();
    $this->actingAs($manager)->get(route('admin.spare-parts.index'))->assertOk();
    $this->actingAs($manager)->get(route('admin.service-bookings.index'))->assertOk();
    $this->actingAs($manager)->get(route('admin.work-orders.index'))->assertOk();
    $this->actingAs($manager)->get(route('admin.reports.index'))->assertOk();
    $this->actingAs($manager)->get(route('admin.reports.workshop'))->assertOk();

    // Forbidden routes
    $this->actingAs($manager)->get(route('admin.pos.index'))->assertForbidden();
    $this->actingAs($manager)->get(route('admin.cars.create'))->assertForbidden();
    $this->actingAs($manager)->get(route('admin.spare-parts.create'))->assertForbidden();
    $this->actingAs($manager)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($manager)->get(route('admin.settings.index'))->assertForbidden();
});

test('sales can create cars and view offers but cannot access pos or delete cars', function () {
    $sales = User::factory()->sales()->create();
    $car = CarUnit::factory()->create();

    // Allowed
    $this->actingAs($sales)->get(route('admin.cars.index'))->assertOk();
    $this->actingAs($sales)->get(route('admin.cars.create'))->assertOk();
    $this->actingAs($sales)->get(route('admin.cars.edit', $car))->assertOk();
    $this->actingAs($sales)->get(route('admin.offers.index'))->assertOk();
    $this->actingAs($sales)->get(route('admin.reports.sales'))->assertOk();

    // Forbidden
    $this->actingAs($sales)->get(route('admin.pos.index'))->assertForbidden();
    $this->actingAs($sales)->delete(route('admin.cars.destroy', $car))->assertForbidden();
    $this->actingAs($sales)->get(route('admin.work-orders.index'))->assertForbidden();
    $this->actingAs($sales)->get(route('admin.spare-parts.create'))->assertForbidden();
    $this->actingAs($sales)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($sales)->get(route('admin.settings.index'))->assertForbidden();
    $this->actingAs($sales)->get(route('admin.customers.index'))->assertForbidden();
});

test('cashier can access pos and work order creation/delivery but is blocked from showroom', function () {
    $cashier = User::factory()->cashier()->create();

    // Allowed
    $this->actingAs($cashier)->get(route('admin.pos.index'))->assertOk();
    $this->actingAs($cashier)->get(route('admin.service-bookings.index'))->assertOk();
    $this->actingAs($cashier)->get(route('admin.work-orders.index'))->assertOk();
    $this->actingAs($cashier)->get(route('admin.reports.finance'))->assertOk();

    // Forbidden
    $this->actingAs($cashier)->get(route('admin.cars.index'))->assertForbidden();
    $this->actingAs($cashier)->get(route('admin.offers.index'))->assertForbidden();
    $this->actingAs($cashier)->get(route('admin.spare-parts.create'))->assertForbidden();
    $this->actingAs($cashier)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($cashier)->get(route('admin.settings.index'))->assertForbidden();
    $this->actingAs($cashier)->get(route('admin.reports.workshop'))->assertForbidden();
});

test('inventory staff can manage spare parts and stock but is blocked from pos and showroom', function () {
    $inventory = User::factory()->inventory()->create();

    // Allowed
    $this->actingAs($inventory)->get(route('admin.spare-parts.index'))->assertOk();
    $this->actingAs($inventory)->get(route('admin.spare-parts.create'))->assertOk();
    $this->actingAs($inventory)->get(route('admin.inventory.index'))->assertOk();
    $this->actingAs($inventory)->get(route('admin.reports.inventory'))->assertOk();

    // Forbidden
    $this->actingAs($inventory)->get(route('admin.cars.index'))->assertForbidden();
    $this->actingAs($inventory)->get(route('admin.offers.index'))->assertForbidden();
    $this->actingAs($inventory)->get(route('admin.pos.index'))->assertForbidden();
    $this->actingAs($inventory)->get(route('admin.work-orders.index'))->assertForbidden();
    $this->actingAs($inventory)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($inventory)->get(route('admin.settings.index'))->assertForbidden();
});

test('mechanic is strictly scoped to own work orders and blocked from commercial modules', function () {
    $mechanic = User::factory()->mechanic()->create();
    $otherMechanic = User::factory()->mechanic()->create();

    $customer = Customer::factory()->create();
    $vehicle = CustomerVehicle::factory()->create(['customer_id' => $customer->id]);

    $ownWo = WorkOrder::factory()->create([
        'customer_vehicle_id' => $vehicle->id,
        'assigned_mechanic_id' => $mechanic->id,
    ]);
    $otherWo = WorkOrder::factory()->create([
        'customer_vehicle_id' => $vehicle->id,
        'assigned_mechanic_id' => $otherMechanic->id,
    ]);

    // Work order scope
    $response = $this->actingAs($mechanic)->get(route('admin.work-orders.index'));
    $response->assertOk()
        ->assertSee($ownWo->code)
        ->assertDontSee($otherWo->code);

    $this->actingAs($mechanic)->get(route('admin.work-orders.show', $ownWo))->assertOk();
    $this->actingAs($mechanic)->get(route('admin.work-orders.show', $otherWo))->assertForbidden();

    // Can view spare parts list
    $this->actingAs($mechanic)->get(route('admin.spare-parts.index'))->assertOk();

    // Forbidden
    $this->actingAs($mechanic)->get(route('admin.pos.index'))->assertForbidden();
    $this->actingAs($mechanic)->get(route('admin.cars.index'))->assertForbidden();
    $this->actingAs($mechanic)->get(route('admin.offers.index'))->assertForbidden();
    $this->actingAs($mechanic)->get(route('admin.service-bookings.index'))->assertForbidden();
    $this->actingAs($mechanic)->get(route('admin.spare-parts.create'))->assertForbidden();
    $this->actingAs($mechanic)->get(route('admin.reports.index'))->assertForbidden();
    $this->actingAs($mechanic)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($mechanic)->get(route('admin.settings.index'))->assertForbidden();
});

test('sales invoice access is scoped to car sales invoices only', function () {
    $sales = User::factory()->sales()->create();
    $cashier = User::factory()->cashier()->create();

    $customer = Customer::factory()->create();

    $carSalesOrder = SalesOrder::factory()->create([
        'customer_id' => $customer->id,
        'type' => SalesOrderType::CarSales,
    ]);
    $serviceSalesOrder = SalesOrder::factory()->create([
        'customer_id' => $customer->id,
        'type' => SalesOrderType::Service,
    ]);

    $carSaleInvoice = Invoice::factory()->create([
        'invoiceable_type' => SalesOrder::class,
        'invoiceable_id' => $carSalesOrder->id,
    ]);
    $serviceInvoice = Invoice::factory()->create([
        'invoiceable_type' => SalesOrder::class,
        'invoiceable_id' => $serviceSalesOrder->id,
    ]);

    // Sales can view car sale invoice
    $this->actingAs($sales)->get(route('admin.invoices.show', $carSaleInvoice))->assertOk();

    // Sales gets 403 on workshop/service invoice
    $this->actingAs($sales)->get(route('admin.invoices.show', $serviceInvoice))->assertForbidden();

    // Cashier can view both
    $this->actingAs($cashier)->get(route('admin.invoices.show', $carSaleInvoice))->assertOk();
    $this->actingAs($cashier)->get(route('admin.invoices.show', $serviceInvoice))->assertOk();
});

test('super admin can view user details with comprehensive role access matrix and authority guide', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $salesUser = User::factory()->sales()->create(['name' => 'Budi Sales']);
    $mechanicUser = User::factory()->mechanic()->create(['name' => 'Doni Mekanik']);

    // Index view has role guide matrix
    $responseIndex = $this->actingAs($superAdmin)->get(route('admin.users.index'));
    $responseIndex->assertOk()
        ->assertSee('MANAJEMEN PENGGUNA')
        ->assertSee('Panduan Hak Akses')
        ->assertSee('PANDUAN MATRIKS HAK AKSES SISTEM')
        ->assertSee('Budi Sales')
        ->assertSee('Doni Mekanik');

    // Show view for Sales user displays role authority details
    $responseSales = $this->actingAs($superAdmin)->get(route('admin.users.show', $salesUser));
    $responseSales->assertOk()
        ->assertSee('KETERANGAN HAK AKSES ROLE')
        ->assertSee('SALES EXECUTIVE')
        ->assertSee('Tindakan yang Diizinkan')
        ->assertSee('Batasan Ketat Sistem')
        ->assertSee('Unit Supercar')
        ->assertSee('Pesanan Masuk');

    // Show view for Mechanic user displays mechanic authority details
    $responseMechanic = $this->actingAs($superAdmin)->get(route('admin.users.show', $mechanicUser));
    $responseMechanic->assertOk()
        ->assertSee('KETERANGAN HAK AKSES ROLE')
        ->assertSee('MEKANIK / TEKNISI')
        ->assertSee('Work Order')
        ->assertSee('AI Sound Scan');
});

test('admin can view purchase order index and detail without enum type errors', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $po = PurchaseOrder::factory()->create([
        'status' => PurchaseOrderStatus::Draft,
        'supplier_name' => 'Brembo Racing SpA',
    ]);

    $responseIndex = $this->actingAs($superAdmin)->get(route('admin.purchase-orders.index'));
    $responseIndex->assertOk()
        ->assertSee('Brembo Racing SpA')
        ->assertSee('Draft');

    $responseShow = $this->actingAs($superAdmin)->get(route('admin.purchase-orders.show', $po));
    $responseShow->assertOk()
        ->assertSee('PURCHASE ORDER: Brembo Racing SpA')
        ->assertSee('Draft');
});
