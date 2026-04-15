<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePickUpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'scheduled_time' => ['required','date','after:now'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'products' => ['required','array','min:1'],
            'products.*.product_id' => ['required','exists:products,id'],
            'products.*.amount' => ['required','integer','min:1']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
