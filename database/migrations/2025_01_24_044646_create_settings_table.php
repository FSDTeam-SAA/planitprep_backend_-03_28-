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
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('logo')->default('');
                $table->string('facebook')->default('');
                $table->string('instagram')->default('');
                $table->string('x')->default('');
                $table->string('youtube')->default('');
                $table->text('address')->nullable();
                $table->string('phone')->default('');
                $table->string('email')->default('');
                $table->string('marquee')->default('');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
