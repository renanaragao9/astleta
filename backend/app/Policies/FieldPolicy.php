<?php

namespace App\Policies;

use App\Models\Field;
use App\Models\User;
use App\Services\CheckPermissionsService;

class FieldPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-field');
    }

    public function view(User $user, Field $field): bool
    {
        return CheckPermissionsService::run($user, 'show-field');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-field');
    }

    public function update(User $user, Field $field): bool
    {
        return CheckPermissionsService::run($user, 'edit-field');
    }

    public function delete(User $user, Field $field): bool
    {
        return CheckPermissionsService::run($user, 'delete-field');
    }

    public function restore(User $user, Field $field): bool
    {
        return CheckPermissionsService::run($user, 'restore-field');
    }

    public function forceDelete(User $user, Field $field): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-field');
    }
}
