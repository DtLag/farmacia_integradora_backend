<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserMetricsRequest extends FormRequest
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
            'period' => 'required|in:week,month,custom,historical',
            'from' => 'sometimes|date',
            'to' => 'sometimes|date|after_or_equal:from',
            'user'=>'required|string'
        ];
    }
}
