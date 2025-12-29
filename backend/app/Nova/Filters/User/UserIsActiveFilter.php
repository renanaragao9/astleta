<?php

namespace App\Nova\Filters\User;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserIsActiveFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('is_active', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Sim' => 1,
            'Não' => 0,
        ];
    }

    public function name(): string
    {
        return 'Ativo';
    }
}
