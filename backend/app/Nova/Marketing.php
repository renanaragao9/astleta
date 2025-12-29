<?php

namespace App\Nova;

use App\Models\Marketing as MarketingModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Marketing extends Resource
{
    public static $model = MarketingModel::class;

    public static $title = 'title';

    public static $search = [
        'id',
        'title',
    ];

    public static function label(): string
    {
        return 'Marketings';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),

            Text::make('Título', 'title')
                ->sortable(),

            Image::make('Imagem', 'image_path')
                ->path('marketings/images')
                ->disk('s3')
                ->nullable(),

            Text::make('Link', 'link')
                ->nullable(),

            Textarea::make('Conteúdo', 'content')
                ->nullable(),

            Date::make('Data de Início', 'start_date')
                ->nullable(),

            Date::make('Data de Fim', 'end_date')
                ->nullable(),

            Number::make('Idade', 'age')
                ->default(0),

            BelongsTo::make('Tipo de Marketing', 'marketingType', MarketingType::class)
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
}
