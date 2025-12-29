<?php

namespace App\Nova;

use App\Models\FieldItem as FieldItemModel;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldItem extends Resource
{
    public static $model = FieldItemModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
    ];

    public static function label(): string
    {
        return 'Itens de Campo';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable(),
            Text::make('Ícone', 'icon')->nullable(),
            BelongsToMany::make('Campos', 'fields', Field::class),
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
        return "/resources/fields/{$resource->field_id}";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return "/resources/fields/{$resource->field_id}";
    }
}
