<?php

namespace App\Services\Api\Athlete\Profile;

use App\Models\AthleteProfile;
use App\Models\User;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;

class StoreProfileService extends BaseService
{
    public function run(array $data): User
    {
        $user = $this->getUser();

        $data['user_id'] = $user->id;
        $skillIds = $data['skill_ids'] ?? [];

        unset($data['skill_ids']);

        return DB::transaction(function () use ($data, $skillIds, $user) {
            AthleteProfile::create($data);

            if ($skillIds) {
                $user->skills()->sync($skillIds);
            }

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
