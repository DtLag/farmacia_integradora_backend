<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'codigo' => 'nullable|string|max:255|unique:products,codigo',
            'name' => 'required|string|max:255',
            'presentation' => 'nullable|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0|gte:purchase_price',
            'location' => 'nullable|string|max:255',
            'min_stock' => 'required|integer|min:0|lte:max_stock', 
            'max_stock' => 'nullable|integer|min:0|gte:min_stock',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }
}
