<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends BaseModel
{
    protected $fillable = [
        'name',
        'sport_id',
    ];

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function athleteProfiles(): HasMany
    {
        return $this->hasMany(AthleteProfile::class);
    }

    public function athleteSubpositionProfiles(): HasMany
    {
        return $this->hasMany(AthleteProfile::class, 'subposition_id');
    }
}
