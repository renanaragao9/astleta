<?php

namespace App\Policies;

use App\Models\PreCompaniesRegistration;
use App\Models\User;
use App\Services\CheckPermissionsService;

class PreCompaniesRegistrationPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-pre-companies-registration');
    }

    public function view(User $user, PreCompaniesRegistration $preCompaniesRegistration): bool
    {
        return CheckPermissionsService::run($user, 'show-pre-companies-registration');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-pre-companies-registration');
    }

    public function update(User $user, PreCompaniesRegistration $preCompaniesRegistration): bool
    {
        return CheckPermissionsService::run($user, 'edit-pre-companies-registration');
    }

    public function delete(User $user, PreCompaniesRegistration $preCompaniesRegistration): bool
    {
        return CheckPermissionsService::run($user, 'delete-pre-companies-registration');
    }

    public function restore(User $user, PreCompaniesRegistration $preCompaniesRegistration): bool
    {
        return CheckPermissionsService::run($user, 'restore-pre-companies-registration');
    }

    public function forceDelete(User $user, PreCompaniesRegistration $preCompaniesRegistration): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-pre-companies-registration');
    }
}
