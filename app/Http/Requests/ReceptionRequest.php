<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identifier_batch' => 'required|string|max:255|unique:batches,identifier_batch',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'entry_date' => 'nullable|date',
            'expiration_date' => 'nullable|date|after:entry_date',
            'notes' => 'nullable|string', 

            'products'              => 'required|array|min:1', 
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.amount'     => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.expiration_date' => 'nullable|date|after:entry_date',
        ];
    }
}
