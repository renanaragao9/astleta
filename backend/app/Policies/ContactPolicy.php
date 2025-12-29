<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use App\Services\CheckPermissionsService;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-contact');
    }

    public function view(User $user, Contact $contact): bool
    {
        return CheckPermissionsService::run($user, 'show-contact');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-contact');
    }

    public function update(User $user, Contact $contact): bool
    {
        return CheckPermissionsService::run($user, 'edit-contact');
    }

    public function delete(User $user, Contact $contact): bool
    {
        return CheckPermissionsService::run($user, 'delete-contact');
    }

    public function restore(User $user, Contact $contact): bool
    {
        return CheckPermissionsService::run($user, 'edit-contact');
    }

    public function forceDelete(User $user, Contact $contact): bool
    {
        return CheckPermissionsService::run($user, 'delete-contact');
    }
}
