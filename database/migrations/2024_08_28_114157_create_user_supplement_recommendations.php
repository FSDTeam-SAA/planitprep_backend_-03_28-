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
        Schema::create('user_supplement_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->index()->comment('Foreign key referencing the user');
            $table->unsignedBigInteger('supplement_id')->default(0)->index()->comment('Foreign key referencing the supplement');
            $table->string('dosage')->default('')->comment('Dosage recommended for the user');
            $table->string('frequency')->default('')->comment('Frequency of intake (e.g., daily, weekly)');
            $table->date('start_date')->nullable()->comment('Start date of the recommendation');
            $table->date('end_date')->nullable()->comment('End date of the recommendation (optional)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_supplement_recommendations');
    }
};
