<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'customer_id'       => 'nullable|exists:customers,id',
            'amount_received'   => 'required_if:payment_method_id,1|numeric|min:0',
            'products'          => 'required|array|min:1',
            'products.*.id'     => 'required|exists:products,id',
            'products.*.amount' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'products.required'          => 'Debe agregar al menos un producto a la venta.',
            'products.*.amount.min'      => 'La cantidad mínima por producto es 1.',
        ];
    }
}