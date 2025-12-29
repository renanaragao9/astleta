<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldSize extends BaseModel
{
    protected $fillable = [
        'name',
        'width',
        'height',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }
}
