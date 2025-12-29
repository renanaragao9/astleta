<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingParticipant extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'amount_paid',
        'paid_at',
        'status',
        'confirmed_at',
        'is_paid',
        'booking_id',
        'user_id',
        'added_by_user_id',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'status' => 'string',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}
