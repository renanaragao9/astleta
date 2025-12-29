<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function athleteProfiles(): HasMany
    {
        return $this->hasMany(AthleteProfile::class);
    }
}
