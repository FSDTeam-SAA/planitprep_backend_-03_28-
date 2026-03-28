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
        if (! Schema::hasColumn('food_item_replacements', 'replace_food_item_id')) {
            Schema::table('food_item_replacements', function (Blueprint $table) {
                $table->integer('replace_food_item_id')->default(0)->index();
                $table->decimal('quantity', 10, 2)->default(0.00)->comment('Quantity of the food item in the plan (grams)');
                $table->integer('meal_plan_id')->default(0)->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
