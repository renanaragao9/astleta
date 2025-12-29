<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'data',
        'read_at',
        'user_id',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
}
