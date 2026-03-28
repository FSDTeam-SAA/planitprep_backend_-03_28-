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
        Schema::create('ai_responses', function (Blueprint $table) {
        $table->id();
        $table->string('user_id');
        $table->string('type'); // diet_plan, weekly_diet, surprise_meal
        $table->date('response_date'); // YYYY-MM-DD
        $table->json('response_json');
        $table->timestamps();

        $table->unique(['user_id', 'type', 'response_date']);
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_responses');
    }
};
