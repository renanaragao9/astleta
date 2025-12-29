<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamStatisticsBooking extends BaseModel
{
    protected $fillable = [
        'count',
        'team_id',
        'team_booking_id',
        'statistic_id',
        'booking_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function teamBooking(): BelongsTo
    {
        return $this->belongsTo(TeamBooking::class);
    }

    public function statisticTeam(): BelongsTo
    {
        return $this->belongsTo(StatisticsTeam::class, 'statistic_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
