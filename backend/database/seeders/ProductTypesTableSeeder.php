<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_types')->insert(['name' => 'Eletrônico']);
        DB::table('product_types')->insert(['name' => 'Vestuário']);
        DB::table('product_types')->insert(['name' => 'Alimento']);
        DB::table('product_types')->insert(['name' => 'Bebida']);
        DB::table('product_types')->insert(['name' => 'Acessório']);
        DB::table('product_types')->insert(['name' => 'Equipamento']);
        DB::table('product_types')->insert(['name' => 'Suplemento']);
        DB::table('product_types')->insert(['name' => 'Tira gosto']);
        DB::table('product_types')->insert(['name' => 'Outro']);
    }
}
