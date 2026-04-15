<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:10', 
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('is_verified', true);
                })
            ],
            'email' => ['required', 'email', 
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('is_verified', true);
                })
            ],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}

