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
        Schema::create('user_test_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->index();
            $table->string('name', 100)->default('');
            $table->string('file', 100)->default('');
            $table->date('test_date')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_test_reports');
    }
};
