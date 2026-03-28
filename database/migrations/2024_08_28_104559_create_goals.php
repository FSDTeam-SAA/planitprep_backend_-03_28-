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
        if (! Schema::hasTable('user_goals')) {
            Schema::create('user_goals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->default(0)->index()->comment('Foreign key referencing the user');
                $table->integer('type')->default(0);
                $table->decimal('current_value', 10, 2)->default(0.00)->comment('Current value for the goal (e.g., current weight)');
                $table->decimal('target_value', 10, 2)->default(0.00)->comment('Target value for the goal (e.g., target weight)');
                $table->date('start_date')->nullable()->comment('Start date of the goal');
                $table->date('end_date')->nullable()->comment('End date of the goal (if applicable)');
                $table->char('status', 1)->default('A')->comment('A: Active, C:Completed, F:Failed');
                $table->string('image')->default('');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_goals');
    }
};
