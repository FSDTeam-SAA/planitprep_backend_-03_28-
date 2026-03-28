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
        Schema::create('progress_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->index()->comment('Foreign key referencing the user');
            $table->unsignedBigInteger('goal_id')->default(0)->index()->comment('Foreign key referencing the goal');
            $table->date('log_date')->nullable()->comment('Date of the progress log entry');
            $table->decimal('value', 10, 2)->default(0.00)->comment('Logged value (e.g., current weight)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_logs');
    }
};
