<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldType extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }
}
