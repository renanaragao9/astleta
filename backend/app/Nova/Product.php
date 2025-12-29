<?php

namespace App\Nova;

use App\Models\Product as ProductModel;
use App\Nova\Actions\ToggleProductActiveStatusAction;
use App\Nova\Filters\Product\ProductActiveFilter;
use App\Nova\Filters\Product\ProductCreatedAtFromFilter;
use App\Nova\Filters\Product\ProductCreatedAtToFilter;
use App\Nova\Filters\Product\ProductTypeFilter;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Product extends Resource
{
    public static $model = ProductModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'description',
    ];

    public static function label(): string
    {
        return 'Produtos';
    }

    public static function singularLabel(): string
    {
        return 'Produto';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o nome do produto que será exibido no sistema.'),

            BelongsTo::make('Tipo de Produto', 'productType', ProductType::class)
                ->rules('required')
                ->help('Selecione a categoria ou tipo ao qual este produto pertence.')
                ->sortable(),

            Currency::make('Preço', 'price')
                ->currency('BRL')
                ->rules('required', 'numeric', 'min:0')
                ->help('Digite o preço por hora do campo. Separe as casas decimais por ponto. Exemplo: 120.50'),

            Textarea::make('Descrição', 'description')
                ->nullable()
                ->hideFromIndex()
                ->help('Adicione uma descrição detalhada do produto (opcional).')
                ->sortable(),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Escolha a empresa responsável por este produto.'),

            Boolean::make('Ativo', 'is_active')
                ->default(true)
                ->help('Marque se o produto está disponível para venda ou uso.')
                ->sortable(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new ProductCreatedAtFromFilter,
            new ProductCreatedAtToFilter,
            new ProductTypeFilter,
            new ProductActiveFilter,
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new ToggleProductActiveStatusAction,
        ];
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource): string
    {
        return "/resources/companies/{$resource->company_id}";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return "/resources/companies/{$resource->company_id}";
    }
}
