<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meal extends Model
{
    use HasFactory;

    public function meal_plan_items(): HasMany
    {
        return $this->hasMany(MealPlanItem::class, 'meal_id');
    }

    public function intake_food_items(): HasMany
    {
        return $this->hasMany(IntakeFoodItem::class, 'meal_id');
    }
}
