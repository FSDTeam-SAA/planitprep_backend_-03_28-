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
        Schema::create('supplements', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('')->comment('Name of the supplement');
            $table->integer('supplement_type_id')->default(0);
            $table->string('dosage')->default('')->comment('Recommended dosage (e.g., 1000 mg, 1 capsule)');
            $table->text('benefits')->nullable()->comment('Benefits of the supplement');
            $table->text('side_effects')->nullable()->comment('Possible side effects');
            $table->string('image')->default('');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplements');
    }
};
