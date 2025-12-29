<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            ['name' => 'Coletes', 'icon' => 'fas fa-tshirt'],
            ['name' => 'Apito', 'icon' => 'fas fa-bullhorn'],
            ['name' => 'Cronômetro', 'icon' => 'fas fa-stopwatch'],
            ['name' => 'Placar Manual', 'icon' => 'fas fa-list-ol'],
            ['name' => 'Água / Gelo', 'icon' => 'fas fa-tint'],
            ['name' => 'Vestiários com Chuveiro', 'icon' => 'fas fa-shower'],
            ['name' => 'Estacionamento Gratuito', 'icon' => 'fas fa-parking'],
            ['name' => 'Sistema de Som', 'icon' => 'fas fa-volume-up'],
            ['name' => 'Área de Descanso', 'icon' => 'fas fa-couch'],
            ['name' => 'Lanchonete', 'icon' => 'fas fa-coffee'],
            ['name' => 'Wi-Fi Gratuito', 'icon' => 'fas fa-wifi'],
            ['name' => 'Segurança 24h', 'icon' => 'fas fa-shield-alt'],
            ['name' => 'Climatização', 'icon' => 'fas fa-snowflake'],
            ['name' => 'Banheiros', 'icon' => 'fas fa-restroom'],
            ['name' => 'Bolas Disponíveis', 'icon' => 'fas fa-futbol'],
            ['name' => 'Vestiários Masculino/Feminino', 'icon' => 'fas fa-people-arrows'],
        ];

        DB::table('field_items')->insert(
            collect($items)->map(fn ($item) => [
                'name' => $item['name'],
                'icon' => $item['icon'],
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
