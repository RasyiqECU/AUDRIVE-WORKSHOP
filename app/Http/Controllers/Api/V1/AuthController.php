<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends ApiController
{
    /**
     * Register new customer
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'is_active' => true,
        ]);


        // Assign role customer
        $user->assignRole('customer');


        // Create customer profile
        Customer::create([
            'user_id' => $user->id,
            'code' => 'CUS-' . strtoupper(Str::random(6)),
            'address' => $request->address,
            'city' => $request->city,
            'province' => $request->province,
            'birth_date' => $request->birth_date,
        ]);


        // Create token
        $token = $user->createToken('api-token')->plainTextToken;


        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Registration successful', 201);
    }


    /**
     * Login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid credentials', null, 401);
        }


        $user = User::where('email', $request->email)->first();


        if (!$user->is_active) {
            return $this->errorResponse('Account is inactive', null, 403);
        }


        $token = $user->createToken('api-token')->plainTextToken;


        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }


    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();


        return $this->successResponse(null, 'Logout successful');
    }


    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('customer');


        return $this->successResponse([
            'user' => new UserResource($user),
            'customer' => $user->customer,
        ]);
    }
}



