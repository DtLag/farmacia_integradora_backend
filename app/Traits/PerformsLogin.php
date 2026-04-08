<?php

namespace App\Traits;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

trait PerformsLogin
{
    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array{token: string, user: \App\Models\User}
     */
    protected function performStaffLogin(array $credentials): array
    {
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        $user->load('role');

        if (! $user->role || $user->role === 'customer') {
            throw ValidationException::withMessages([
                'email' => ['No eres staff. Usa el login de clientes.'],
            ]);
        }

        return [
            'token' => $user->createToken('staff-token')->plainTextToken,
            'user' => $user,
        ];
    }

    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array{token: string, user: \App\Models\Customer}
     */
    protected function performCustomerLogin(array $credentials): array
    {
        $customer = Customer::where('email', $credentials['email'])->first();

        if (! $customer || ! Hash::check($credentials['password'], $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        if (! $customer->is_verified) {
            throw ValidationException::withMessages([
                'email' => ['Debes verificar tu correo antes de iniciar sesión.'],
            ]);
        }

        return [
            'token' => $customer->createToken('customer-token')->plainTextToken,
            'user' => $customer,
        ];
    }
}
