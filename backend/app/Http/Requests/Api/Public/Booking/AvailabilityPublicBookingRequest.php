<?php

namespace App\Http\Requests\Api\Public\Booking;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityPublicBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'field_id' => 'required|integer|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'field_id.required' => 'Campo é obrigatório.',
            'field_id.integer' => 'Campo deve ser um número inteiro.',
            'field_id.exists' => 'Campo selecionado não existe.',
            'date.required' => 'Data é obrigatória.',
            'date.date' => 'Data deve ser uma data válida.',
            'date.after_or_equal' => 'Data deve ser hoje ou uma data futura.',
        ];
    }
}
