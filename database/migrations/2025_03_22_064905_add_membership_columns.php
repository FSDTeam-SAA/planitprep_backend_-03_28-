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
        if (! Schema::hasColumn('memberships', 'description')) {
            Schema::table('memberships', function (Blueprint $table) {
                $table->text('description')->nullable()->after('title');
                $table->decimal('discounted_price', 10, 2)->default(0.00)->after('price');
                $table->string('image', 225)->default('')->after('month');
                $table->integer('rank')->default(0)->after('image');
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
