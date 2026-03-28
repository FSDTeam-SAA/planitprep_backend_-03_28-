<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_allergies')) {
            Schema::create('user_allergies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('allergy_id')->constrained('allergens')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'allergy_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_allergies');
    }
};
