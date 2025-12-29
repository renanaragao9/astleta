<?php

namespace App\Http\Requests\Api\Company\Booking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class GetByMonthBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'year' => 'nullable|integer|min:1900|max:'.(now()->year + 10),
            'month' => 'nullable|integer|min:1|max:12',
        ];
    }

    public function messages(): array
    {
        return [
            'year.integer' => 'Ano deve ser um número inteiro.',
            'year.min' => 'Ano deve ser pelo menos 1900.',
            'year.max' => 'Ano não pode ser superior a '.(now()->year + 10).'.',
            'month.integer' => 'Mês deve ser um número inteiro.',
            'month.min' => 'Mês deve ser pelo menos 1.',
            'month.max' => 'Mês deve ser no máximo 12.',
        ];
    }
}
