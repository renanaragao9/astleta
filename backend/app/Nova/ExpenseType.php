<?php

namespace App\Nova;

use App\Models\ExpenseType as ExpenseTypeModel;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ExpenseType extends Resource
{
    public static $model = ExpenseTypeModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
    ];

    public static function label(): string
    {
        return 'Tipos de Despesa';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable(),
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
}
