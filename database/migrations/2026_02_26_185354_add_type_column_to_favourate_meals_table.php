<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('favourate_meals', function (Blueprint $table) {
            $table->string('type')->nullable()->after('ai_res_id');
        });
    }

    public function down(): void
    {
        Schema::table('favourate_meals', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};