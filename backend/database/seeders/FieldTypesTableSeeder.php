<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $types = [
            'Campo de Futebol',
            'Campo Society',
            'Campo de Areia',
            'Quadra de Areia',
            'Ginásio',
        ];

        DB::table('field_types')->insert(
            collect($types)->map(fn ($type) => [
                'name' => $type,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
