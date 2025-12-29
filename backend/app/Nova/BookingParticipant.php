<?php

namespace App\Nova;

use App\Models\BookingParticipant as BookingParticipantModel;
use Customfields\PhoneNumber\PhoneNumber;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class BookingParticipant extends Resource
{
    public static $model = BookingParticipantModel::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'phone',
    ];

    private const STATUS_OPTIONS = [
        'pendente' => 'Pendente',
        'confirmado' => 'Confirmado',
        'cancelado' => 'Cancelado',
    ];

    public static function label(): string
    {
        return 'Participantes da Reserva';
    }

    public static function singularLabel(): string
    {
        return 'Participante da Reserva';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Nome', 'name')
                ->sortable()
                ->rules('nullable', 'string', 'max:255')
                ->help('Nome do participante'),

            PhoneNumber::make('Telefone', 'phone')
                ->sortable()
                ->rules('nullable', 'string', 'max:255')
                ->help('Telefone do participante'),

            Number::make('Valor Pago', 'amount_paid')
                ->sortable()
                ->rules('numeric', 'min:0')
                ->displayUsing(fn ($value) => 'R$ '.number_format($value, 2, ',', '.'))
                ->help('Valor pago pelo participante'),

            Boolean::make('Pago', 'is_paid')
                ->sortable()
                ->help('Indica se o participante pagou'),

            DateTime::make('Pago em', 'paid_at')
                ->sortable()
                ->rules('nullable')
                ->help('Data e hora do pagamento'),

            Select::make('Status')
                ->options(self::STATUS_OPTIONS)
                ->sortable()
                ->rules('required')
                ->help('Status do participante'),

            DateTime::make('Confirmado em', 'confirmed_at')
                ->sortable()
                ->rules('nullable')
                ->help('Data e hora da confirmação'),

            BelongsTo::make('Reserva', 'booking', Booking::class)
                ->sortable()
                ->help('Reserva associada'),

            BelongsTo::make('Usuário', 'user', User::class)
                ->sortable()
                ->nullable()
                ->help('Usuário associado'),

            BelongsTo::make('Adicionado por', 'addedByUser', User::class)
                ->sortable()
                ->help('Usuário que adicionou o participante'),
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
