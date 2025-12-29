<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Classe base para FormRequests da API que agrupa múltiplos erros de validação
 * em uma única mensagem principal.
 *
 * Como usar:
 * class SeuRequest extends ApiFormRequest
 * {
 *     // Suas regras e mensagens normais
 * }
 *
 * Quando houver múltiplos erros, eles serão agrupados na mensagem principal
 * ao invés de serem retornados separadamente.
 */
abstract class ApiFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $message = count($errors) > 1 ? implode(' ', $errors) : ($errors[0] ?? 'Erro de validação.');

        throw new HttpResponseException(response()->json([
            'message' => $message,
            'errors' => $validator->errors()->toArray(),
        ], 422));
    }
}
