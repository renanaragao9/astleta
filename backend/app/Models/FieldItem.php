<?php

namespace App\Models;

class FieldItem extends BaseModel
{
    protected $fillable = [
        'name',
        'icon',
    ];

    public function fields()
    {
        return $this->belongsToMany(Field::class);
    }
}
