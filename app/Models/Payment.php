<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'user_membership_id',
        'user_id',
        'transaction_id',
        'payment_method',
        'price',
        'status',
        'response',
    ];
}
