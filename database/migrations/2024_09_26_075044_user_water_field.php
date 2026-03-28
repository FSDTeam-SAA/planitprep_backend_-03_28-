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
        if (! Schema::hasColumn('users', 'daily_water')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('daily_water', 10, 2)->default(0.00)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
