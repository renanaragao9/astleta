<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('teams')->insert([
            'uuid' => Str::uuid(),
            'name' => 'Flamengo FC',
            'nickname' => 'Mengão',
            'stadium_name' => 'Maracanã',
            'primary_color' => '#FF0000',
            'secondary_color' => '#000000',
            'website' => 'https://www.flamengo.com.br',
            'founded_date' => '1895-11-17',
            'description' => 'Equipe profissional de futebol adulto',
            'max_members' => 30,
            'is_public' => true,
            'user_id' => 1,
            'sport_id' => 1,
            'team_type_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
