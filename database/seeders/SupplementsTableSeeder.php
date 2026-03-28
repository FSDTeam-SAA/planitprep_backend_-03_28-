<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SupplementsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('supplements')->truncate();

        \DB::table('supplements')->insert([
            0 => [
                'id' => 1,
                'name' => 'Fish Oil',
                'supplement_type_id' => 1,
                'dosage' => '1,000 mg daily',
                'benefits' => '<p>Supports heart health, reduces inflammation</p>',
                'side_effects' => '<p>May cause fishy aftertaste, digestive upset</p>',
                'active' => 1,
                'image' => '1725355659_milk-dairy-products.jpg',
                'created_at' => '2024-09-03 09:27:39',
                'updated_at' => '2024-09-03 09:38:03',
            ],
            1 => [
                'id' => 2,
                'name' => 'Vitamin D3',
                'supplement_type_id' => 2,
                'dosage' => '1,000-2,000 IU daily',
                'benefits' => '<p>Supports bone health, immune function</p>',
                'side_effects' => '<p>May cause nausea, headache in high doses</p>',
                'active' => 1,
                'image' => '',
                'created_at' => '2024-09-03 09:38:31',
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => 'Magnesium',
                'supplement_type_id' => 3,
                'dosage' => '200-400 mg daily',
                'benefits' => '<p>Supports muscle function, blood pressure regulation</p>',
                'side_effects' => '<p>May cause diarrhea or stomach cramps</p>',
                'active' => 1,
                'image' => '',
                'created_at' => '2024-09-03 09:38:57',
                'updated_at' => null,
            ],
            3 => [
                'id' => 4,
                'name' => 'Protein Powder',
                'supplement_type_id' => 4,
                'dosage' => '20-30 grams post-workout',
                'benefits' => '<p>Supports muscle growth and recovery</p>',
                'side_effects' => '<p>May cause digestive issues or allergic reactions</p>',
                'active' => 1,
                'image' => '',
                'created_at' => '2024-09-03 09:39:24',
                'updated_at' => null,
            ],
            4 => [
                'id' => 5,
                'name' => 'CoQ10',
                'supplement_type_id' => 5,
                'dosage' => '100-200 mg daily',
                'benefits' => '<p>Supports heart health, energy production</p>',
                'side_effects' => '<p>May cause digestive upset or headaches</p>',
                'active' => 1,
                'image' => '',
                'created_at' => '2024-09-03 09:39:50',
                'updated_at' => null,
            ],
        ]);

    }
}
