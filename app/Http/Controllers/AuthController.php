<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // --- STAFF ---

    public function loginStaff(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        $user = Auth::user();

        if (!$user->role || $user->role === 'customer') {
            throw ValidationException::withMessages([
                'email' => ['No eres staff. Usa el login de clientes.'],
            ]);
        }

        $token = $user->createToken('staff-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    // Registrar un nuevo staff
    public function registerStaff(Request $request)
    {
        // Verificar si el usuario autenticado es admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'user_id' => 'required|string|unique:users',
            'role' => 'required|string|in:admin,vendedor',
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_id' => $request->user_id,
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Usuario de staff creado exitosamente',
            'user' => $user,
        ], 201);
    }

    // --- CUSTOMERS ---

    public function registerCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|unique:customers',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => 'required|string|min:8',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $customer,
        ], 201);
    }

    public function loginCustomer(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $customer,
        ]);
    }
}
