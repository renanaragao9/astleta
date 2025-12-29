<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends BaseModel
{
    public function profile(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'profiles_permissions', 'permission_id', 'profile_id');
    }
}
