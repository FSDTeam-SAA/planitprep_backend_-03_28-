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
        Schema::create('weight_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->index()->comment('Foreign key referencing the user');
            $table->decimal('current_value', 10, 2)->default(0.00)->comment('Current value for the goal (e.g., current weight)');
            $table->decimal('target_value', 10, 2)->default(0.00)->comment('Target value for the goal (e.g., target weight)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weight_tracking');
    }
};
