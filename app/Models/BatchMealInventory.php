<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatchMealInventory extends Model
{
    use HasFactory;

    protected $table = 'batch_meal_inventory';

    protected $fillable = [
        'user_id',
        'recipe_id',
        'total_portions',
        'used_portions',
        'remaining_portions',
        'calories_per_portion',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationship: Inventory belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: Inventory belongs to Recipe
    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }

    // Helper: consume portion
    public function consumeOnePortion()
    {
        if ($this->remaining_portions > 0) {
            $this->used_portions += 1;
            $this->remaining_portions -= 1;

            if ($this->remaining_portions == 0) {
                $this->is_active = false;
            }

            $this->save();
        }
    }
}