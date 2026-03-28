<?php

namespace Database\Seeders;

use App\Models\Meal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Meal::truncate();

        DB::table('meals')->insert([
            ['id' => 1, 'name' => 'Breakfast', 'order' => 1],
            ['id' => 2, 'name' => 'Morning Snack', 'order' => 2],
            ['id' => 3, 'name' => 'Lunch', 'order' => 3],
            ['id' => 4, 'name' => 'Evening Snack', 'order' => 4],
            ['id' => 5, 'name' => 'Supper/Dinner', 'order' => 5],
        ]);
    }
}
