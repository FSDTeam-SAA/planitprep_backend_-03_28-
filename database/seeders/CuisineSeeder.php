<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuisineSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cuisines')->insert([
            ['name' => 'Indian', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mediterranean', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Continental', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Asian', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
