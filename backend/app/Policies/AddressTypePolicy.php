<?php

namespace App\Policies;

use App\Models\AddressType;
use App\Models\User;
use App\Services\CheckPermissionsService;

class AddressTypePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-address-type');
    }

    public function view(User $user, AddressType $addressType): bool
    {
        return CheckPermissionsService::run($user, 'show-address-type');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-address-type');
    }

    public function update(User $user, AddressType $addressType): bool
    {
        return CheckPermissionsService::run($user, 'edit-address-type');
    }

    public function delete(User $user, AddressType $addressType): bool
    {
        return CheckPermissionsService::run($user, 'delete-address-type');
    }

    public function restore(User $user, AddressType $addressType): bool
    {
        return CheckPermissionsService::run($user, 'edit-address-type');
    }

    public function forceDelete(User $user, AddressType $addressType): bool
    {
        return CheckPermissionsService::run($user, 'delete-address-type');
    }
}
