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
        Schema::create('user_meal_plan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->index()->comment('Foreign key referencing the meal plan');
            $table->unsignedInteger('day')->default(0)->index()->comment('Foreign key referencing the meal plan');
            $table->unsignedBigInteger('meal_id')->default(0)->index()->comment('Foreign key referencing the meal plan');
            $table->unsignedBigInteger('meal_plan_id')->default(0)->index()->comment('Foreign key referencing the meal plan');
            $table->unsignedBigInteger('food_item_id')->default(0)->index()->comment('Foreign key referencing the food item');
            $table->decimal('quantity', 10, 2)->default(0.00)->comment('Quantity of the food item in the plan (grams)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_meal_plan_items');
    }
};
