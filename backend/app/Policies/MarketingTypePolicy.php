<?php

namespace App\Policies;

use App\Models\MarketingType;
use App\Models\User;
use App\Services\CheckPermissionsService;

class MarketingTypePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-marketing-type');
    }

    public function view(User $user, MarketingType $marketingType): bool
    {
        return CheckPermissionsService::run($user, 'show-marketing-type');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-marketing-type');
    }

    public function update(User $user, MarketingType $marketingType): bool
    {
        return CheckPermissionsService::run($user, 'edit-marketing-type');
    }

    public function delete(User $user, MarketingType $marketingType): bool
    {
        return CheckPermissionsService::run($user, 'delete-marketing-type');
    }

    public function restore(User $user, MarketingType $marketingType): bool
    {
        return CheckPermissionsService::run($user, 'restore-marketing-type');
    }

    public function forceDelete(User $user, MarketingType $marketingType): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-marketing-type');
    }
}
