<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use App\Services\CheckPermissionsService;

class TagPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-tag');
    }

    public function view(User $user, Tag $tag): bool
    {
        return CheckPermissionsService::run($user, 'show-tag');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-tag');
    }

    public function update(User $user, Tag $tag): bool
    {
        return CheckPermissionsService::run($user, 'edit-tag');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return CheckPermissionsService::run($user, 'delete-tag');
    }

    public function restore(User $user, Tag $tag): bool
    {
        return CheckPermissionsService::run($user, 'edit-tag');
    }

    public function forceDelete(User $user, Tag $tag): bool
    {
        return CheckPermissionsService::run($user, 'delete-tag');
    }
}
