<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentTeam extends BaseModel
{
    protected $fillable = [
        'tournament_id',
        'team_id',
        'points',
        'position',
        'wins',
        'draws',
        'losses',
    ];

    protected $casts = [
        'points' => 'integer',
        'position' => 'integer',
        'wins' => 'integer',
        'draws' => 'integer',
        'losses' => 'integer',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}