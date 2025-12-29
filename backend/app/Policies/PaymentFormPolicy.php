<?php

namespace App\Policies;

use App\Models\PaymentForm;
use App\Models\User;
use App\Services\CheckPermissionsService;

class PaymentFormPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-payment-form');
    }

    public function view(User $user, PaymentForm $paymentForm): bool
    {
        return CheckPermissionsService::run($user, 'show-payment-form');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-payment-form');
    }

    public function update(User $user, PaymentForm $paymentForm): bool
    {
        return CheckPermissionsService::run($user, 'edit-payment-form');
    }

    public function delete(User $user, PaymentForm $paymentForm): bool
    {
        return CheckPermissionsService::run($user, 'delete-payment-form');
    }

    public function restore(User $user, PaymentForm $paymentForm): bool
    {
        return CheckPermissionsService::run($user, 'edit-payment-form');
    }

    public function forceDelete(User $user, PaymentForm $paymentForm): bool
    {
        return CheckPermissionsService::run($user, 'delete-payment-form');
    }
}
