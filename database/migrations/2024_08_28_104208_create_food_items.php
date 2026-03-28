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
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('')->comment('Name of the food item');
            $table->integer('food_group_id')->default(0)->index();
            $table->char('type', 2)->default('')->comment('veg:VG, noveg:NV, Eggetarian:EG, Vegan:VE');
            $table->decimal('serving_size', 10, 2)->default(0.00)->comment('Serving size amount (typically in grams)');
            $table->string('serving_unit', 50)->default('')->comment('Unit of the serving size (e.g., grams, cups)');
            $table->decimal('calories', 10, 2)->default(0.00)->nullable()->comment('Calories (kcal) per serving');
            $table->decimal('total_fat', 10, 2)->default(0.00)->nullable()->comment('Total fat content per serving (grams)');
            $table->decimal('saturated_fat', 10, 2)->default(0.00)->nullable()->comment('Saturated fat content per serving (grams)');
            $table->decimal('trans_fat', 10, 2)->default(0.00)->nullable()->comment('Trans fat content per serving (grams)');
            $table->decimal('monounsaturated_fat', 10, 2)->default(0.00)->nullable()->comment('Monounsaturated fat content per serving (grams)');
            $table->decimal('polyunsaturated_fat', 10, 2)->default(0.00)->nullable()->comment('Polyunsaturated fat content per serving (grams)');
            $table->decimal('cholesterol', 10, 2)->default(0.00)->nullable()->comment('Cholesterol content per serving (milligrams)');
            $table->decimal('sodium', 10, 2)->default(0.00)->nullable()->comment('Sodium content per serving (milligrams)');
            $table->decimal('total_carbohydrates', 10, 2)->default(0.00)->comment('Total carbohydrate content per serving (grams)');
            $table->decimal('dietary_fiber', 10, 2)->default(0.00)->nullable()->comment('Dietary fiber content per serving (grams)');
            $table->decimal('sugars', 10, 2)->default(0.00)->nullable()->comment('Total sugars content per serving (grams)');
            $table->decimal('added_sugars', 10, 2)->default(0.00)->nullable()->comment('Added sugars content per serving (grams)');
            $table->decimal('protein', 10, 2)->default(0.00)->comment('Protein content per serving (grams)');
            $table->decimal('vitamin_a', 10, 2)->default(0.00)->nullable()->comment('Vitamin A content per serving (micrograms)');
            $table->decimal('vitamin_c', 10, 2)->default(0.00)->nullable()->comment('Vitamin C content per serving (milligrams)');
            $table->decimal('calcium', 10, 2)->default(0.00)->nullable()->comment('Calcium content per serving (milligrams)');
            $table->decimal('iron', 10, 2)->default(0.00)->nullable()->comment('Iron content per serving (milligrams)');
            $table->decimal('potassium', 10, 2)->default(0.00)->nullable()->comment('Potassium content per serving (milligrams)');
            $table->decimal('vitamin_d', 10, 2)->default(0.00)->nullable()->comment('Vitamin D content per serving (micrograms)');
            $table->decimal('vitamin_b6', 10, 2)->default(0.00)->nullable()->comment('Vitamin B6 content per serving (milligrams)');
            $table->decimal('vitamin_b12', 10, 2)->default(0.00)->nullable()->comment('Vitamin B12 content per serving (micrograms)');
            $table->decimal('magnesium', 10, 2)->default(0.00)->nullable()->comment('Magnesium content per serving (milligrams)');
            $table->decimal('glycemic_index', 10, 2)->default(0.00)->nullable()->comment('Glycemic index value (if applicable)');
            $table->text('comments')->nullable()->comment('Additional notes or comments about the food item');
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
        Schema::dropIfExists('food_items');
    }
};
