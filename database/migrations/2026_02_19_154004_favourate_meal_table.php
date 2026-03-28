<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favourate_meals', function (Blueprint $table) {
            $table->id(); // Primary Key

            $table->unsignedBigInteger('user_id'); 
            $table->string('ai_res_id'); 

            $table->date('date'); // year-month-day format

            $table->json('response_json'); 

            $table->timestamp('created_at')->useCurrent();

            // Foreign Key
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favourate_meals');
    }
};
