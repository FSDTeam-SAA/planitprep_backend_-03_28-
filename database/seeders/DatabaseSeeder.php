<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(AdminTableSeeder::class);
        $this->call(MealTableSeeder::class);
        $this->call(MedicalIssueTableSeeder::class);
        $this->call(FitnessLevelSeeder::class);
        $this->call(GoalSeeder::class);
        $this->call(SupplementTypesTableSeeder::class);
        $this->call(SupplementsTableSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(StateSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(ExercisePlanDaySeeder::class);
        $this->call(CookingPrepSeeder::class);
        $this->call(CuisineSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(NewsletterSeeder::class);
        $this->call(ServingUnitSeeder::class);
        $this->call(ChatgptAiSettingSeeder::class);
        $this->call(HomepagesTableSeeder::class);
    }
}
