<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CookingPrepSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cooking_preps')->insert([
            ['name' => 'Quick & Simple', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Balanced Cooking', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Batch Prep Friendly', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
