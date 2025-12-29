<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStatistic extends BaseModel
{
    protected $fillable = [
        'count',
        'user_id',
        'booking_participant_id',
        'statistic_id',
        'booking_id',
    ];

    protected $casts = [
        'count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statistic(): BelongsTo
    {
        return $this->belongsTo(Statistics::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingParticipant(): BelongsTo
    {
        return $this->belongsTo(BookingParticipant::class);
    }
}
