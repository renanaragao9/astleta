<?php

namespace App\Policies;

use App\Models\FieldItem;
use App\Models\User;
use App\Services\CheckPermissionsService;

class FieldItemPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-field-item');
    }

    public function view(User $user, FieldItem $fieldItem): bool
    {
        return CheckPermissionsService::run($user, 'show-field-item');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-field-item');
    }

    public function update(User $user, FieldItem $fieldItem): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-item');
    }

    public function delete(User $user, FieldItem $fieldItem): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-item');
    }

    public function restore(User $user, FieldItem $fieldItem): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-item');
    }

    public function forceDelete(User $user, FieldItem $fieldItem): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-item');
    }
}
