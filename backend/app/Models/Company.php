<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Company extends BaseModel
{
    protected $fillable = [
        'uuid',
        'name',
        'status',
        'type',
        'cnpj',
        'cpf',
        'phone',
        'description',
        'image_path',
        'asaas_customer_id',
        'asaas_sub_account_id',
        'is_open',
        'is_free',
        'user_id',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'is_free' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingsThroughFields(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Field::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function tabs(): HasMany
    {
        return $this->hasMany(Tab::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }

    public function setCnpjAttribute($value): void
    {
        $this->attributes['cnpj'] = preg_replace('/\D/', '', $value);
    }

    public function setCpfAttribute($value): void
    {
        $this->attributes['cpf'] = preg_replace('/\D/', '', $value);
    }

    public function setImageAttribute($value): void
    {
        if ($this->image && $this->image !== $value) {
            Storage::disk('s3')->delete($this->image);
        }
        $this->attributes['image'] = $value;
    }
}
