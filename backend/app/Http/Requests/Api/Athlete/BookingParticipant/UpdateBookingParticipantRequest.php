<?php

namespace App\Http\Requests\Api\Athlete\BookingParticipant;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateBookingParticipantRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'user_phone' => 'nullable|string|regex:/^\d{10,11}$/|exists:users,phone',
            'phone' => 'nullable|string|max:20',
            'amount_paid' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pendente,confirmado,cancelado',
            'is_paid' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Nome deve ser uma string.',
            'name.max' => 'Nome não pode ter mais de 255 caracteres.',
            'user_phone.string' => 'Telefone do usuário deve ser uma string.',
            'user_phone.regex' => 'Telefone do usuário deve conter apenas dígitos (10 ou 11 números).',
            'user_phone.exists' => 'Telefone do usuário não encontrado.',
            'phone.string' => 'Telefone deve ser uma string.',
            'phone.max' => 'Telefone não pode ter mais de 20 caracteres.',
            'amount_paid.numeric' => 'Valor pago deve ser um número.',
            'amount_paid.min' => 'Valor pago não pode ser negativo.',
            'status.in' => 'Status inválido. Use: pendente, confirmado ou cancelado.',
            'is_paid.boolean' => 'Campo pago deve ser verdadeiro ou falso.',
        ];
    }
}
