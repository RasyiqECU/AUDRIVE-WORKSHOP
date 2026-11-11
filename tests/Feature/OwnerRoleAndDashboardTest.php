<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user model recognizes owner role', function () {
    $owner = User::factory()->owner()->create();

    expect($owner->isOwner())->toBeTrue()
        ->and($owner->role)->toBe('owner');
});

test('owner can access admin dashboard and see executive reporting view', function () {
    $owner = User::factory()->owner()->create(['name' => 'Pak Bos']);

    $response = $this->actingAs($owner)->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertViewIs('admin.dashboard.owner')
        ->assertSee('Executive Command')
        ->assertSee('BUSINESS OWNER CONSOLE')
        ->assertSee('Pak Bos')
        ->assertSee('Total Omzet Bersih')
        ->assertSee('Showroom Supercar')
        ->assertSee('Workshop & Servis')
        ->assertSee('Spare Parts & Retail');
});

test('owner dashboard supports date presets and custom date range filters', function () {
    $owner = User::factory()->owner()->create();

    // Test with last_month preset
    $responseLastMonth = $this->actingAs($owner)->get(route('admin.dashboard', ['preset' => 'last_month']));
    $responseLastMonth->assertOk()
        ->assertSee('Bulan Lalu');

    // Test with today preset
    $responseToday = $this->actingAs($owner)->get(route('admin.dashboard', ['preset' => 'today']));
    $responseToday->assertOk()
        ->assertSee('Hari Ini');

    // Test with yesterday preset
    $responseYesterday = $this->actingAs($owner)->get(route('admin.dashboard', ['preset' => 'yesterday']));
    $responseYesterday->assertOk()
        ->assertSee('Kemarin');

    // Test with this_year preset
    $responseThisYear = $this->actingAs($owner)->get(route('admin.dashboard', ['preset' => 'this_year']));
    $responseThisYear->assertOk()
        ->assertSee('Tahun Ini');

    // Test with custom date range
    $responseCustom = $this->actingAs($owner)->get(route('admin.dashboard', [
        'preset' => 'custom',
        'start_date' => now()->subDays(10)->format('Y-m-d'),
        'end_date' => now()->format('Y-m-d'),
    ]));
    $responseCustom->assertOk()
        ->assertSee('Menampilkan Pelaporan Periode:');
});

test('owner is strictly forbidden from accessing operational and management routes', function () {
    $owner = User::factory()->owner()->create();

    // Cars management
    $this->actingAs($owner)->get(route('admin.cars.index'))->assertForbidden();

    // User management
    $this->actingAs($owner)->get(route('admin.users.index'))->assertForbidden();

    // POS / Kasir
    $this->actingAs($owner)->get(route('admin.pos.index'))->assertForbidden();

    // Settings
    $this->actingAs($owner)->get(route('admin.settings.index'))->assertForbidden();

    // Customers
    $this->actingAs($owner)->get(route('admin.customers.index'))->assertForbidden();
});

test('owner can access reporting views as view-only', function () {
    $owner = User::factory()->owner()->create();

    $this->actingAs($owner)->get(route('admin.reports.index'))->assertOk();
    $this->actingAs($owner)->get(route('admin.reports.sales'))->assertOk();
    $this->actingAs($owner)->get(route('admin.reports.finance'))->assertOk();
});

test('generic /dashboard redirect redirects owner to admin dashboard', function () {
    $owner = User::factory()->owner()->create();

    $this->actingAs($owner)
        ->get('/dashboard')
        ->assertRedirect(route('admin.dashboard'));
});
