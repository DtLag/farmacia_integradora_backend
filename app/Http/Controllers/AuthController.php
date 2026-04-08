<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginCustomerRequest;
use App\Http\Requests\Auth\LoginStaffRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Http\Requests\Auth\RegisterStaffRequest;
use App\Http\Resources\Auth\CustomerResource;
use App\Http\Resources\Auth\LoginResponseResource;
use App\Http\Resources\Auth\StaffResource;
use App\Models\User;
use App\Models\Customer;
use App\Traits\PerformsLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendVerificationCode;

class AuthController extends Controller
{
    use PerformsLogin;

    // --- STAFF ---

    public function loginStaff(LoginStaffRequest $request)
    {
        $result = $this->performStaffLogin($request->validated());

        return new LoginResponseResource([
            'token' => $result['token'],
            'user' => new StaffResource($result['user']),
        ]);
    }

    // Registrar un nuevo staff
    public function registerStaff(RegisterStaffRequest $request)
    {
        // Verificar si el usuario autenticado es admin
        if ($request->user()->role->slug !== 'admin') {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_id' => $validated['user_id'],
            'role_id' => $validated['role_id'],
        ]);

        return response()->json([
            'message' => 'Usuario de staff creado exitosamente',
            'user' => $user->load('role'),
        ], 201);
    }

    // --- CUSTOMERS ---

    public function registerCustomer(RegisterCustomerRequest $request)
    {
        $code = random_int(1000, 9999);
        $validated = $request->validated();

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'verification_code' => Hash::make($code),
            'verification_code_expires_at' => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        Mail::to($customer->email)->send(new SendVerificationCode($code));

        return response()->json([
            'user' => $customer,
        ], 201);
    }

    public function loginCustomer(LoginCustomerRequest $request)
    {
        $result = $this->performCustomerLogin($request->validated());

        return new LoginResponseResource([
            'token' => $result['token'],
            'user' => new CustomerResource($result['user']),
        ]);
    }
}
