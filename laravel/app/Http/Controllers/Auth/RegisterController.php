<?php


declare(strict_types=1);


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;


class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }


    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'role'              => 'customer',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);


        // Buat customer profile
        Customer::create([
            'user_id'    => $user->id,
            'address'    => $request->address,
            'city'       => $request->city,
            'province'   => $request->province,
            'birth_date' => $request->birth_date,
        ]);


        Auth::login($user);


        return redirect()->route('customer.dashboard');
    }
}



