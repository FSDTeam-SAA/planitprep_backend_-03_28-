<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('')->comment('Name of the meal (e.g., Breakfast, Lunch, Dinner, etc.)');
            $table->integer('order')->default(0)->comment('Order of the meal in the day (1 for Breakfast, 2 for Morning Snack, etc.)');
            $table->string('image')->default('');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
