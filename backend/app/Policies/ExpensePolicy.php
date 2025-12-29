<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use App\Services\CheckPermissionsService;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-expense');
    }

    public function view(User $user, Expense $expense): bool
    {
        return CheckPermissionsService::run($user, 'show-expense');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-expense');
    }

    public function update(User $user, Expense $expense): bool
    {
        return CheckPermissionsService::run($user, 'edit-expense');
    }

    public function delete(User $user, Expense $expense): bool
    {
        return CheckPermissionsService::run($user, 'delete-expense');
    }

    public function restore(User $user, Expense $expense): bool
    {
        return CheckPermissionsService::run($user, 'restore-expense');
    }

    public function forceDelete(User $user, Expense $expense): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-expense');
    }
}
