<?php

namespace App\Policies;

use App\Models\FieldSurface;
use App\Models\User;
use App\Services\CheckPermissionsService;

class FieldSurfacePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-field-surface');
    }

    public function view(User $user, FieldSurface $fieldSurface): bool
    {
        return CheckPermissionsService::run($user, 'show-field-surface');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-field-surface');
    }

    public function update(User $user, FieldSurface $fieldSurface): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-surface');
    }

    public function delete(User $user, FieldSurface $fieldSurface): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-surface');
    }

    public function restore(User $user, FieldSurface $fieldSurface): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-surface');
    }

    public function forceDelete(User $user, FieldSurface $fieldSurface): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-surface');
    }
}
