<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::truncate();
        DB::table('countries')->insert([
            'name' => 'India',
            'iso_code' => 'IND',
            'iso_code_2' => 'IN',
            'phone_code' => '+91',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
