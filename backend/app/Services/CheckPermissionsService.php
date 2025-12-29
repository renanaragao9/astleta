<?php

namespace App\Services;

use App\Models\User;

class CheckPermissionsService
{
    public static function run(User $user, string $permissionNeeded): bool
    {
        $permissions = $user->profile->permission->pluck('name')->toArray();
        if (in_array($permissionNeeded, $permissions, true)) {
            return true;
        }

        return false;
    }
}
