<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReceptionRequest extends FormRequest
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
        $code = $this->codigo;

        return [
            'name' => ['sometimes','required','string','max:255'],

            'codigo' => [
                'sometimes','required','string','max:50',
                    Rule::unique('products','codigo')->ignore($code,'codigo'),
                ],

            'purchase_price' => ['sometimes','required','numeric','min:0'],
            'sale_price'     => ['sometimes','required','numeric','min:0'],

            'category_id' => ['sometimes','required','exists:categories,id'],
            'supplier_id' => ['sometimes','required','exists:suppliers,id'],

            'min_stock' => ['sometimes','required','integer','min:0'],
            'max_stock' => ['sometimes','required','integer','gte:min_stock'],

            'location' => ['sometimes','nullable','string','max:100'],
            'description' => ['sometimes','nullable','string'],
            'image' => ['sometimes','nullable','image','max:2048'],
        ];
    }
}
