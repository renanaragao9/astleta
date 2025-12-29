<?php

namespace App\Models;

class Skill extends BaseModel
{
    protected $fillable = [
        'name',
        'sport_id',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
