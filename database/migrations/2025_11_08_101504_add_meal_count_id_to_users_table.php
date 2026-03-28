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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('meal_count_id')
                  ->nullable()
                  ->after('meal_prep_schedule_id') // Places it after the last column we added
                  ->constrained('meal_counts') // Links to your new 'meal_counts' table
                  ->onDelete('set null'); // If a meal count is deleted, set this to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // This drops the foreign key and the column
            $table->dropConstrainedForeignId('meal_count_id');
        });
    }
};