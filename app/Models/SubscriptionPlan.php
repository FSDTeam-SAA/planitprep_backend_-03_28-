<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'title',
        'price',
        'duration',
        'price_id',
        'highlight',
        'features',
    ];

    // Automatically cast JSON to array
    protected $casts = [
        'features' => 'array',
        'highlight' => 'boolean',
    ];
}
