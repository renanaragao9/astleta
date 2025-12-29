<?php

namespace App\Nova\Filters\Product;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\DateFilter;

class ProductCreatedAtToFilter extends DateFilter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->whereDate('products.created_at', '<=', $value);
    }

    public function name(): string
    {
        return 'Criado Em (Até)';
    }
}
