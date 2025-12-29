<?php

namespace App\Http\Requests\Api\Company\Booking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class SendBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'booking_id' => 'required|integer|exists:bookings,id',
            'send_method' => 'required|string|in:email,system',
            'email' => 'required_if:send_method,email|email|nullable',
            'phone' => 'required_if:send_method,system|string|nullable|exists:users,phone',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'ID da reserva é obrigatório.',
            'booking_id.integer' => 'ID da reserva deve ser um número inteiro.',
            'booking_id.exists' => 'Reserva não encontrada.',
            'send_method.required' => 'Método de envio é obrigatório.',
            'send_method.in' => 'Método de envio deve ser email ou sistema.',
            'email.required_if' => 'Email é obrigatório quando o método de envio é email.',
            'email.email' => 'Email deve ter um formato válido.',
            'phone.required_if' => 'Telefone é obrigatório quando o método de envio é sistema.',
            'phone.regex' => 'Telefone deve ter 11 dígitos.',
            'phone.exists' => 'Telefone não encontrado.',
        ];
    }
}
