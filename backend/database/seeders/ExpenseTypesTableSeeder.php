<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $types = [
            'Água',
            'Internet',
            'Energia Elétrica',
            'Manutenção do Campo',
            'Funcionários',
            'Compra de Equipamentos',
            'Limpeza',
            'Divulgação / Marketing',
            'Taxa da Plataforma',
            'Comandas',
            'Pagamento de Cartão',
            'Aluguel',
            'Fornecedores',
            'Aplicações',
            'Outros',
        ];

        DB::table('expense_types')->insert(
            collect($types)->map(fn ($type) => [
                'name' => $type,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
