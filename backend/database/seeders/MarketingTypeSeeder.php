<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketingTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('marketing_types')->insert([
            ['name' => 'Bets', 'description' => 'Marketing relacionado a apostas'],
            ['name' => 'Oficina', 'description' => 'Marketing para oficinas'],
            ['name' => 'Escola', 'description' => 'Marketing para escolas'],
            ['name' => 'Software', 'description' => 'Marketing para softwares'],
        ]);
    }
}
