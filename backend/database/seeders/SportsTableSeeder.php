<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $sports = [
            'Futebol',
            'Vôlei',
        ];

        DB::table('sports')->insert(
            collect($sports)->map(fn ($sport) => [
                'name' => $sport,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
