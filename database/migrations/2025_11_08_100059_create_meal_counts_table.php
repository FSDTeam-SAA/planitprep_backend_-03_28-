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
        // Laravel will create the table as 'meal_counts' (plural)
        Schema::create('meal_counts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // No timestamps needed for this lookup table
        });

        // 2. Insert your default data
        DB::table('meal_counts')->insert([
            ['id' => 1, 'name' => '2'],
            ['id' => 2, 'name' => '3'],
            ['id' => 3, 'name' => '4 or more'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_counts');
    }
};