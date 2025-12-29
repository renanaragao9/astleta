<?php

namespace App\Nova;

use App\Models\Feature as FeatureModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Feature extends Resource
{
    public static $model = FeatureModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
    ];

    public static function label(): string
    {
        return 'Características';
    }

    public static function singularLabel(): string
    {
        return 'Característica';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable()->rules('required', 'max:255'),
            BelongsTo::make('Posição', 'position', Position::class)
                ->sortable()
                ->rules('required')
                ->help('Posição relacionada a esta característica.'),
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
