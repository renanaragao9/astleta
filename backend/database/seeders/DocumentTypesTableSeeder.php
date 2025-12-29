<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $types = [
            'CPF - Frente',
            'CPF - Verso',
            'CNH - Frente',
            'CNH - Verso',
            'RG - Frente',
            'RG - Verso',
            'Comprovante de Endereço',
            'Contrato',
            'Alvará',
        ];

        DB::table('document_types')->insert(
            collect($types)->map(fn ($type) => [
                'name' => $type,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
