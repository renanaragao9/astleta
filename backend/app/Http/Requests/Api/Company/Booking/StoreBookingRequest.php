<?php

namespace App\Http\Requests\Api\Company\Booking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'field_id' => 'required|integer|exists:fields,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'payment_type' => 'required|string|in:online,presencial',
            'booking_status' => 'nullable|string|in:pendente,confirmado,cancelado,concluido',
            'payment_form_id' => 'required|integer|exists:payment_forms,id',
            'coupon_id' => 'nullable|integer|exists:coupons,id',
            'is_extra_hour' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            'discount_amount' => 'nullable|numeric|min:0',
            'user_phone' => 'nullable|string|regex:/^\d{11}$/|exists:users,phone',
        ];
    }

    public function messages(): array
    {
        return [
            'field_id.required' => 'Campo é obrigatório.',
            'field_id.exists' => 'Campo selecionado não existe.',
            'booking_date.required' => 'Data da reserva é obrigatória.',
            'booking_date.date' => 'Data da reserva deve ser uma data válida.',
            'booking_date.after_or_equal' => 'Data da reserva não pode ser anterior a hoje.',
            'start_time.required' => 'Horário de início é obrigatório.',
            'start_time.date_format' => 'Horário de início deve estar no formato HH:MM.',
            'end_time.required' => 'Horário de término é obrigatório.',
            'end_time.date_format' => 'Horário de término deve estar no formato HH:MM.',
            'end_time.after' => 'Horário de término deve ser posterior ao horário de início.',
            'payment_type.required' => 'Tipo de pagamento é obrigatório.',
            'payment_type.in' => 'Tipo de pagamento inválido. Use: online ou presencial.',
            'booking_status.in' => 'Status inválido. Use: pendente, confirmado, cancelado ou concluido.',
            'payment_form_id.required' => 'Forma de pagamento é obrigatória.',
            'payment_form_id.exists' => 'Forma de pagamento selecionada não existe.',
            'coupon_id.exists' => 'Cupom selecionado não existe.',
            'notes.max' => 'Observações não podem ter mais de 1000 caracteres.',
            'discount_amount.numeric' => 'Valor do desconto deve ser um número.',
            'discount_amount.min' => 'Valor do desconto não pode ser negativo.',
            'user_phone.exists' => 'Telefone do usuário não encontrado.',
            'user_phone.regex' => 'Telefone deve ter 11 dígitos.',
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->booking_date && $this->start_time) {
                $bookingDateTime = $this->booking_date.' '.$this->start_time;
                if (strtotime($bookingDateTime) <= time()) {
                    $validator->errors()->add('start_time', 'A reserva deve ser feita para um horário futuro.');
                }
            }

            if ($this->start_time && $this->end_time) {
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
        });
    }
}
