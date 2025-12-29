<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use App\Services\CheckPermissionsService;

class AddressPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-address');
    }

    public function view(User $user, Address $address): bool
    {
        return CheckPermissionsService::run($user, 'show-address');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-address');
    }

    public function update(User $user, Address $address): bool
    {
        return CheckPermissionsService::run($user, 'edit-address');
    }

    public function delete(User $user, Address $address): bool
    {
        return CheckPermissionsService::run($user, 'delete-address');
    }

    public function restore(User $user, Address $address): bool
    {
        return CheckPermissionsService::run($user, 'edit-address');
    }

    public function forceDelete(User $user, Address $address): bool
    {
        return CheckPermissionsService::run($user, 'delete-address');
    }
}
