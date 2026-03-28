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
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'apple_store_image')) {
                $table->string('apple_store_image')->default('');
            }
            if (! Schema::hasColumn('settings', 'apple_store_link')) {
                $table->string('apple_store_link')->default('');
            }
            if (! Schema::hasColumn('settings', 'android_store_image')) {
                $table->string('android_store_image')->default('');
            }
            if (! Schema::hasColumn('settings', 'android_store_link')) {
                $table->string('android_store_link')->default('');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
