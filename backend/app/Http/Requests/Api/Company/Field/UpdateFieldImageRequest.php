<?php

namespace App\Http\Requests\Api\Company\Field;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFieldImageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:4120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'A imagem é obrigatória.',
            'image.image' => 'O arquivo deve ser uma imagem válida.',
            'image.mimes' => 'A imagem deve ser dos tipos: jpeg, png, jpg ou webp.',
            'image.max' => 'A imagem não pode ser maior que 4MB.',
        ];
    }
}
