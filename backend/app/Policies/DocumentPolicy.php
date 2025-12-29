<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use App\Services\CheckPermissionsService;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-document');
    }

    public function view(User $user, Document $document): bool
    {
        return CheckPermissionsService::run($user, 'show-document');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-document');
    }

    public function update(User $user, Document $document): bool
    {
        return CheckPermissionsService::run($user, 'edit-document');
    }

    public function delete(User $user, Document $document): bool
    {
        return CheckPermissionsService::run($user, 'delete-document');
    }

    public function restore(User $user, Document $document): bool
    {
        return CheckPermissionsService::run($user, 'edit-document');
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return CheckPermissionsService::run($user, 'delete-document');
    }
}
