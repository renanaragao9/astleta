<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $positionsBySport = [
            'Futebol' => [
                'Goleiro',
                'Zagueiro',
                'Lateral Direito',
                'Lateral Esquerdo',
                'Volante',
                'Meia',
                'Atacante',
                'Centroavante',
                'Ponta Direita',
                'Ponta Esquerda',
            ],
            'Vôlei' => [
                'Levantador',
                'Oposto',
                'Central',
                'Ponteiro',
                'Líbero',
            ],
        ];

        foreach ($positionsBySport as $sportName => $positions) {
            $sport = DB::table('sports')->where('name', $sportName)->first();

            if ($sport) {
                DB::table('positions')->insert(
                    collect($positions)->map(fn ($position) => [
                        'sport_id' => $sport->id,
                        'name' => $position,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->toArray()
                );
            }
        }
    }
}
