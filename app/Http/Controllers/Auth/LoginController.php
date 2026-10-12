<?php


declare(strict_types=1);


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }


    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();


        $request->session()->regenerate();


        $user = Auth::user();


        // Redirect berdasarkan role
        $redirectPath = match($user->role) {
            'super-admin', 'manager', 'sales', 'cashier', 'inventory', 'mechanic' => route('admin.dashboard'),
            default => route('customer.dashboard'),
        };

        // Jika user staff/admin tapi intended url adalah halaman customer portal, jangan redirect ke customer portal
        $intended = session()->get('url.intended');
        if ($intended && $user->role !== 'customer' && str_contains($intended, '/customer')) {
            session()->forget('url.intended');
            return redirect($redirectPath);
        }

        return redirect()->intended($redirectPath);
    }


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();


        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect('/');
    }
}



