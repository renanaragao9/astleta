<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AddressType extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
