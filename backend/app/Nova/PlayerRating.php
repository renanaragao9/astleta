<?php

namespace App\Nova;

use App\Models\PlayerRating as PlayerRatingModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class PlayerRating extends Resource
{
    public static $model = PlayerRatingModel::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static function label(): string
    {
        return 'Avaliações de Jogadores';
    }

    public static function singularLabel(): string
    {
        return 'Avaliação de Jogador';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Number::make('Avaliação Geral', 'rating')
                ->min(1)
                ->max(5)
                ->step(1)
                ->sortable(),

            Number::make('Avaliação Técnica', 'technical_rating')
                ->min(1)
                ->max(5)
                ->step(1)
                ->sortable(),

            Number::make('Avaliação Tática', 'tactical_rating')
                ->min(1)
                ->max(5)
                ->step(1)
                ->sortable(),

            Number::make('Avaliação Física', 'physical_rating')
                ->min(1)
                ->max(5)
                ->step(1)
                ->sortable(),

            Number::make('Avaliação Mental', 'mental_rating')
                ->min(1)
                ->max(5)
                ->step(1)
                ->sortable(),

            Number::make('Avaliação de Trabalho em Equipe', 'teamwork_rating')
                ->min(1)
                ->max(5)
                ->step(1)
                ->sortable(),

            Textarea::make('Comentário', 'comment')
                ->nullable()
                ->alwaysShow(),

            BelongsTo::make('Reserva', 'booking', Booking::class)
                ->searchable()
                ->sortable(),

            BelongsTo::make('Avaliador', 'rater', User::class)
                ->searchable()
                ->sortable(),

            BelongsTo::make('Avaliado', 'rated', User::class)
                ->searchable()
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
