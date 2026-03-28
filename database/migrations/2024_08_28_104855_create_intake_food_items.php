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
        Schema::create('intake_food_items', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->nullable();
            $table->integer('meal_id')->default(0)->nullable();
            $table->unsignedBigInteger('food_id')->default(0)->index()->comment('Foreign key referencing the food item');
            $table->decimal('quantity', 10, 2)->default(0.00)->comment('Quantity of the food item consumed (grams)');
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intake_food_items');
    }
};
