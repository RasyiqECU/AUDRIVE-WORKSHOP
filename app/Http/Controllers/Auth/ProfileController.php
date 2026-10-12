<?php


declare(strict_types=1);


namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('auth.profile', [
            'user' => $request->user(),
            'customer' => $request->user()->customer,
        ]);
    }


    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();


        $user->fill($request->validated());


        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }


        $user->save();


        // Update customer data jika ada
        if ($user->customer) {
            $user->customer->update([
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'birth_date' => $request->birth_date,
            ]);
        }


        return redirect()->route('profile.edit')->with('status', 'Profile updated successfully.');
    }


    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);


        return redirect()->route('profile.edit')->with('status', 'Password updated successfully.');
    }
}