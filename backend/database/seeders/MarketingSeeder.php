<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        // Assumindo que o tipo 'Software' tem id 4, baseado na ordem do seeder anterior
        DB::table('marketings')->insert([
            [
                'title' => 'astleta',
                'image_path' => null,
                'link' => 'https://astleta.com',
                'content' => 'Descubra o Astleta, a plataforma ideal para gerenciar esportes e equipes. Junte-se a nós e eleve seu jogo!',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(6)->toDateString(),
                'age' => 0,
                'marketing_type_id' => 4, // Software
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
