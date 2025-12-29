<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldSurfacesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $surfaces = [
            'Grama Natural',
            'Grama Sintética',
            'Areia',
            'Madeira',
            'Piso de Concreto',
            'Asfalto',
            'Tartan',
            'Piso Flutuante',
        ];

        DB::table('field_surfaces')->insert(
            collect($surfaces)->map(fn ($surface) => [
                'name' => $surface,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
