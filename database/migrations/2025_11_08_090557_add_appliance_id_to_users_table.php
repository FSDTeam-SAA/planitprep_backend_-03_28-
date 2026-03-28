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
            $table->foreignId('appliance_id')
                  ->nullable()
                  ->constrained('appliances')   // Links to your new 'appliances' table
                  ->onDelete('set null');    // If an appliance is deleted, set this to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // This automatically drops the foreign key and the column
            $table->dropConstrainedForeignId('appliance_id');
        });
    }
};