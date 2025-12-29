<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TabItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quantity',
        'total',
        'observation',
        'tab_id',
        'product_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total' => 'decimal:2',
    ];

    public function tab(): BelongsTo
    {
        return $this->belongsTo(Tab::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
