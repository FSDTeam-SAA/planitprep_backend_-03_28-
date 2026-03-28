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
        Schema::create('batch_meal_inventory', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('recipe_id');

            $table->integer('total_portions');
            $table->integer('used_portions')->default(0);
            $table->integer('remaining_portions')->default(0);

            $table->integer('calories_per_portion');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Foreign Keys (optional but recommended)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('recipe_id')
                  ->references('id')
                  ->on('recipes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_meal_inventory');
    }
};