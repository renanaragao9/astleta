<?php

namespace App\Nova;

use App\Models\Tournament as TournamentModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Tournament extends Resource
{
    public static $model = TournamentModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'status',
    ];

    public static function label(): string
    {
        return 'Torneios';
    }

    public static function singularLabel(): string
    {
        return 'Torneio';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Digite o nome do torneio.'),

            Select::make('Status', 'status')
                ->options([
                    'ativo' => 'Ativo',
                    'inativo' => 'Inativo',
                    'finalizado' => 'Finalizado',
                ])
                ->help('Selecione o status do torneio.'),

            Textarea::make('Descrição', 'description')
                ->nullable()
                ->hideFromIndex()
                ->help('Descrição opcional do torneio.'),

            Text::make('Prêmios', 'awards')
                ->nullable()
                ->hideFromIndex()
                ->help('Prêmios do torneio (opcional).'),

            Text::make('E-mail de Boas-vindas', 'welcome_email')
                ->nullable()
                ->hideFromIndex()
                ->help('E-mail de boas-vindas (opcional).'),

            Date::make('Data de Início', 'start_date')
                ->rules('required')
                ->help('Selecione a data de início.'),

            Date::make('Data de Fim', 'end_date')
                ->rules('required')
                ->help('Selecione a data de fim.'),

            Number::make('Máximo de Participantes', 'max_participants')
                ->rules('required', 'integer', 'min:1')
                ->help('Digite o número máximo de participantes.'),

            Boolean::make('Público', 'is_public')
                ->default(false)
                ->help('Marque se o torneio é público.'),

            BelongsTo::make('Empresa', 'company', Company::class)
                ->rules('required')
                ->help('Selecione a empresa.'),

            HasMany::make('Equipes do Torneio', 'tournamentTeams', TournamentTeam::class),
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
        return "/resources/companies/{$resource->company_id}";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource): string
    {
        return "/resources/companies/{$resource->company_id}";
    }
}