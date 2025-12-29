<?php

namespace App\Policies;

use App\Models\Receipt;
use App\Models\User;
use App\Services\CheckPermissionsService;

class ReceiptPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-receipt');
    }

    public function view(User $user, Receipt $receipt): bool
    {
        return CheckPermissionsService::run($user, 'show-receipt');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-receipt');
    }

    public function update(User $user, Receipt $receipt): bool
    {
        return CheckPermissionsService::run($user, 'edit-receipt');
    }

    public function delete(User $user, Receipt $receipt): bool
    {
        return CheckPermissionsService::run($user, 'delete-receipt');
    }

    public function restore(User $user, Receipt $receipt): bool
    {
        return CheckPermissionsService::run($user, 'restore-receipt');
    }

    public function forceDelete(User $user, Receipt $receipt): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-receipt');
    }
}
