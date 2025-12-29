<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marketing extends BaseModel
{
    protected $fillable = [
        'title',
        'image_path',
        'link',
        'content',
        'start_date',
        'end_date',
        'age',
        'marketing_type_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'age' => 'integer',
    ];

    public function marketingType(): BelongsTo
    {
        return $this->belongsTo(MarketingType::class);
    }
}
