<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends BaseModel
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_amount',
        'discount_percentage',
        'usage_limit',
        'start_date',
        'end_date',
        'expires_at',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'usage_limit' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
