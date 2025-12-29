<?php

namespace App\Services\Api\Athlete\Profile;

use App\Models\User;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;

class UpdateUserService extends BaseService
{
    public function run(array $userData): User
    {
        $user = $this->getUser();

        return DB::transaction(function () use ($userData, $user) {
            $user->update($userData);

            return User::with([
                'athleteProfile.sport:id,name',
                'athleteProfile.position:id,name',
                'athleteProfile.subposition:id,name',
                'athleteProfile.feature:id,name',
                'athleteProfile.subfeature:id,name',
                'profile:id,name',
                'skills:id,name',
            ])->find($user->id);
        });
    }
}
