<?php

namespace Database\Seeders;

use App\Models\ChatgptAiSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatgptAiSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChatgptAiSetting::truncate();
        DB::table('chatgpt_ai_settings')->insert([
            'api_key' => '',
            'model' => '',
            'active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
