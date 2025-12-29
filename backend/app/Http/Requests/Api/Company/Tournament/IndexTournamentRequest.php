<?php

namespace App\Http\Requests\Api\Company\Tournament;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexTournamentRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'is_public' => 'nullable',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'O termo de busca não pode ter mais de 255 caracteres.',
            'search.string' => 'O termo de busca deve ser uma string válida.',
            'status.string' => 'O status deve ser uma string válida.',
            'status.max' => 'O status não pode ter mais de 255 caracteres.',
        ];
    }
}