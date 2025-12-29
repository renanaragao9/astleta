<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends BaseModel
{
    protected $fillable = [
        'zipcode',
        'country',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
        'latitude',
        'longitude',
        'address_type_id',
        'addressable_type',
        'addressable_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function addressType(): BelongsTo
    {
        return $this->belongsTo(AddressType::class);
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullAddressAttribute(): string
    {
        $address = $this->street;

        if ($this->number) {
            $address .= ', '.$this->number;
        }

        if ($this->complement) {
            $address .= ' - '.$this->complement;
        }

        if ($this->district) {
            $address .= ' - '.$this->district;
        }

        $address .= ' - '.$this->city.'/'.$this->state;

        if ($this->zipcode) {
            $address .= ' - CEP: '.$this->zipcode;
        }

        return $address;
    }
}
