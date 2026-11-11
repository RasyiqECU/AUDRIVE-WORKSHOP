<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InternalLoginController extends Controller
{
    /**
     * Show the separate, hidden internal login form
     */
    public function create(): View
    {
        return view('auth.internal-login');
    }

    /**
     * Handle internal staff / management / owner login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Hanya selain customer yang diizinkan masuk di portal internal
        if ($user->role === 'customer') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akses ditolak. Portal ini khusus untuk Staf, Manajemen, dan Business Owner AUDrive. Akun pelanggan silakan login melalui portal utama.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }
}
