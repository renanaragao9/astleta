<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $skills = [
            'Passe',
            'Chute',
            'Defesa',
            'Drible',
            'Cabeceio',
            'Velocidade',
            'Força',
            'Resistência',
            'Visão de Jogo',
            'Liderança',
            'Marcação',
            'Cruzamento',
            'Técnica',
            'Posicionamento',
            'Concentração',
            'Agilidade',
            'Reflexo',
            'Comunicação',
            'Anticipação',
            'Controle de Bola',
        ];

        DB::table('skills')->insert(
            collect($skills)->map(fn ($skill) => [
                'name' => $skill,
                'sport_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
