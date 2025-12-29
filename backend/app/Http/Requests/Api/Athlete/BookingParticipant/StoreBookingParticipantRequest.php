<?php

namespace App\Http\Requests\Api\Athlete\BookingParticipant;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreBookingParticipantRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required_without:user_phone|string|max:255',
            'user_phone' => 'required_without:name|string|exists:users,phone',
            'phone' => 'nullable|string|max:20',
            'amount_paid' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pendente,confirmado,cancelado',
            'booking_id' => 'required|integer|exists:bookings,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required_without' => 'Nome é obrigatório quando telefone do usuário não for informado.',
            'name.string' => 'Nome deve ser uma string.',
            'name.max' => 'Nome não pode ter mais de 255 caracteres.',
            'user_phone.required_without' => 'Telefone do usuário é obrigatório quando nome não for informado.',
            'user_phone.regex' => 'Telefone do usuário deve conter apenas dígitos (10 ou 11 números).',
            'user_phone.exists' => 'Telefone do usuário não encontrado.',
            'phone.string' => 'Telefone deve ser uma string.',
            'phone.max' => 'Telefone não pode ter mais de 20 caracteres.',
            'amount_paid.numeric' => 'Valor pago deve ser um número.',
            'amount_paid.min' => 'Valor pago não pode ser negativo.',
            'status.in' => 'Status inválido. Use: pendente, confirmado ou cancelado.',
            'user_id.integer' => 'ID do usuário deve ser um número inteiro.',
            'user_id.exists' => 'Usuário selecionado não existe.',
            'user_id.unique' => 'Este usuário já está participando desta reserva.',
            'booking_id.required' => 'ID da reserva é obrigatório.',
            'booking_id.integer' => 'ID da reserva deve ser um número inteiro.',
            'booking_id.exists' => 'Reserva selecionada não existe.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'booking_id' => $this->route('booking')->id,
            'added_by_user_id' => auth()->id(),
        ]);
    }
}
