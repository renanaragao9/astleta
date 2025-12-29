<?php

namespace App\Http\Requests\Api\Company\Booking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'bookingStatus' => [
                'required',
                'string',
                Rule::in(['pendente', 'confirmado', 'cancelado', 'concluido']),
            ],
            'cancellation_reason' => [
                'nullable',
                'string',
                'required_if:bookingStatus,cancelado',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'bookingStatus.required' => 'O status da reserva é obrigatório.',
            'bookingStatus.string' => 'O status da reserva deve ser uma string.',
            'bookingStatus.in' => 'O status da reserva deve ser: pendente, confirmado, cancelado ou concluido.',
            'cancellation_reason.required_if' => 'A razão de cancelamento é obrigatória quando o status for cancelado.',
            'cancellation_reason.string' => 'A razão de cancelamento deve ser uma string.',
            'cancellation_reason.max' => 'A razão de cancelamento não pode exceder 255 caracteres.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $booking = $this->route('booking');

            if ($booking && $booking->booking_status === 'cancelado') {
                $validator->errors()->add('bookingStatus', 'Não é possível alterar o status de uma reserva cancelada.');
            }
        });
    }
}
