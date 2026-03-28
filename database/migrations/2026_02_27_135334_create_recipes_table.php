<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {

            $table->id(); // Id (auto increment)

            $table->unsignedBigInteger('userid');

            $table->date('date');

            $table->enum('meal_type', [
                'BREAKFAST',
                'LUNCH',
                'SNACKS',
                'DINNER',
                'SURPRISE_MEAL'
            ]);

            $table->enum('preparation_type', [
                'BATCH_MEAL',
                'DAY_WISE_MEAL'
            ]);

            $table->string('type')->nullable();

            $table->string('recipeName');

            $table->string('prepTime')->nullable();

            $table->string('calories')->nullable();

            $table->boolean('isVegetarian')->default(false);

            $table->string('recipePoints')->nullable();

            $table->json('grocery_list')->nullable();

            $table->boolean('isFavorate')->default(false);

            $table->timestamp('createDate')->nullable();

            $table->timestamp('modifyDate')->nullable();

            $table->boolean('isDeleted')->default(0);

            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};