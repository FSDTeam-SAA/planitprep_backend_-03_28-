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
            // This is the "Laravel way" to add a foreign key
            $table->foreignId('cooking_prep_id')
                ->nullable()                  // Makes it optional (a user doesn't have to have one)
                ->after('password')           // Puts it after the password column
                ->constrained('cooking_preps')  // Links to the 'id' on the 'cooking_preps' table
                ->onDelete('set null');        // If a cooking prep is deleted, set this field to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // This line automatically removes the foreign key and the column
            $table->dropConstrainedForeignId('cooking_prep_id');
        });
    }
};
