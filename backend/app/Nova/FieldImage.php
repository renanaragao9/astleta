<?php

namespace App\Nova;

use App\Models\FieldImage as FieldImageModel;
use App\Nova\Actions\UploadImageToFieldImageAction;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldImage extends Resource
{
    public static $model = FieldImageModel::class;

    public static $title = 'file';

    public static $search = [
        'id',
        'file',
        'caption',
    ];

    public static function label(): string
    {
        return 'Imagens do Campo';
    }

    public static function singularLabel(): string
    {
        return 'Imagem do Campo';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Campo', 'field', Field::class)
                ->rules('required'),

            Image::make('Imagem', 'image_path')
                ->path('fields/complements/images')
                ->disk('s3'),

            Text::make('Legenda', 'caption')
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
        return [
            new UploadImageToFieldImageAction,
        ];
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
