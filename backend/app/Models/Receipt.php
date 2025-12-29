<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends BaseModel
{
    protected $fillable = [
        'number',
        'status',
        'file_path',
        'description',
        'issued_at',
        'paymente_date',
        'amount',
        'asaas_payment_id',
        'asaas_customer_id',
        'company_id',
        'user_id',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'paymente_date' => 'date',
        'amount' => 'decimal:2',
        'asaas_payment_id' => 'integer',
        'asaas_customer_id' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
