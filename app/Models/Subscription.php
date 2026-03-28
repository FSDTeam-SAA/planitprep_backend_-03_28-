<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'stripe_subscriptions';

    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'cancel_at_period_end',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'is_subscribed', // added new column
    ];

    protected $casts = [
        'cancel_at_period_end' => 'boolean',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'is_subscribed' => 'integer', // cast as integer
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
