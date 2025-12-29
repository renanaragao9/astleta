<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingType extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function marketings(): HasMany
    {
        return $this->hasMany(Marketing::class);
    }
}
