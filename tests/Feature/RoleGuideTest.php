<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\RoleAccessMatrixService;

test('jumlah menu di panduan hak akses sama dengan menu sidebar tiap role staf', function (string $role) {
    $this->actingAs(User::factory()->create(['role' => $role]));
    $html = view('components.layout.admin-sidebar')->render();

    expect(count(app(RoleAccessMatrixService::class)->getRole($role)['menus']))
        ->toBe(preg_match_all('/class="sidebar-item[ "]/', $html));
})->with(['super-admin', 'owner', 'manager', 'sales', 'cashier', 'inventory', 'mechanic']);
