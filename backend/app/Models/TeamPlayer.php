<?php

namespace App\Models;

class TeamPlayer extends BaseModel
{
    protected $fillable = [
        'number',
        'role',
        'status',
        'joined_at',
        'left_at',
        'team_id',
        'user_id',
    ];

    protected $casts = [
        'role' => 'string',
        'status' => 'string',
        'joined_at' => 'date',
        'left_at' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
