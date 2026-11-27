<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('setiap role bisa membuka profil, memperbarui data, dan mengganti password', function (string $role) {
    $user = User::factory()->create(['role' => $role]);
    if ($role === 'customer') {
        Customer::factory()->create(['user_id' => $user->id]);
    }

    $this->actingAs($user)->get(route('profile.edit'))->assertOk()->assertSee('Ubah Password');
    $this->actingAs($user)
        ->patch(route('profile.update'), ['name' => 'Nama Baru', 'email' => $user->email, 'phone' => $user->phone])
        ->assertRedirect(route('profile.edit'));
    $this->actingAs($user)
        ->put(route('profile.password.update'), [
            'current_password' => 'password',
            'password' => 'rahasia-baru-123',
            'password_confirmation' => 'rahasia-baru-123',
        ])
        ->assertSessionHasNoErrors();

    expect($user->fresh()->name)->toBe('Nama Baru')
        ->and(Hash::check('rahasia-baru-123', $user->fresh()->password))->toBeTrue()
        ->and($user->fresh()->role)->toBe($role);
})->with(['super-admin', 'owner', 'manager', 'sales', 'cashier', 'inventory', 'mechanic', 'customer']);

test('ganti password ditolak bila password lama salah', function () {
    $user = User::factory()->sales()->create();

    $this->actingAs($user)
        ->put(route('profile.password.update'), [
            'current_password' => 'salah',
            'password' => 'rahasia-baru-123',
            'password_confirmation' => 'rahasia-baru-123',
        ])
        ->assertSessionHasErrors('current_password');

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});
