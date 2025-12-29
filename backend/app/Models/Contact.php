<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contact extends BaseModel
{
    protected $fillable = [
        'value',
        'contact_type_id',
        'contactable_type',
        'contactable_id',
    ];

    public function contactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class);
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
