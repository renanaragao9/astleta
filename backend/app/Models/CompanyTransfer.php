<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyTransfer extends BaseModel
{
    protected $fillable = [
        'booking_id',
        'company_id',
        'fee_amount',
        'is_free',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
