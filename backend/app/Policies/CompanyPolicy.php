<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use App\Services\CheckPermissionsService;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-company');
    }

    public function view(User $user, Company $company): bool
    {
        return CheckPermissionsService::run($user, 'show-company');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-company');
    }

    public function update(User $user, Company $company): bool
    {
        return CheckPermissionsService::run($user, 'edit-company');
    }

    public function delete(User $user, Company $company): bool
    {
        return CheckPermissionsService::run($user, 'delete-company');
    }

    public function restore(User $user, Company $company): bool
    {
        return CheckPermissionsService::run($user, 'restore-company');
    }

    public function forceDelete(User $user, Company $company): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-company');
    }
}
