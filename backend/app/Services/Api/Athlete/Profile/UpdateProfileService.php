<?php

namespace App\Services\Api\Athlete\Profile;

use App\Models\AthleteProfile;
use App\Models\User;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Support\Facades\DB;

class UpdateProfileService extends BaseService
{
    public function run(array $athleteProfileData): User
    {
        $user = $this->getUser();

        $athleteProfileData['user_id'] = $user->id;
        $skillIds = $athleteProfileData['skill_ids'] ?? [];

        unset($athleteProfileData['skill_ids']);

        return DB::transaction(function () use ($athleteProfileData, $skillIds, $user) {
            if ($user->athleteProfile) {
                $user->athleteProfile->update($athleteProfileData);
            } else {
                AthleteProfile::create($athleteProfileData);
            }

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
