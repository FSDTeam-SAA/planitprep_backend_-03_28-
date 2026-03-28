<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FitnessLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fitness_levels')->insert([
            ['name' => 'Beginner', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Intermediate', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Advanced', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Athlete', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
