<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseType extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
