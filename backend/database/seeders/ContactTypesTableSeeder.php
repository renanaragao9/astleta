<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $types = [
            [
                'name' => 'Telefone',
                'icon' => 'fa-solid fa-phone',
            ],
            [
                'name' => 'Celular',
                'icon' => 'fa-solid fa-mobile-screen',
            ],
            [
                'name' => 'WhatsApp',
                'icon' => 'fa-brands fa-whatsapp',
            ],
            [
                'name' => 'E-mail',
                'icon' => 'fa-solid fa-envelope',
            ],
            [
                'name' => 'Instagram',
                'icon' => 'fa-brands fa-instagram',
            ],
            [
                'name' => 'Facebook',
                'icon' => 'fa-brands fa-facebook',
            ],
            [
                'name' => 'LinkedIn',
                'icon' => 'fa-brands fa-linkedin',
            ],
            [
                'name' => 'Site',
                'icon' => 'fa-solid fa-globe',
            ],
            [
                'name' => 'Wi-Fi',
                'icon' => 'fa-solid fa-wifi',
            ],
        ];

        DB::table('contact_types')->insert(
            collect($types)->map(fn($type) => [
                'name' => $type['name'],
                'icon' => $type['icon'],
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray()
        );
    }
}
