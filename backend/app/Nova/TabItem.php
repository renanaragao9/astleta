<?php

namespace App\Nova;

use App\Models\TabItem as TabItemModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class TabItem extends Resource
{
    public static $model = TabItemModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
        'observation',
    ];

    public static function label(): string
    {
        return 'Itens da Comanda';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Produto', 'product', Product::class)
                ->sortable(),

            Number::make('Quantidade', 'quantity')
                ->sortable()
                ->rules('required', 'integer', 'min:1'),

            Currency::make('Total', 'total')
                ->currency('BRL')
                ->sortable()
                ->rules('required', 'numeric', 'min:0')
                ->step(0.01),

            Textarea::make('Observação', 'observation')
                ->nullable(),

            BelongsTo::make('Comanda', 'tab', Tab::class)
                ->sortable(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [];
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource): string
    {
        return "/resources/tabs/{$resource->tab_id}";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return "/resources/tabs/{$resource->tab_id}";
    }
}
