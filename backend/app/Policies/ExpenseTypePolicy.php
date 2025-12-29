<?php

namespace App\Policies;

use App\Models\ExpenseType;
use App\Models\User;
use App\Services\CheckPermissionsService;

class ExpenseTypePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-expense-type');
    }

    public function view(User $user, ExpenseType $expenseType): bool
    {
        return CheckPermissionsService::run($user, 'show-expense-type');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-expense-type');
    }

    public function update(User $user, ExpenseType $expenseType): bool
    {
        return CheckPermissionsService::run($user, 'edit-expense-type');
    }

    public function delete(User $user, ExpenseType $expenseType): bool
    {
        return CheckPermissionsService::run($user, 'delete-expense-type');
    }

    public function restore(User $user, ExpenseType $expenseType): bool
    {
        return CheckPermissionsService::run($user, 'edit-expense-type');
    }

    public function forceDelete(User $user, ExpenseType $expenseType): bool
    {
        return CheckPermissionsService::run($user, 'delete-expense-type');
    }
}
