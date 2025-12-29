<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamType extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
