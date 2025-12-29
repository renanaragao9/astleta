<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamBooking extends BaseModel
{
    protected $fillable = [
        'result',
        'goals_home',
        'goals_away',
        'is_friendly',
        'home_team_id',
        'away_team_id',
        'booking_id',
        'sport_id',
        'winner_id',
    ];

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_id');
    }

    public function teamStatisticsBookings(): HasMany
    {
        return $this->hasMany(TeamStatisticsBooking::class);
    }
}
