<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiResponse extends Model
{
    protected $table = 'ai_responses';

    protected $fillable = [
        'user_id',
        'type',
        'favourite_types',
        'response_date',
        'response_json',
        'is_favourite',
    ];

    protected $casts = [
        'response_json' => 'array',
        'response_date' => 'date',
        'is_favourite' => 'boolean',
    ];
}
