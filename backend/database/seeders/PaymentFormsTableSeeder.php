<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentFormsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payment_forms')->insert(['name' => 'Cartão de Crédito', 'type' => 'online']);
        DB::table('payment_forms')->insert(['name' => 'Cartão de Débito', 'type' => 'online']);
        DB::table('payment_forms')->insert(['name' => 'Pix', 'type' => 'online']);
        DB::table('payment_forms')->insert(['name' => 'Dinheiro', 'type' => 'presencial']);
        DB::table('payment_forms')->insert(['name' => 'Pix', 'type' => 'presencial']);
        DB::table('payment_forms')->insert(['name' => 'Cheque', 'type' => 'presencial']);
        DB::table('payment_forms')->insert(['name' => 'Cartão de Crédito', 'type' => 'presencial']);
        DB::table('payment_forms')->insert(['name' => 'Cartão de Débito', 'type' => 'presencial']);
    }
}
