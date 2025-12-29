<?php

namespace App\Http\Requests\Api\Athlete\Racha;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexAthleteRachaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'booking_status' => 'nullable|string|in:pendente,confirmado,cancelado,concluido',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'Busca não pode ter mais de 255 caracteres.',
            'booking_status.in' => 'Status inválido. Use: pendente, confirmado, cancelado ou concluido.',
            'start_date.date' => 'Data inicial deve ser uma data válida.',
            'end_date.date' => 'Data final deve ser uma data válida.',
            'end_date.after_or_equal' => 'Data final deve ser igual ou posterior à data inicial.',
        ];
    }
}
