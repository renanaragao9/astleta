<?php

namespace App\Policies;

use App\Models\DocumentType;
use App\Models\User;
use App\Services\CheckPermissionsService;

class DocumentTypePolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-document-type');
    }

    public function view(User $user, DocumentType $documentType): bool
    {
        return CheckPermissionsService::run($user, 'show-document-type');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-document-type');
    }

    public function update(User $user, DocumentType $documentType): bool
    {
        return CheckPermissionsService::run($user, 'edit-document-type');
    }

    public function delete(User $user, DocumentType $documentType): bool
    {
        return CheckPermissionsService::run($user, 'delete-document-type');
    }

    public function restore(User $user, DocumentType $documentType): bool
    {
        return CheckPermissionsService::run($user, 'edit-document-type');
    }

    public function forceDelete(User $user, DocumentType $documentType): bool
    {
        return CheckPermissionsService::run($user, 'delete-document-type');
    }
}
