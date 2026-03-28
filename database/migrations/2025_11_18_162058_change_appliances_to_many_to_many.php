<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // OLD CODE THAT CRASHED:
        // Schema::create('user_appliances', function (Blueprint $table) { ... });

        // NEW SAFE CODE:
        if (!Schema::hasTable('user_appliances')) {
            Schema::create('user_appliances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('appliance_id');
                // Add any other columns that were in the original file here
                $table->timestamps(); 
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_appliances');
    }
};