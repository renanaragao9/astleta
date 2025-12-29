<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldSizesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $sizes = [
            'Personalizado',
            'Pequeno',
            'Médio',
            'Grande',
            'Reduzido',
            'Oficial Vôlei',
            'Oficial Vôlei (Praia)',
            'Oficial Futebol',
            'Oficial Society',
            'Oficial Society (Fut7)',
        ];

        DB::table('field_sizes')->insert(
            collect($sizes)->map(fn ($size) => [
                'name' => $size,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
