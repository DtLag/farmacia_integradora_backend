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
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Usuario de staff creado exitosamente',
            'user' => $user,
        ], 201);
    }

    // --- CUSTOMERS ---

    public function registerCustomer(RegisterCustomerRequest $request)
    {
        $validated = $request->validated();

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'token' => $token,
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
