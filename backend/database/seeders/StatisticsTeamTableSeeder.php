<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatisticsTeamTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $sport = DB::table('sports')->where('name', 'Futebol')->first();
        if (! $sport) {
            return;
        }

        $statistics = [
            [
                'name' => 'Vitória',
                'icon' => 'pi-check-circle',
                'color' => '#22c55e',
                'sport_id' => $sport->id,
            ],
            [
                'name' => 'Empate',
                'icon' => 'pi-minus-circle',
                'color' => '#f59e0b',
                'sport_id' => $sport->id,
            ],
            [
                'name' => 'Derrota',
                'icon' => 'pi-times-circle',
                'color' => '#ef4444',
                'sport_id' => $sport->id,
            ],
            [
                'name' => 'Gols Marcados',
                'icon' => 'pi-plus-circle',
                'color' => '#3b82f6',
                'sport_id' => $sport->id,
            ],
            [
                'name' => 'Gols Sofridos',
                'icon' => 'pi-minus-circle',
                'color' => '#ef4444',
                'sport_id' => $sport->id,
            ],
        ];

        DB::table('statistics_teams')->insert(
            collect($statistics)->map(fn ($stat) => array_merge($stat, [
                'created_at' => $now,
                'updated_at' => $now,
            ]))->toArray()
        );
    }
}
