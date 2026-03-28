<?php

namespace Database\Seeders;

use App\Models\ServingUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServingUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServingUnit::truncate();
        DB::table('serving_units')->insert([
            ['id' => 1, 'name' => 'grams', 'unit' => 'g'],
            ['id' => 2, 'name' => 'kilograms', 'unit' => 'kg'],
            ['id' => 3, 'name' => 'ounces', 'unit' => 'oz'],
            ['id' => 4, 'name' => 'pound', 'unit' => 'lb'],
            ['id' => 5, 'name' => 'milligrams', 'unit' => 'mg'],
            ['id' => 6, 'name' => 'milliliters', 'unit' => 'ml'],
            ['id' => 7, 'name' => 'liters', 'unit' => 'l'],
            ['id' => 8, 'name' => 'teaspoon', 'unit' => 'tsp'],
            ['id' => 9, 'name' => 'tablespoon', 'unit' => 'tbsp'],
            ['id' => 10, 'name' => 'cup', 'unit' => 'cup'],
            ['id' => 11, 'name' => 'piece', 'unit' => 'piece'],
            ['id' => 12, 'name' => 'slice', 'unit' => 'slice'],
        ]);
    }
}
