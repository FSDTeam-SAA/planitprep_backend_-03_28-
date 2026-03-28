<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;     // <-- 1. Import DB Facade
use Illuminate\Support\Carbon; // <-- 2. Import Carbon for timestamps

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appliances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('isActive')->default(true);
            $table->timestamp('createdate')->nullable(); // <-- Your custom column
            $table->timestamp('modifydate')->nullable(); // <-- Your custom column
        });

        // 3. Insert your default data
        $now = Carbon::now();
        DB::table('appliances')->insert([
            ['id' => 1, 'name' => 'Stove & oven', 'createdate' => $now, 'modifydate' => $now],
            ['id' => 2, 'name' => 'Microwave only', 'createdate' => $now, 'modifydate' => $now],
            ['id' => 3, 'name' => 'Blender / air fryer', 'createdate' => $now, 'modifydate' => $now],
            ['id' => 4, 'name' => 'Limited (minimal tools)', 'createdate' => $now, 'modifydate' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appliances');
    }
};