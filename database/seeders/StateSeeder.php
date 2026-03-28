<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        State::truncate();
        DB::table('states')->insert([
            ['country_id' => 1, 'name' => 'Maharashtra', 'iso_code' => 'MH', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'Karnataka', 'iso_code' => 'KA', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'Gujarat', 'iso_code' => 'GJ', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'Punjab', 'iso_code' => 'PB', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'Rajasthan', 'iso_code' => 'RJ', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'Uttar Pradesh', 'iso_code' => 'UP', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'Tamil Nadu', 'iso_code' => 'TN', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'West Bengal', 'iso_code' => 'WB', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'Bihar', 'iso_code' => 'BR', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => 1, 'name' => 'Haryana', 'iso_code' => 'HR', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
