<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SupplementTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('supplement_types')->truncate();

        \DB::table('supplement_types')->insert([
            0 => [
                'id' => 1,
                'name' => 'Omega-3',
                'image' => '',
                'active' => 1,
                'created_at' => '2024-09-03 06:39:06',
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'Vitamin',
                'image' => '',
                'active' => 1,
                'created_at' => '2024-09-03 06:39:15',
                'updated_at' => '2024-09-03 09:36:17',
            ],
            2 => [
                'id' => 3,
                'name' => 'Mineral',
                'image' => '',
                'active' => 1,
                'created_at' => '2024-09-03 09:36:24',
                'updated_at' => null,
            ],
            3 => [
                'id' => 4,
                'name' => 'Protein',
                'image' => '',
                'active' => 1,
                'created_at' => '2024-09-03 09:36:31',
                'updated_at' => null,
            ],
            4 => [
                'id' => 5,
                'name' => 'Antioxidant',
                'image' => '',
                'active' => 1,
                'created_at' => '2024-09-03 09:36:38',
                'updated_at' => null,
            ],
        ]);

    }
}
