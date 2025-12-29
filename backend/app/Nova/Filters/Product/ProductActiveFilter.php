<?php

namespace App\Nova\Filters\Product;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class ProductActiveFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('products.is_active', $value);
    }

    public function options(Request $request): array
    {
        return [
            'Ativo' => true,
            'Inativo' => false,
        ];
    }

    public function name(): string
    {
        return 'Status';
    }
}
