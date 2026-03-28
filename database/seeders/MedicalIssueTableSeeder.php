<?php

namespace Database\Seeders;

use App\Models\MedicalIssue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalIssueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedicalIssue::truncate();
        DB::table('medical_issues')->insert([
            ['id' => 1, 'name' => 'Diabetes'],
            ['id' => 2, 'name' => 'Hypertension (High Blood Pressure)'],
            ['id' => 3, 'name' => 'Heart Disease'],
            ['id' => 4, 'name' => 'Kidney Disease'],
            ['id' => 5, 'name' => 'Food Allergies (e.g., nuts, shellfish)'],
            ['id' => 6, 'name' => 'Lactose Intolerance'],
            ['id' => 7, 'name' => 'Gluten Intolerance'],
            ['id' => 8, 'name' => 'Irritable Bowel Syndrome (IBS)'],
            ['id' => 9, 'name' => 'Anemia'],
            ['id' => 10, 'name' => 'Osteoporosis'],
            ['id' => 11, 'name' => 'Hypothyroidism'],
            ['id' => 12, 'name' => 'Hyperthyroidism'],
            ['id' => 13, 'name' => 'High Cholesterol'],
            ['id' => 14, 'name' => 'Gout'],
            ['id' => 15, 'name' => 'Fatty Liver'],
            ['id' => 16, 'name' => 'Cirrhosis'],
            ['id' => 17, 'name' => 'Cancer (with specific dietary needs)'],
            ['id' => 18, 'name' => 'Eating Disorders (e.g., anorexia, bulimia)'],
            ['id' => 19, 'name' => 'Obesity'],
            ['id' => 20, 'name' => 'Pancreatitis'],
            ['id' => 21, 'name' => 'Stroke Recovery'],
            ['id' => 22, 'name' => 'Others'],
        ]);
    }
}
