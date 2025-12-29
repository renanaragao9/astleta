<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            'admin',
            'athlete',
            'company',
            'referee',
            'analyst',
        ];

        foreach ($profiles as $profileName) {
            $profileId = DB::table('profiles')->insertGetId(['name' => $profileName]);

            if ($profileName === 'admin') {
                $permissions = DB::table('permissions')->pluck('id');
                foreach ($permissions as $permissionId) {
                    DB::table('profiles_permissions')->insert([
                        'profile_id' => $profileId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }
}
