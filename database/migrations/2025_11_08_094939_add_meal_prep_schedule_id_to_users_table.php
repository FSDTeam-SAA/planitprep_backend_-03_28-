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
            $table->foreignId('meal_prep_schedule_id')
                  ->nullable()
                  ->constrained('meal_prep_schedules') // Links to your new 'meal_prep_schedules' table
                  ->onDelete('set null'); // If a schedule is deleted, set this to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // This drops the foreign key and the column
            $table->dropConstrainedForeignId('meal_prep_schedule_id');
        });
    }
};