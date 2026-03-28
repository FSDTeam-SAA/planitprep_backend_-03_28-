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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('');
            $table->string('code', 20);
            $table->char('type', 1)->default('A')->comment('A:Admin only, U:Users');
            $table->unsignedInteger('limit')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->char('coupon_type', 1)->default('')->comment('A for All, I for Individual');
            $table->enum('discount_type', ['flat', 'percent', 'b1g1'])->default('flat');
            $table->float('amount', 8, 2)->default(0)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
