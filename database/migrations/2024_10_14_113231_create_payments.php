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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_membership_id')->default(0)->index();
            $table->integer('user_id')->default(0)->index();
            $table->string('transaction_id', 225)->default('');
            $table->string('payment_method', 50)->default('');
            $table->double('price', 8, 2);
            $table->char('status', 2)->default('P')->comment('P:pending, S:success, C:cancel');
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
