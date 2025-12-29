<?php

namespace App\Http\Requests\Api\Company\TabItem;

use Illuminate\Foundation\Http\FormRequest;

class IndexTabItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tab_id' => 'nullable|integer|exists:tabs,id',
            'product_id' => 'nullable|integer|exists:products,id',
            'sort' => 'nullable|string|in:id,quantity,total,tab_id,product_id',
            'direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'tab_id.integer' => 'O ID da comanda deve ser um número inteiro.',
            'tab_id.exists' => 'A comanda selecionada não existe.',
            'product_id.integer' => 'O ID do produto deve ser um número inteiro.',
            'product_id.exists' => 'O produto selecionado não existe.',
            'sort.string' => 'O campo de ordenação deve ser uma string.',
            'sort.in' => 'Campo de ordenação inválido.',
            'direction.string' => 'A direção da ordenação deve ser uma string.',
            'direction.in' => 'A direção deve ser crescente (asc) ou decrescente (desc).',
            'per_page.integer' => 'O número de itens por página deve ser um número inteiro.',
            'per_page.min' => 'O número de itens por página deve ser pelo menos 1.',
            'per_page.max' => 'O número de itens por página não pode ser maior que 100.',
            'page.integer' => 'O número da página deve ser um número inteiro.',
            'page.min' => 'O número da página deve ser pelo menos 1.',
        ];
    }
}
