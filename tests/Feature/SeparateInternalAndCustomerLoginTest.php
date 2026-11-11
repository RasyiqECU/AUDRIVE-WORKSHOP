<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer can login via standard public login form', function () {
    $customer = User::factory()->create([
        'email' => 'customer@example.com',
        'password' => bcrypt('password123'),
        'role' => 'customer',
        'is_active' => true,
    ]);

    $response = $this->post(route('login'), [
        'email' => 'customer@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('customer.dashboard'));
    $this->assertAuthenticatedAs($customer);
});

test('non-customer staff and owner are rejected when trying to login via standard public login form', function () {
    $roles = ['super-admin', 'owner', 'manager', 'sales', 'cashier', 'inventory', 'mechanic'];

    foreach ($roles as $role) {
        $staff = User::factory()->create([
            'email' => "{$role}@example.com",
            'password' => bcrypt('password123'),
            'role' => $role,
            'is_active' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => "{$role}@example.com",
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
});

test('staff and owner can login via hidden internal login form', function () {
    $roles = ['super-admin', 'owner', 'manager', 'sales', 'cashier', 'inventory', 'mechanic'];

    foreach ($roles as $role) {
        $staff = User::factory()->create([
            'email' => "internal_{$role}@example.com",
            'password' => bcrypt('password123'),
            'role' => $role,
            'is_active' => true,
        ]);

        $response = $this->post(route('internal.login.store'), [
            'email' => "internal_{$role}@example.com",
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($staff);

        // Logout before next iteration
        $this->post(route('logout'));
        $this->assertGuest();
    }
});

test('customer is rejected when trying to login via hidden internal login form', function () {
    $customer = User::factory()->create([
        'email' => 'customer_intruder@example.com',
        'password' => bcrypt('password123'),
        'role' => 'customer',
        'is_active' => true,
    ]);

    $response = $this->post(route('internal.login.store'), [
        'email' => 'customer_intruder@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('internal users are redirected to internal login form after logout', function () {
    $roles = ['super-admin', 'owner', 'manager', 'sales', 'cashier', 'inventory', 'mechanic'];

    foreach ($roles as $role) {
        $staff = User::factory()->create([
            'email' => "logout_{$role}@example.com",
            'password' => bcrypt('password123'),
            'role' => $role,
            'is_active' => true,
        ]);

        $this->actingAs($staff);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('internal.login'));
        $this->assertGuest();
    }
});

test('customers are redirected to the public landing page after logout', function () {
    $customer = User::factory()->create([
        'email' => 'logout_customer@example.com',
        'password' => bcrypt('password123'),
        'role' => 'customer',
        'is_active' => true,
    ]);

    $this->actingAs($customer);

    $response = $this->post(route('logout'));

    $response->assertRedirect('/');
    $this->assertGuest();
});

test('unauthenticated guests accessing admin routes are redirected to internal login', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('internal.login'));

    $responseCars = $this->get('/admin/cars');
    $responseCars->assertRedirect(route('internal.login'));
});

test('unauthenticated guests accessing customer routes are redirected to standard login', function () {
    $response = $this->get(route('customer.dashboard'));
    $response->assertRedirect(route('login'));
});
