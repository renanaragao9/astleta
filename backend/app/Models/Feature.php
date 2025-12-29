<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends BaseModel
{
    protected $fillable = [
        'name',
        'position_id',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function athleteProfiles(): HasMany
    {
        return $this->hasMany(AthleteProfile::class);
    }

    public function athleteSubfeatureProfiles(): HasMany
    {
        return $this->hasMany(AthleteProfile::class, 'subfeature_id');
    }
}
