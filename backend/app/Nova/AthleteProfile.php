<?php

namespace App\Nova;

use App\Models\AthleteProfile as AthleteProfileModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class AthleteProfile extends Resource
{
    public static $model = AthleteProfileModel::class;

    public static $title = 'user.name';

    public static $search = [
        'id',
        'user.name',
        'user.email',
        'bio',
    ];

    public static $with = ['user'];

    public static function label(): string
    {
        return 'Perfis de Atleta';
    }

    public static function singularLabel(): string
    {
        return 'Perfil de Atleta';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Usuário', 'user', User::class)
                ->sortable()
                ->rules('required')
                ->help('Usuário associado a este perfil de atleta.'),

            BelongsTo::make('Esporte', 'sport', Sport::class)
                ->nullable()
                ->sortable()
                ->help('Esporte principal praticado pelo atleta.'),

            BelongsTo::make('Posição', 'position', Position::class)
                ->nullable()
                ->sortable()
                ->help('Posição principal do atleta.'),

            BelongsTo::make('Subposição', 'subposition', Position::class)
                ->nullable()
                ->hideFromIndex()
                ->help('Posição secundária do atleta.'),

            BelongsTo::make('Característica', 'feature', Feature::class)
                ->nullable()
                ->hideFromIndex()
                ->help('Característica principal do atleta.'),

            BelongsTo::make('Subcaracterística', 'subfeature', Feature::class)
                ->nullable()
                ->hideFromIndex()
                ->help('Característica secundária do atleta.'),

            Select::make('Lado Dominante', 'dominant_side')
                ->options([
                    'esquerdo' => 'Esquerdo',
                    'direito' => 'Direito',
                    'ambos' => 'Ambos',
                ])
                ->nullable()
                ->sortable()
                ->help('Lado dominante do atleta.'),

            Number::make('Altura (m)', 'height')
                ->step(0.01)
                ->min(0.50)
                ->max(2.50)
                ->nullable()
                ->sortable()
                ->help('Altura do atleta em metros.'),

            Number::make('Peso (kg)', 'weight')
                ->step(0.1)
                ->min(30)
                ->max(200)
                ->nullable()
                ->sortable()
                ->help('Peso do atleta em quilogramas.'),

            Textarea::make('Biografia', 'bio')
                ->nullable()
                ->hideFromIndex()
                ->help('Biografia ou descrição do atleta.'),
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
