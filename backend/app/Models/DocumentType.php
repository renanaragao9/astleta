<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
