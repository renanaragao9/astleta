<?php

namespace App\Http\Requests\Api\Public\Field;

use Illuminate\Foundation\Http\FormRequest;

class IndexPublicFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'sport_type' => 'nullable|string|max:255',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0|gte:price_min',
            'sort' => 'nullable|string|in:name,price,price_low_to_high,price_high_to_low,company_name,city,newest,oldest,created_at,updated_at',
            'direction' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50',
        ];
    }

    public function attributes(): array
    {
        return [
            'search' => 'busca',
            'city' => 'cidade',
            'state' => 'estado',
            'district' => 'bairro',
            'zipcode' => 'CEP',
            'sport_type' => 'tipo de esporte',
            'field_surface' => 'superfície do campo',
            'field_size' => 'tamanho do campo',
            'price_min' => 'preço mínimo',
            'price_max' => 'preço máximo',
            'is_free_company' => 'empresa gratuita',
            'is_open_company' => 'empresa aberta',
            'has_extra_hour' => 'permite hora extra',
            'sort' => 'ordenação',
            'direction' => 'direção',
            'page' => 'página',
            'per_page' => 'itens por página',
        ];
    }
}
