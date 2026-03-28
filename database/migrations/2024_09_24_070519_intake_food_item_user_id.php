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
        if (! Schema::hasColumn('intake_food_items', 'user_id')) {
            Schema::table('intake_food_items', function (Blueprint $table) {
                $table->integer('user_id')->default(0)->nullable();
            });
        }

        if (! Schema::hasColumn('intake_food_items', 'meal_id')) {
            Schema::table('intake_food_items', function (Blueprint $table) {
                $table->integer('meal_id')->default(0)->nullable();
            });
        }

        if (! Schema::hasColumn('intake_food_items', 'date')) {
            Schema::table('intake_food_items', function (Blueprint $table) {
                $table->date('date')->nullable();
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
