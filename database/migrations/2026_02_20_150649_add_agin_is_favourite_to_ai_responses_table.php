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
        Schema::table('ai_responses', function (Blueprint $table) {
            $table->boolean('is_favourite')
                  ->default(0)
                  ->after('response_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_responses', function (Blueprint $table) {
            $table->dropColumn('is_favourite');
        });
    }
};