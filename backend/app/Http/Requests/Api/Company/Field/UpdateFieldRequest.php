<?php

namespace App\Http\Requests\Api\Company\Field;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFieldRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price_per_hour' => 'required|numeric|min:0.01',
            'extra_hour_price' => 'nullable|numeric|min:0',
            'field_type_id' => 'required|integer|exists:field_types,id',
            'field_surface_id' => 'required|integer|exists:field_surfaces,id',
            'field_size_id' => 'required|integer|exists:field_sizes,id',
            'is_active' => 'boolean',
            'is_allows_extra_hour' => 'boolean',

            'item_ids' => 'nullable|array|max:20',
            'item_ids.*' => 'exists:field_items,id',

            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|integer|min:1|max:7',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do campo é obrigatório.',
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.string' => 'A descrição deve ser uma string válida.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'price_per_hour.required' => 'O preço por hora é obrigatório.',
            'price_per_hour.numeric' => 'O preço por hora deve ser um valor numérico.',
            'price_per_hour.min' => 'O preço por hora deve ser maior que zero.',
            'field_type_id.required' => 'O tipo do campo é obrigatório.',
            'field_type_id.integer' => 'O tipo do campo deve ser um número inteiro.',
            'field_type_id.exists' => 'O tipo do campo selecionado não existe.',
            'field_surface_id.required' => 'A superfície do campo é obrigatória.',
            'field_surface_id.integer' => 'A superfície do campo deve ser um número inteiro.',
            'field_surface_id.exists' => 'A superfície do campo selecionada não existe.',
            'field_size_id.required' => 'O tamanho do campo é obrigatório.',
            'field_size_id.integer' => 'O tamanho do campo deve ser um número inteiro.',
            'field_size_id.exists' => 'O tamanho do campo selecionado não existe.',
            'is_active.boolean' => 'O status deve ser um valor booleano.',
            'is_allows_extra_hour.boolean' => 'O campo "permite hora extra" deve ser um valor booleano.',

            'item_ids.array' => 'Os itens adicionais devem ser um array.',
            'item_ids.max' => 'Muito espertinho! Máximo 20 itens adicionais.',
            'item_ids.*.exists' => 'Um ou mais itens adicionais são inválidos.',

            'schedules.array' => 'Os horários devem estar em formato de lista.',
            'schedules.*.day_of_week.required' => 'O dia da semana é obrigatório.',
            'schedules.*.day_of_week.integer' => 'O dia da semana deve ser um número entre 1 e 7.',
            'schedules.*.day_of_week.min' => 'O dia da semana deve ser entre 1 (segunda-feira) e 7 (domingo).',
            'schedules.*.day_of_week.max' => 'O dia da semana deve ser entre 1 (segunda-feira) e 7 (domingo).',

            'schedules.*.start_time.required' => 'O horário de início é obrigatório.',
            'schedules.*.start_time.date_format' => 'Formato inválido para horário de início. Use HH:mm.',

            'schedules.*.end_time.required' => 'O horário de término é obrigatório.',
            'schedules.*.end_time.date_format' => 'Formato inválido para horário de término. Use HH:mm.',
            'schedules.*.end_time.after' => 'O horário de término deve ser após o horário de início.',
        ];
    }
}
