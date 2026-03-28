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
        Schema::create('daily_intakes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->index();
            $table->unsignedBigInteger('meal_id')->default(0)->index();
            $table->date('intake_date')->nullable();
            $table->decimal('total_calories', 10, 2)->default(0.00);
            $table->decimal('total_fat', 10, 2)->default(0.00)->comment('in grams');
            $table->decimal('total_carbohydrates', 10, 2)->default(0.00)->comment('in grams');
            $table->decimal('total_protein', 10, 2)->default(0.00)->comment('in grams');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_intakes');
    }
};
