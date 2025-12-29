<?php

namespace App\Nova;

use App\Models\Field as FieldModel;
use App\Nova\Actions\UploadImageToFieldAction;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Field extends Resource
{
    public static $model = FieldModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'description',
    ];

    public static function label(): string
    {
        return 'Campos';
    }

    public static function singularLabel(): string
    {
        return 'Campo';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Image::make('Imagem', 'image_path')
                ->path('fields/images')
                ->disk('s3'),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o nome do campo.'),

            Currency::make('Preço por Hora', 'price_per_hour')
                ->currency('BRL')
                ->rules('required', 'numeric', 'min:0')
                ->help('Digite o preço por hora do campo. Separe as casas decimais por ponto. Exemplo: 120.50'),

            Boolean::make('Permite Hora Extra', 'is_allows_extra_hour')
                ->help('Marque se este campo permite reservas de hora extra.'),

            Currency::make('Preço Hora Extra', 'extra_hour_price')
                ->currency('BRL')
                ->rules('nullable', 'numeric', 'min:0', 'required_if:is_allows_extra_hour,1,true')
                ->help('Digite o preço da hora extra do campo. Separe as casas decimais por ponto. Exemplo: 150.00'),

            BelongsTo::make('Tipo de Campo', 'fieldType', FieldType::class)
                ->rules('required')
                ->help('Selecione o tipo de campo.'),

            BelongsTo::make('Superfície', 'fieldSurface', FieldSurface::class)
                ->rules('required')
                ->help('Selecione a superfície do campo.'),

            BelongsTo::make('Tamanho', 'fieldSize', FieldSize::class)
                ->rules('required')
                ->help('Selecione o tamanho do campo.'),

            Textarea::make('Descrição', 'description')
                ->nullable()
                ->hideFromIndex()
                ->help('Descrição opcional do campo.'),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Selecione a empresa associada.'),

            HasMany::make('Horários do Campo', 'fieldSchedules', FieldSchedule::class),

            BelongsToMany::make('Itens do Campo', 'fieldItems', FieldItem::class)
                ->help('Selecione os itens associados ao campo.'),

            HasMany::make('Imagens', 'fieldImages', FieldImage::class),
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
            new UploadImageToFieldAction,
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
