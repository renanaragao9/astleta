<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AthleteProfile extends BaseModel
{
    protected $fillable = [
        'dominant_side',
        'height',
        'weight',
        'bio',
        'user_id',
        'sport_id',
        'position_id',
        'subposition_id',
        'feature_id',
        'subfeature_id',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'dominant_side' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function subposition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'subposition_id');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function subfeature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'subfeature_id');
    }
}
