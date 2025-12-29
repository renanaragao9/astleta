<?php

namespace App\Policies;

use App\Models\FieldSize;
use App\Models\User;
use App\Services\CheckPermissionsService;

class FieldSizePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-field-size');
    }

    public function view(User $user, FieldSize $fieldSize): bool
    {
        return CheckPermissionsService::run($user, 'show-field-size');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-field-size');
    }

    public function update(User $user, FieldSize $fieldSize): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-size');
    }

    public function delete(User $user, FieldSize $fieldSize): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-size');
    }

    public function restore(User $user, FieldSize $fieldSize): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-size');
    }

    public function forceDelete(User $user, FieldSize $fieldSize): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-size');
    }
}
