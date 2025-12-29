<?php

namespace App\Http\Requests\Api\Company\Booking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'field_id' => 'sometimes|integer|exists:fields,id',
            'booking_date' => 'sometimes|date|after_or_equal:today',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'payment_type' => 'sometimes|string|in:online,presencial',
            'booking_status' => 'sometimes|string|in:pendente,confirmado,cancelado,concluido',
            'payment_form_id' => 'sometimes|integer|exists:payment_forms,id',
            'coupon_id' => 'nullable|integer|exists:coupons,id',
            'is_extra_hour' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:1000',
            'cancellation_reason' => 'nullable|string|max:500',
            'discount_amount' => 'sometimes|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'field_id.exists' => 'Campo selecionado não existe.',
            'booking_date.date' => 'Data da reserva deve ser uma data válida.',
            'booking_date.after_or_equal' => 'Data da reserva não pode ser anterior a hoje.',
            'start_time.date_format' => 'Horário de início deve estar no formato HH:MM.',
            'end_time.date_format' => 'Horário de término deve estar no formato HH:MM.',
            'end_time.after' => 'Horário de término deve ser posterior ao horário de início.',
            'payment_type.in' => 'Tipo de pagamento inválido. Use: online ou presencial.',
            'booking_status.in' => 'Status inválido. Use: pendente, confirmado, cancelado ou concluido.',
            'payment_form_id.exists' => 'Forma de pagamento selecionada não existe.',
            'coupon_id.exists' => 'Cupom selecionado não existe.',
            'notes.max' => 'Observações não podem ter mais de 1000 caracteres.',
            'cancellation_reason.max' => 'Motivo do cancelamento não pode ter mais de 500 caracteres.',
            'discount_amount.numeric' => 'Valor do desconto deve ser um número.',
            'discount_amount.min' => 'Valor do desconto não pode ser negativo.',
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('booking_date') && $this->has('start_time')) {
                $bookingDateTime = $this->booking_date.' '.$this->start_time;
                if (strtotime($bookingDateTime) <= time()) {
                    $validator->errors()->add('start_time', 'A reserva deve ser feita para um horário futuro.');
                }
            }

            if ($this->has('start_time') && $this->has('end_time')) {
                $start = strtotime($this->start_time);
                $end = strtotime($this->end_time);
                $durationMinutes = ($end - $start) / 60;

                if ($durationMinutes < 60) {
                    $validator->errors()->add('end_time', 'A reserva deve ter duração mínima de 1 hora.');
                }

                if ($durationMinutes > 480) {
                    $validator->errors()->add('end_time', 'A reserva não pode ter mais de 8 horas de duração.');
                }
            }

            if ($this->booking_status === 'cancelado' && empty($this->cancellation_reason)) {
                $validator->errors()->add('cancellation_reason', 'Motivo do cancelamento é obrigatório.');
            }
        });
    }
}
