<?php

namespace App\Policies;

use App\Models\FieldType;
use App\Models\User;
use App\Services\CheckPermissionsService;

class FieldTypePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-field-type');
    }

    public function view(User $user, FieldType $fieldType): bool
    {
        return CheckPermissionsService::run($user, 'show-field-type');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-field-type');
    }

    public function update(User $user, FieldType $fieldType): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-type');
    }

    public function delete(User $user, FieldType $fieldType): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-type');
    }

    public function restore(User $user, FieldType $fieldType): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-type');
    }

    public function forceDelete(User $user, FieldType $fieldType): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-type');
    }
}
