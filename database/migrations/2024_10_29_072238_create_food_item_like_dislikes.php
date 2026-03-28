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
        Schema::create('food_item_like_dislikes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('food_item_id')->index();
            $table->char('type', 1)->default('')->comment('L:like,D:dilike');
            $table->boolean('replace')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_item_like_dislikes');
    }
};
