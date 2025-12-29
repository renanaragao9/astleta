<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatisticsTeam extends BaseModel
{
    protected $fillable = [
        'name',
        'icon',
        'color',
        'sport_id',
    ];

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function teamStatisticsBookings(): HasMany
    {
        return $this->hasMany(TeamStatisticsBooking::class, 'statistic_id');
    }
}
