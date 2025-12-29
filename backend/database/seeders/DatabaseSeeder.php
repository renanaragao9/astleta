<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seederNames = [
            PermissionSeeder::class,
            ProfileSeeder::class,
            SportsTableSeeder::class,
            PositionsTableSeeder::class,
            FeaturesTableSeeder::class,
            PaymentFormsTableSeeder::class,
            UserSeeder::class,
            FieldTypesTableSeeder::class,
            FieldSurfacesTableSeeder::class,
            FieldSizesTableSeeder::class,
            ContactTypesTableSeeder::class,
            DocumentTypesTableSeeder::class,
            ProductTypesTableSeeder::class,
            ExpenseTypesTableSeeder::class,
            FieldItemsTableSeeder::class,
            TeamTypesTableSeeder::class,
            AddressTypesTableSeeder::class,
            TeamsTableSeeder::class,
            SkillsTableSeeder::class,
            StatisticsTableSeeder::class,
            StatisticsTeamTableSeeder::class,
            MarketingTypeSeeder::class,
            MarketingSeeder::class,
        ];

        foreach ($seederNames as $seederName) {
            $exists = DB::table('seeders')
                ->where('name', $seederName)
                ->exists();

            if (! $exists) {
                $this->call($seederName);
                DB::table('seeders')->insert([
                    'name' => $seederName,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
