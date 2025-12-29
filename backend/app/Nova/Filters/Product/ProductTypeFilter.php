<?php

namespace App\Nova\Filters\Product;

use App\Models\ProductType;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class ProductTypeFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('products.product_type_id', $value);
    }

    public function options(Request $request): array
    {
        $productTypes = ProductType::select('id', 'name')->orderBy('name')->get();

        $options = [];
        foreach ($productTypes as $productType) {
            $options[$productType->name] = $productType->id;
        }

        return $options;
    }

    public function name(): string
    {
        return 'Tipo de Produto';
    }
}
