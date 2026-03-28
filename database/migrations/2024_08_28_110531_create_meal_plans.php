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
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id')->default(0)->index()->comment('Foreign key referencing the user');
            $table->string('name')->default('')->comment('Name of the meal plan');
            $table->text('description')->nullable()->comment('Description of the meal plan');
            $table->tinyInteger('active')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
