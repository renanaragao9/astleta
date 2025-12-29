<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatisticsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $sport = DB::table('sports')->where('name', 'Futebol')->first();
        if (! $sport) {
            return;
        }

        $goalkeeperPosition = DB::table('positions')
            ->where('name', 'Goleiro')
            ->where('sport_id', $sport->id)
            ->first();

        $statistics = [
            [
                'name' => 'Gols',
                'icon' => 'fas fa-futbol',
                'color' => 'green-500',
                'sport_id' => $sport->id,
                'position_id' => null,
            ],
            [
                'name' => 'Assistências',
                'icon' => 'fas fa-handshake',
                'color' => 'blue-500',
                'sport_id' => $sport->id,
                'position_id' => null,
            ],
            [
                'name' => 'Passe Finalização',
                'icon' => 'fas fa-bullseye',
                'color' => 'red-500',
                'sport_id' => $sport->id,
                'position_id' => null,
            ],
            [
                'name' => 'Cartão Amarelo',
                'icon' => 'fas fa-square',
                'color' => 'yellow-500',
                'sport_id' => $sport->id,
                'position_id' => null,
            ],
            [
                'name' => 'Cartão Vermelho',
                'icon' => 'fas fa-square',
                'color' => 'red-500',
                'sport_id' => $sport->id,
                'position_id' => null,
            ],
        ];

        if ($goalkeeperPosition) {
            $statistics[] = [
                'name' => 'Defesas Difíceis',
                'icon' => 'fas fa-shield-alt',
                'color' => 'purple-500',
                'sport_id' => $sport->id,
                'position_id' => $goalkeeperPosition->id,
            ];
            $statistics[] = [
                'name' => 'Defesa de Pênaltis',
                'icon' => 'fas fa-shield-alt',
                'color' => 'orange-500',
                'sport_id' => $sport->id,
                'position_id' => $goalkeeperPosition->id,
            ];
        }

        DB::table('statistics')->insert(
            collect($statistics)->map(fn ($stat) => array_merge($stat, [
                'created_at' => $now,
                'updated_at' => $now,
            ]))->toArray()
        );
    }
}
