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
        Schema::create('login_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('email')->default(false);
            $table->boolean('phone')->default(false);
            $table->boolean('facebook')->default(false);
            $table->boolean('google')->default(false);
            $table->boolean('apple')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_settings');
    }
};
