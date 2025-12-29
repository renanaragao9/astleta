<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends BaseModel
{
    protected $fillable = [
        'public_id',
        'booking_number',
        'booking_date',
        'booking_end_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'base_price',
        'discount_amount',
        'total_amount',
        'notes',
        'cancellation_reason',
        'payment_type',
        'booking_status',
        'asaas_payment_id',
        'asaas_customer_id',
        'user_id',
        'field_id',
        'coupon_id',
        'payment_form_id',
        'is_extra_hour',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_end_date' => 'date',
        'duration_minutes' => 'integer',
        'base_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function paymentForm(): BelongsTo
    {
        return $this->belongsTo(PaymentForm::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function teamBooking(): HasOne
    {
        return $this->hasOne(TeamBooking::class);
    }
}
