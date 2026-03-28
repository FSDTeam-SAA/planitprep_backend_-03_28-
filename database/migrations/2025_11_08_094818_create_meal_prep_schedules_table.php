<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- 1. Import the DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Laravel's convention will create the table as 'meal_prep_schedules'
        Schema::create('meal_prep_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // We don't need timestamps for a simple lookup table
        });

        // 2. Insert your default data
        DB::table('meal_prep_schedules')->insert([
            ['id' => 1, 'name' => 'Morning'],
            ['id' => 2, 'name' => 'Evening'],
            ['id' => 3, 'name' => 'Weekends (batch prep)'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_prep_schedules');
    }
};