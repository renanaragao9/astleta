<?php

namespace App\Policies;

use App\Models\FieldSchedule;
use App\Models\User;
use App\Services\CheckPermissionsService;

class FieldSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-field-schedule');
    }

    public function view(User $user, FieldSchedule $fieldSchedule): bool
    {
        return CheckPermissionsService::run($user, 'show-field-schedule');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-field-schedule');
    }

    public function update(User $user, FieldSchedule $fieldSchedule): bool
    {
        return CheckPermissionsService::run($user, 'edit-field-schedule');
    }

    public function delete(User $user, FieldSchedule $fieldSchedule): bool
    {
        return CheckPermissionsService::run($user, 'delete-field-schedule');
    }

    public function restore(User $user, FieldSchedule $fieldSchedule): bool
    {
        return CheckPermissionsService::run($user, 'restore-field-schedule');
    }

    public function forceDelete(User $user, FieldSchedule $fieldSchedule): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-field-schedule');
    }
}
