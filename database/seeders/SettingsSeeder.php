<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::truncate();
        Setting::insert([
            'logo' => '',
            'facebook' => '',
            'instagram' => '',
            'x' => '',
            'youtube' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
            'marquee' => 'Transform your health with personalized nutrition plans! Start your journey to wellness today!',
        ]);
    }
}
