<?php

namespace App\Http\Requests\Api\Company\TabItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTabItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'sometimes|required|integer|min:1',
            'total' => 'sometimes|required|numeric|min:0.01',
            'observation' => 'sometimes|nullable|string|max:500',
            'tab_id' => 'sometimes|required|integer|exists:tabs,id',
            'product_id' => 'sometimes|required|integer|exists:products,id',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade deve ser pelo menos 1.',
            'total.required' => 'O total é obrigatório.',
            'total.numeric' => 'O total deve ser um valor numérico.',
            'total.min' => 'O total deve ser maior que zero.',
            'observation.string' => 'A observação deve ser uma string válida.',
            'observation.max' => 'A observação não pode ter mais de 500 caracteres.',
            'tab_id.required' => 'A comanda é obrigatória.',
            'tab_id.integer' => 'O ID da comanda deve ser um número inteiro.',
            'tab_id.exists' => 'A comanda selecionada não existe.',
            'product_id.required' => 'O produto é obrigatório.',
            'product_id.integer' => 'O ID do produto deve ser um número inteiro.',
            'product_id.exists' => 'O produto selecionado não existe.',
        ];
    }
}
