<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            // Plan basic info
            $table->string('title');
            $table->string('price');
            $table->string('duration');

            // Stripe price id or unique plan identifier
            $table->string('price_id')->unique();

            // Highlight badge
            $table->boolean('highlight')->default(false);

            // Features stored as JSON array
            $table->json('features');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
