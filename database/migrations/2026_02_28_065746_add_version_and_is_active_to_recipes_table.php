<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {

            // Version number for slot history
            $table->integer('version')
                  ->default(1)
                  ->after('type');

            // Only one active recipe per slot
            $table->boolean('is_active')
                  ->default(true)
                  ->after('version');

        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {

            $table->dropColumn('version');
            $table->dropColumn('is_active');

        });
    }
};