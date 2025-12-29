<?php

namespace App\Policies;

use App\Models\ContactType;
use App\Models\User;
use App\Services\CheckPermissionsService;

class ContactTypePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-contact-type');
    }

    public function view(User $user, ContactType $contactType): bool
    {
        return CheckPermissionsService::run($user, 'show-contact-type');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-contact-type');
    }

    public function update(User $user, ContactType $contactType): bool
    {
        return CheckPermissionsService::run($user, 'edit-contact-type');
    }

    public function delete(User $user, ContactType $contactType): bool
    {
        return CheckPermissionsService::run($user, 'delete-contact-type');
    }

    public function restore(User $user, ContactType $contactType): bool
    {
        return CheckPermissionsService::run($user, 'edit-contact-type');
    }

    public function forceDelete(User $user, ContactType $contactType): bool
    {
        return CheckPermissionsService::run($user, 'delete-contact-type');
    }
}
