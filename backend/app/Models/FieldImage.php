<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldImage extends BaseModel
{
    protected $fillable = [
        'field_id',
        'image_path',
        'caption',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
