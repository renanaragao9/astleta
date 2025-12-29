<?php

namespace App\Nova\Filters\User;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserProfileFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('profile_id', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Admin' => 1,
            'Atleta' => 2,
            'Empresa' => 3,
            'Árbitro' => 4,
            'Analista' => 5,
        ];
    }

    public function name(): string
    {
        return 'Cargo';
    }
}
