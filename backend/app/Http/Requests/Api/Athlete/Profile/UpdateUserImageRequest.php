<?php

namespace App\Http\Requests\Api\Athlete\Profile;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateUserImageRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:4608',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'A imagem é obrigatória.',
            'image.image' => 'O arquivo deve ser uma imagem válida.',
            'image.mimes' => 'A imagem deve ser dos tipos: jpeg, png, jpg ou webp.',
            'image.max' => 'A imagem não pode ser maior que 4.5MB.',
        ];
    }
}
