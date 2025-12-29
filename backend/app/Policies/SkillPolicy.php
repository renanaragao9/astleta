<?php

namespace App\Policies;

use App\Models\Skill;
use App\Models\User;
use App\Services\CheckPermissionsService;

class SkillPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-skill');
    }

    public function view(User $user, Skill $skill): bool
    {
        return CheckPermissionsService::run($user, 'show-skill');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-skill');
    }

    public function update(User $user, Skill $skill): bool
    {
        return CheckPermissionsService::run($user, 'edit-skill');
    }

    public function delete(User $user, Skill $skill): bool
    {
        return CheckPermissionsService::run($user, 'delete-skill');
    }

    public function restore(User $user, Skill $skill): bool
    {
        return CheckPermissionsService::run($user, 'edit-skill');
    }

    public function forceDelete(User $user, Skill $skill): bool
    {
        return CheckPermissionsService::run($user, 'delete-skill');
    }
}
