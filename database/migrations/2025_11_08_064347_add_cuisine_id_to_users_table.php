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
            // This is the best way to add a foreign key
            $table->foreignId('cuisine_id')
                ->nullable()                // Makes it optional
                ->constrained('cuisines')   // Links to the 'id' on the 'cuisines' table
                ->onDelete('set null');      // If a cuisine is deleted, set this to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // This automatically drops the foreign key and the column
            $table->dropConstrainedForeignId('cuisine_id');
        });
    }
};
