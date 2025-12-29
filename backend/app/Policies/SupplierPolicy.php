<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use App\Services\CheckPermissionsService;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-supplier');
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return CheckPermissionsService::run($user, 'show-supplier');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-supplier');
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return CheckPermissionsService::run($user, 'edit-supplier');
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return CheckPermissionsService::run($user, 'delete-supplier');
    }

    public function restore(User $user, Supplier $supplier): bool
    {
        return CheckPermissionsService::run($user, 'restore-supplier');
    }

    public function forceDelete(User $user, Supplier $supplier): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-supplier');
    }
}