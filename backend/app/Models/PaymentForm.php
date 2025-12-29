<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentForm extends BaseModel
{
    protected $fillable = [
        'name',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
