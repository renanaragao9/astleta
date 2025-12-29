<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends BaseModel
{
    protected $fillable = [
        'name',
        'status',
        'description',
        'awards',
        'welcome_email',
        'start_date',
        'end_date',
        'max_participants',
        'is_public',
        'company_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_participants' => 'integer',
        'is_public' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tournamentTeams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class);
    }
}