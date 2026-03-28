<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripePayment extends Model
{

    public function user()
        {
            return $this->belongsTo(\App\Models\User::class);
        }
        

    protected $table = 'stripe_payments';

    protected $fillable = [
        'user_id',
        'stripe_payment_intent_id',
        'stripe_invoice_id',
        'amount',
        'currency',
        'status',
        'type',
        'payment_method',
    ];
}
