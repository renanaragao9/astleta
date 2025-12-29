<?php

namespace App\Policies;

use App\Models\FieldImage;
use App\Models\User;
use App\Services\CheckPermissionsService;

class FieldImagePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-field-image');
    }

    public function view(User $user, FieldImage $fieldImage): bool
    {
        return CheckPermissionsService::run($user, 'show-field-image');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-field-image');
    }

    public function update(User $user, FieldImage $fieldImage): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-image');
    }

    public function delete(User $user, FieldImage $fieldImage): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-image');
    }

    public function restore(User $user, FieldImage $fieldImage): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-image');
    }

    public function forceDelete(User $user, FieldImage $fieldImage): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-image');
    }
}
