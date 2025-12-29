<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends BaseModel
{
    public function permission(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'profiles_permissions', 'profile_id', 'permission_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
