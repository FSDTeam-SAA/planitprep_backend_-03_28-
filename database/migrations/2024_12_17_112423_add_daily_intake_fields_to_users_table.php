<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDailyIntakeFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adding the new fields with default values and comments
            $table->integer('daily_calorie_intake')->default(1800)->comment('Unit Kcal');
            $table->integer('daily_carbs_intake')->default(150)->comment('Unit grams');
            $table->integer('daily_fiber_intake')->default(10)->comment('Unit grams');
            $table->integer('daily_protein_intake')->default(100)->comment('Unit grams');
            $table->integer('daily_fats_intake')->default(50)->comment('Unit grams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping the fields when rolled back
            $table->dropColumn([
                'daily_calorie_intake',
                'daily_carbs_intake',
                'daily_fiber_intake',
                'daily_protein_intake',
                'daily_fats_intake',
            ]);
        });
    }
}
