<?php

namespace Database\Seeders;

use App\Models\TeamType;
use Illuminate\Database\Seeder;

class TeamTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $teamTypes = [
            ['name' => 'Masculino'],
            ['name' => 'Feminino'],
            ['name' => 'Misto'],
            ['name' => 'Infantil'],
            ['name' => 'Juvenil'],
        ];

        foreach ($teamTypes as $teamType) {
            TeamType::create($teamType);
        }
    }
}
