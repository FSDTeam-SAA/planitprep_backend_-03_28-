<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('goals')->insert([
            ['name' => 'Weight Loss', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maintain Weight', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Weight Gain', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Muscle Building', 'image' => '', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
