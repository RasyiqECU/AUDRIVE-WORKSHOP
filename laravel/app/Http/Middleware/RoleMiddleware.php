<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Super Admin.']);
        }

        $userRole = $user->role;

        // Parsing daftar role yang diizinkan (mendukung format pipe '|', koma ',', atau multi-argument)
        $allowedRoles = [];
        foreach ($roles as $role) {
            if (str_contains($role, '|')) {
                $allowedRoles = array_merge($allowedRoles, explode('|', $role));
            } elseif (str_contains($role, ',')) {
                $allowedRoles = array_merge($allowedRoles, explode(',', $role));
            } else {
                $allowedRoles[] = $role;
            }
        }
        $allowedRoles = array_values(array_unique(array_filter(array_map('trim', $allowedRoles))));

        $isCustomerRouteOnly = count($allowedRoles) === 1 && in_array('customer', $allowedRoles, true);

        // Jika rute hanya khusus customer dan yang login adalah staff/admin, alihkan ke admin dashboard
        if ($isCustomerRouteOnly && $userRole !== 'customer') {
            if ($userRole === 'mechanic') {
                return redirect()->route('workshop.index');
            }
            return redirect()->route('admin.dashboard');
        }

        // Super-admin memiliki akses penuh ke seluruh rute admin
        if ($userRole === 'super-admin') {
            return $next($request);
        }

        // Manager memiliki akses ke semua menu admin/staf kecuali yang dikhususkan hanya untuk super-admin
        if ($userRole === 'manager') {
            if (!in_array('super-admin', $allowedRoles, true) || in_array('manager', $allowedRoles, true)) {
                return $next($request);
            }
        }

        // Cek apakah role pengguna termasuk dalam role yang diizinkan
        if (in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
