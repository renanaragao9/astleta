<?php

namespace App\Policies;

use App\Models\PlayerRating;
use App\Models\User;
use App\Services\CheckPermissionsService;

class PlayerRatingPolicy
{
    public function viewAny(User $user): bool
    {
        return CheckPermissionsService::run($user, 'index-player-rating');
    }

    public function view(User $user, PlayerRating $playerRating): bool
    {
        return CheckPermissionsService::run($user, 'show-player-rating');
    }

    public function create(User $user): bool
    {
        return CheckPermissionsService::run($user, 'create-player-rating');
    }

    public function update(User $user, PlayerRating $playerRating): bool
    {
        return CheckPermissionsService::run($user, 'edit-player-rating');
    }

    public function delete(User $user, PlayerRating $playerRating): bool
    {
        return CheckPermissionsService::run($user, 'delete-player-rating');
    }

    public function restore(User $user, PlayerRating $playerRating): bool
    {
        return CheckPermissionsService::run($user, 'restore-player-rating');
    }

    public function forceDelete(User $user, PlayerRating $playerRating): bool
    {
        return CheckPermissionsService::run($user, 'force-delete-player-rating');
    }
}
