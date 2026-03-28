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
        Schema::create('similar_food_items', function (Blueprint $table) {
            $table->id();
            $table->integer('food_item_id')->default(0)->index();
            $table->integer('similar_item_id')->default(0)->index();
            $table->decimal('quantity', 10, 2)->default(0.00)->comment('Quantity of the food item in the plan (grams)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('similar_food_items');
    }
};
