<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Document extends BaseModel
{
    protected $fillable = [
        'number',
        'document_type_id',
        'file_path',
        'status',
        'description',
        'documentable_type',
        'documentable_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $appends = ['file_url'];

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            $disk = Storage::disk('s3');
            if (method_exists($disk, 'temporaryUrl')) {
                return $disk->temporaryUrl($this->file_path, now()->addMinutes(30));
            }

            return Storage::url($this->file_path);
        }

        return null;
    }

    public function setFilePathAttribute($value)
    {
        if ($this->file_path && $this->file_path !== $value) {
            Storage::disk('s3')->delete($this->file_path);
        }
        $this->attributes['file_path'] = $value;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendente' => 'orange',
            'aprovado' => 'green',
            'rejeitado' => 'red',
            default => 'gray',
        };
    }
}
