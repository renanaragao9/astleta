<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends BaseModel
{
    protected $fillable = [
        'type',
        'status',
        'stock_id',
        'movimentable_type',
        'movimentable_id',
    ];

    protected $with = ['stock'];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function movimentable(): MorphTo
    {
        return $this->morphTo();
    }
}