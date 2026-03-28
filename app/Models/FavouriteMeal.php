<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteMeal extends Model
{
    use HasFactory;

    protected $table = 'favourate_meals';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ai_res_id',
        'type',
        'meal_type',
        'date',
        'response_json',
        'is_delete',
        'created_at'
    ];

    protected $casts = [
        'response_json' => 'array',
        'date' => 'date',
        'created_at' => 'datetime'
    ];
}
