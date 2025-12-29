<?php

namespace App\Nova\Filters\User;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserGenderFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('gender', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Masculino' => 'masculino',
            'Feminino' => 'feminino',
            'Outro' => 'outro',
            'Prefiro não informar' => 'nao_informado',
        ];
    }

    public function name(): string
    {
        return 'Gênero';
    }
}
