<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExercisePlanDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('exercise_plan_days')->insert([
            ['name' => '1-2 day', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '3-4 days', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '5-6 days', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Everyday', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
