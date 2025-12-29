<?php

namespace App\Nova;

use App\Models\Booking as BookingModel;
use App\Nova\Filters\Booking\BookingDateFromFilter;
use App\Nova\Filters\Booking\BookingDateToFilter;
use App\Nova\Filters\Booking\BookingExtraHourFilter;
use App\Nova\Filters\Booking\BookingFieldFilter;
use App\Nova\Filters\Booking\BookingPaymentTypeFilter;
use App\Nova\Filters\Booking\BookingStatusFilter;
use App\Nova\Filters\Booking\BookingUserFilter;
use App\Nova\Filters\Booking\CreatedBookingAtFromFilter;
use App\Nova\Filters\Booking\CreatedBookingAtToFilter;
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

class Booking extends Resource
{
    public static $model = BookingModel::class;

    public static $title = 'booking_number';

    public static $search = [
        'id',
        'booking_number',
    ];

    private const PAYMENT_TYPES = [
        'online' => 'Online',
        'presencial' => 'Presencial',
    ];

    private const BOOKING_STATUSES = [
        'pendente' => 'Pendente',
        'confirmado' => 'Confirmado',
        'cancelado' => 'Cancelado',
        'concluido' => 'Concluído',
    ];

    public static function label(): string
    {
        return 'Reservas';
    }

    public static function singularLabel(): string
    {
        return 'Reserva';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Select::make('Status da Reserva', 'booking_status')
                ->options(self::BOOKING_STATUSES)
                ->rules('required')
                ->help('Status atual da reserva'),

            Text::make('Reserva Nº', 'booking_number')
                ->rules('nullable')
                ->help('Número único da reserva'),

            Date::make('Data', 'booking_date')
                ->rules('required', 'date')
                ->default(now()->toDateString())
                ->help('Data da reserva'),

            Text::make('Horário de Início', 'start_time')
                ->rules('required', 'date_format:H:i')
                ->help('Informe o horário de entrada (formato HH:mm)')
                ->sortable(),

            Text::make('Horário Fim', 'end_time')
                ->rules('required', 'date_format:H:i')
                ->help('Informe o horário de saída (formato HH:mm)')
                ->sortable(),

            Number::make('Duração (minutos)', 'duration_minutes')
                ->min(1)
                ->rules('required', 'integer', 'min:1')
                ->help('Duração da reserva em minutos'),

            Boolean::make('Incluir Hora Extra', 'is_extra_hour')
                ->default(false)
                ->help('Marque se deseja incluir 30 minutos extras à reserva.')
                ->hideFromIndex(),

            BelongsTo::make('Arena', 'field', Field::class)
                ->rules('required')
                ->help('Campo reservado'),

            Number::make('Preço Base', 'base_price')
                ->min(0)
                ->step(0.01)
                ->rules('required', 'numeric', 'min:0')
                ->displayUsing(fn ($value) => 'R$ '.number_format($value, 2, ',', '.'))
                ->help('Preço base da reserva')
                ->hideFromIndex(),

            Number::make('Valor do Desconto', 'discount_amount')
                ->min(0)
                ->step(0.01)
                ->rules('required', 'numeric', 'min:0')
                ->displayUsing(fn ($value) => 'R$ '.number_format($value, 2, ',', '.'))
                ->help('Valor do desconto aplicado')
                ->hideFromIndex(),

            Number::make('Preço Total', 'total_amount')
                ->min(0)
                ->step(0.01)
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    $basePrice = $model->base_price ?? 0;
                    $discount = $request->input('discount_amount') ?? 0;
                    $model->{$attribute} = round($basePrice - $discount, 2);
                })
                ->rules('required', 'numeric', 'min:0')
                ->displayUsing(function ($value) {
                    return 'R$ '.number_format($value, 2, ',', '.');
                })
                ->help('Valor total da reserva'),

            Select::make('Tipo de Pagamento', 'payment_type')
                ->options(self::PAYMENT_TYPES)
                ->rules('required')
                ->help('Tipo de pagamento')
                ->hideFromIndex(),

            BelongsTo::make('Forma de Pagamento', 'paymentForm', PaymentForm::class)
                ->rules('required')
                ->help('Forma de pagamento escolhida')
                ->hideFromIndex(),

            Textarea::make('Observações', 'notes')
                ->nullable()
                ->hideFromIndex()
                ->help('Observações adicionais sobre a reserva'),

            Textarea::make('Motivo do Cancelamento', 'cancellation_reason')
                ->nullable()
                ->hideFromIndex()
                ->help('Motivo do cancelamento (se aplicável)'),

            Text::make('ID Pagamento Asaas', 'asaas_payment_id')
                ->nullable()
                ->hideFromIndex()
                ->help('ID do pagamento no Asaas'),

            Text::make('ID Cliente Asaas', 'asaas_customer_id')
                ->nullable()
                ->hideFromIndex()
                ->help('ID do cliente no Asaas'),

            BelongsTo::make('Usuário', 'user', User::class)
                ->rules('required')
                ->help('Usuário que fez a reserva'),

            BelongsTo::make('Cupom', 'coupon', Coupon::class)
                ->nullable()
                ->help('Cupom de desconto utilizado')
                ->hideFromIndex(),

            HasMany::make('Participantes', 'participants', BookingParticipant::class),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new CreatedBookingAtFromFilter,
            new CreatedBookingAtToFilter,
            new BookingDateFromFilter,
            new BookingDateToFilter,
            new BookingFieldFilter,
            new BookingStatusFilter,
            new BookingPaymentTypeFilter,
            new BookingExtraHourFilter,
            new BookingUserFilter,
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Obtém o dia da semana ISO (1=Segunda, 7=Domingo) a partir da data.
     */
    private function getDayOfWeek(string $date): int
    {
        return \Carbon\Carbon::parse($date)->dayOfWeekIso;
    }

    /**
     * Obtém as opções de horários de início disponíveis para o campo e dia da semana.
     */
    private function getAvailableStartTimes(int $fieldId, int $dayOfWeek): array
    {
        $schedules = \App\Models\FieldSchedule::where('field_id', $fieldId)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $options = [];
        foreach ($schedules as $schedule) {
            $start = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
            $options[$start] = $start;
        }

        return $options;
    }

    /**
     * Obtém as opções de horários de saída disponíveis a partir do horário de início.
     */
    private function getAvailableEndTimes(int $fieldId, int $dayOfWeek, string $startTime): array
    {
        $schedules = \App\Models\FieldSchedule::where('field_id', $fieldId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '>=', $startTime)
            ->orderBy('end_time')
            ->get();

        $options = [];
        $currentEnd = null;
        foreach ($schedules as $schedule) {
            $end = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
            if ($end !== $currentEnd) {
                $options[$end] = $end;
                $currentEnd = $end;
            }
        }

        return $options;
    }
}
