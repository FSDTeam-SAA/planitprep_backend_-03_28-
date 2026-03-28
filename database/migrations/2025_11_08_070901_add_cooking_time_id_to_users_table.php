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
            $table->foreignId('cooking_time_id')
                  ->nullable()
                  ->constrained('cooking_time') // Links to your new 'cooking_time' table
                  ->onDelete('set null'); // If a cooking time is deleted, set this to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // This drops the foreign key and the column
            $table->dropConstrainedForeignId('cooking_time_id');
        });
    }
};
