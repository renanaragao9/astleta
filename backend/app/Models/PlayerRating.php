<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerRating extends BaseModel
{
    protected $fillable = [
        'rating',
        'technical_rating',
        'tactical_rating',
        'physical_rating',
        'mental_rating',
        'teamwork_rating',
        'team_rating',
        'comment',
        'user_id',
        'booking_participant_id',
        'booking_id',
    ];

    protected $casts = [
        'rating' => 'integer',
        'technical_rating' => 'integer',
        'tactical_rating' => 'integer',
        'physical_rating' => 'integer',
        'mental_rating' => 'integer',
        'teamwork_rating' => 'integer',
        'team_rating' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookingParticipant(): BelongsTo
    {
        return $this->belongsTo(BookingParticipant::class);
    }
}
