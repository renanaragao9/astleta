<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends BaseModel
{
    protected $fillable = [
        'is_available_use',
        'is_sale',
        'history',
        'status',
        'product_id',
        'warehouse_id',
        'company_id',
    ];

    protected $casts = [
        'is_available_use' => 'boolean',
        'is_sale' => 'boolean',
        'history' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}