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
        Schema::create('cooking_time', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // $table->timestamps(); 
        });

        // 2. Insert your default data
        DB::table('cooking_time')->insert([
            ['id' => 1, 'name' => 'Under 15 minutes'],
            ['id' => 2, 'name' => '15–30 minutes'],
            ['id' => 3, 'name' => '30–60 minutes'],
            ['id' => 4, 'name' => 'Over 1 hour'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooking_time');
    }
};