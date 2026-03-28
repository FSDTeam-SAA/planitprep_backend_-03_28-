<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatchMealConfig extends Model
{
    use HasFactory;

    protected $table = 'batch_meal_config';

    protected $fillable = [
        'user_id',
        'days_of_cook',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationship: Config belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper: return cooking days as array
    public function getCookingDaysArray()
    {
        return explode(',', $this->days_of_cook);
    }
}