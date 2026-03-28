<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stripe_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('stripe_payments', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')
                    ->nullable()
                    ->after('user_id');
            } else {
                $table->string('stripe_payment_intent_id')
                    ->nullable()
                    ->change();
            }

            if (! Schema::hasColumn('stripe_payments', 'stripe_invoice_id')) {
                $table->string('stripe_invoice_id')
                    ->unique()
                    ->after('stripe_payment_intent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stripe_payments', function (Blueprint $table) {
            if (Schema::hasColumn('stripe_payments', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')
                    ->nullable(false)
                    ->change();
            }

            if (Schema::hasColumn('stripe_payments', 'stripe_invoice_id')) {
                $table->dropUnique(['stripe_invoice_id']);
                $table->dropColumn('stripe_invoice_id');
            }
        });
    }
};
