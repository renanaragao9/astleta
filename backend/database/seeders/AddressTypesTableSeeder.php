<?php

namespace Database\Seeders;

use App\Models\AddressType;
use Illuminate\Database\Seeder;

class AddressTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $addressTypes = [
            ['name' => 'Residencial'],
            ['name' => 'Comercial'],
        ];

        foreach ($addressTypes as $addressType) {
            AddressType::create($addressType);
        }
    }
}
