<?php

namespace App\Nova;

use App\Models\Address as AddressModel;
use Customfields\Cep\Cep;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Address extends Resource
{
    public static $model = AddressModel::class;

    public static $title = 'street';

    public static $search = [
        'id',
        'street',
        'city',
        'zipcode',
    ];

    public static function label(): string
    {
        return 'Endereços';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Cep::make('CEP', 'zipcode')
                ->sortable()
                ->rules('required', 'max:10')
                ->help('Digite o CEP do endereço (ex: 12345-678). O campo será formatado automaticamente.'),

            BelongsTo::make('Tipo de Endereço', 'addressType', AddressType::class)
                ->sortable()
                ->help('Selecione o tipo de endereço (residencial, comercial, etc.)'),

            Text::make('Estado', 'state')
                ->sortable()
                ->rules('required', 'max:100')
                ->help('Digite o nome do estado ou unidade federativa.'),

            Text::make('Cidade', 'city')
                ->sortable()
                ->rules('required', 'max:100')
                ->help('Digite o nome da cidade.'),

            Text::make('Bairro', 'district')
                ->nullable()
                ->sortable()
                ->rules('nullable', 'max:100')
                ->help('Digite o nome do bairro ou distrito (opcional).'),

            Text::make('Rua', 'street')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o nome da rua, avenida ou logradouro.'),

            Text::make('Número', 'number')
                ->nullable()
                ->rules('nullable', 'max:20')
                ->help('Digite o número do imóvel (opcional).'),

            Text::make('Complemento', 'complement')
                ->nullable()
                ->rules('nullable', 'max:255')
                ->help('Adicione informações complementares como apartamento, bloco, etc. (opcional).'),

            Hidden::make('País', 'country')
                ->default('Brasil')
                ->sortable(),

            Hidden::make('Latitude', 'latitude')
                ->nullable(),

            Hidden::make('Longitude', 'longitude')
                ->nullable(),

            MorphTo::make('Relacionado', 'addressable')->types([
                User::class,
                Company::class,
            ])->sortable()
                ->help('Selecione o usuário ao qual este endereço está relacionado.'),

            Text::make('Endereço Completo')
                ->onlyOnDetail()
                ->resolveUsing(fn () => $this->resource->full_address),
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
