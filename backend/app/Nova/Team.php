<?php

namespace App\Nova;

use App\Helpers\GenerateTemporaryUrlSHelper;
use App\Models\Team as TeamModel;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Team extends Resource
{
    public static $model = TeamModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'description',
    ];

    public static function label(): string
    {
        return 'Equipes';
    }

    public static function singularLabel(): string
    {
        return 'Equipe';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Hidden::make('UUID', 'uuid')
                ->default(fn () => Str::uuid())
                ->hideFromIndex(),

            Text::make('UUID', 'uuid')
                ->onlyOnDetail()
                ->copyable()
                ->help('Identificador único da equipe.'),

            Text::make('Imagem', 'shield_path', function ($value) {
                if (! $value) {
                    return null;
                }
                $fileLink = (new GenerateTemporaryUrlSHelper)->run($value);

                return "<a href=\"$fileLink\" target='_blank' download><img src=\"$fileLink\" style=\"max-width: 400px;\"></a>";
            })->asHtml()
                ->withMeta([
                    'extraAttributes' => [
                        'readonly' => true,
                    ],
                ])
                ->onlyOnDetail(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Nome oficial da equipe.'),

            Text::make('Apelido', 'nickname')
                ->nullable()
                ->hideFromIndex()
                ->help('Apelido ou nome alternativo da equipe.'),

            Text::make('Nome do Estádio', 'stadium_name')
                ->nullable()
                ->hideFromIndex()
                ->help('Nome do estádio onde a equipe joga.'),

            Text::make('Cor Primária', 'primary_color')
                ->withMeta(['type' => 'color'])
                ->nullable()
                ->rules('nullable', 'regex:/^#[a-fA-F0-9]{6}$/')
                ->hideFromIndex()
                ->help('Cor primária da equipe em formato hexadecimal (ex: #FF0000).'),

            Text::make('Cor Secundária', 'secondary_color')
                ->withMeta(['type' => 'color'])
                ->nullable()
                ->rules('nullable', 'regex:/^#[a-fA-F0-9]{6}$/')
                ->hideFromIndex()
                ->help('Cor secundária da equipe em formato hexadecimal (ex: #00FF00).'),

            Text::make('Website', 'website')
                ->nullable()
                ->rules('nullable', 'url')
                ->hideFromIndex()
                ->help('URL do website oficial da equipe.'),

            Date::make('Data de Fundação', 'founded_date')
                ->nullable()
                ->help('Data de fundação da equipe.')
                ->sortable(),

            Textarea::make('Descrição', 'description')
                ->nullable()
                ->hideFromIndex()
                ->help('Descrição detalhada da equipe.'),

            Number::make('Máximo de Membros', 'max_members')
                ->default(30)
                ->rules('required', 'integer', 'min:1')
                ->help('Número máximo de membros permitidos na equipe.'),

            BelongsTo::make('Esporte', 'sport', Sport::class)
                ->rules('required')
                ->help('Esporte praticado pela equipe.')
                ->sortable(),

            BelongsTo::make('Tipo de Equipe', 'teamType', TeamType::class)
                ->rules('required')
                ->help('Tipo ou categoria da equipe.')
                ->sortable(),

            BelongsTo::make('Criador', 'creator', User::class)
                ->rules('required')
                ->help('Usuário que criou a equipe.'),

            Boolean::make('Público', 'is_public')
                ->default(true)
                ->help('Define se a equipe é pública ou privada.')
                ->sortable(),

            HasMany::make('Jogadores da Equipe', 'teamPlayers', TeamPlayer::class),
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
