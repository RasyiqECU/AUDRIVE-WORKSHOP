<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Fase 4 audit — Super Admin Core: Manajemen Pengguna & Konfigurasi Sistem.
 */
function superAdmin(): User
{
    return User::factory()->superAdmin()->create();
}

test('super admin membuat, mengubah role, dan menghapus user', function () {
    $admin = superAdmin();

    // Buat user kasir baru
    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Kasir Shift Pagi',
            'email' => 'kanipag@audrive.test',
            'phone' => '081200011122',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'cashier',
        ])
        ->assertRedirect(route('admin.users.index'));

    $cashier = User::where('email', 'kanipag@audrive.test')->first();
    expect($cashier)->not->toBeNull('User seharusnya tersimpan');
    expect($cashier->role)->toBe('cashier');
    expect($cashier->is_active)->toBeTrue();

    // Ubah role menjadi inventory
    $this->actingAs($admin)
        ->patch(route('admin.users.update', $cashier), [
            'name' => 'Kasir Shift Pagi',
            'email' => 'kanipag@audrive.test',
            'role' => 'inventory',
        ])
        ->assertRedirect(route('admin.users.index'));
    expect($cashier->fresh()->role)->toBe('inventory');

    // Hapus user
    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $cashier))
        ->assertRedirect(route('admin.users.index'));
    expect(User::find($cashier->id))->toBeNull();
});

test('manajemen non-super-admin ditolak 403', function () {
    $manager = User::factory()->manager()->create();

    $this->actingAs($manager)->get(route('admin.users.index'))->assertStatus(403);
    $this->actingAs($manager)->get(route('admin.users.create'))->assertStatus(403);
    $this->actingAs($manager)->get(route('admin.settings.index'))->assertStatus(403);
});

test('konfigurasi sistem: update nama aplikasi dan reset kembali ke default', function () {
    $admin = superAdmin();
    $defaultName = SettingService::DEFAULT_APP_NAME;

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'app_name' => 'AUDRIVE QA Edition',
        ])
        ->assertRedirect(route('admin.settings.index'));

    expect(SettingService::getName())->toBe('AUDRIVE QA Edition');

    // Reset kembali nama aplikasi ke default
    $this->actingAs($admin)->post(route('admin.settings.reset'), ['item' => 'name']);

    expect(SettingService::getName())->toBe($defaultName);

    // Halaman settings tetap 200
    $this->actingAs($admin)->get(route('admin.settings.index'))->assertOk();
});
