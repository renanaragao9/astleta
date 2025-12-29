<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactType extends BaseModel
{
    protected $fillable = [
        'name',
        'icon',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
