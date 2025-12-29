<?php

namespace App\Nova;

use App\Models\MarketingType as MarketingTypeModel;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class MarketingType extends Resource
{
    public static $model = MarketingTypeModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
    ];

    public static function label(): string
    {
        return 'Tipos de Marketing';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),

            Text::make('Nome', 'name')
                ->sortable(),

            Textarea::make('Descrição', 'description')
                ->nullable(),
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
