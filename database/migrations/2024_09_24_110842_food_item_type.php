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
        if (! Schema::hasColumn('food_items', 'type')) {
            Schema::table('food_items', function (Blueprint $table) {
                $table->char('type', 2)->default('')->comment('veg:VG, noveg:NV, Eggetarian:EG, Vegan:VE');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
