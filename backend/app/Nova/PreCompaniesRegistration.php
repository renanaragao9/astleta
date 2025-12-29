<?php

namespace App\Nova;

use App\Models\PreCompaniesRegistration as PreCompaniesRegistrationModel;
use Customfields\PhoneNumber\PhoneNumber;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class PreCompaniesRegistration extends Resource
{
    public static $model = PreCompaniesRegistrationModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'email',
        'phone',
    ];

    public static function label(): string
    {
        return 'Pré-registros de Empresas';
    }

    public static function singularLabel(): string
    {
        return 'Pré-registro de Empresa';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Hidden::make('UUID', 'uuid')
                ->default(fn () => Str::uuid())
                ->hideFromIndex(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o nome da empresa.'),

            Text::make('Email', 'email')
                ->sortable()
                ->rules('required', 'email', 'unique:pre_companies_registrations,email'.($request->resourceId ? ','.$request->resourceId.',id' : ''), 'max:255')
                ->help('Digite o email de contato.'),

            PhoneNumber::make('Telefone', 'phone')
                ->rules('required', 'unique:pre_companies_registrations,phone'.($request->resourceId ? ','.$request->resourceId.',id' : ''), 'max:20')
                ->help('Digite o número de telefone.'),

            Textarea::make('Descrição', 'description')
                ->nullable()
                ->hideFromIndex()
                ->help('Descrição opcional do pré-registro.'),
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
