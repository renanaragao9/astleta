<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Field extends BaseModel
{
    protected $fillable = [
        'name',
        'price_per_hour',
        'extra_hour_price',
        'description',
        'image_path',
        'is_allows_extra_hour',
        'is_active',
        'company_id',
        'field_type_id',
        'field_surface_id',
        'field_size_id',
    ];

    protected $casts = [
        'price_per_hour' => 'decimal:2',
        'extra_hour_price' => 'decimal:2',
        'is_allows_extra_hour' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function fieldType(): BelongsTo
    {
        return $this->belongsTo(FieldType::class);
    }

    public function fieldSurface(): BelongsTo
    {
        return $this->belongsTo(FieldSurface::class);
    }

    public function fieldSize(): BelongsTo
    {
        return $this->belongsTo(FieldSize::class);
    }

    public function fieldSchedules(): HasMany
    {
        return $this->hasMany(FieldSchedule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function fieldItems(): BelongsToMany
    {
        return $this->belongsToMany(FieldItem::class);
    }

    public function fieldImages(): HasMany
    {
        return $this->hasMany(FieldImage::class);
    }

    public function setImagePathAttribute($value): void
    {
        if ($this->image_path && $this->image_path !== $value) {
            Storage::disk('s3')->delete($this->image_path);
        }
        $this->attributes['image_path'] = $value;
    }
}
