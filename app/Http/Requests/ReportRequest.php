<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            'to' => 'sometimes|date|after_or_equal:startDate',
        ];
    }
    public function messages(): array
    {
        return [
            'period.required' => 'El campo período es obligatorio.',
            'period.in' => 'El campo período debe ser uno de los siguientes: week, month, custom, historical.',
            'from.date' => 'El campo from debe ser una fecha válida.',
            'to.date' => 'El campo to debe ser una fecha válida.',
            'to.after_or_equal' => 'La fecha to debe ser igual o posterior a la fecha from.',
        ];
    }
}
