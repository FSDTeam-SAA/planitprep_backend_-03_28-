<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    use HasFactory;

    public function meal_plan_items(): HasMany
    {
        return $this->hasMany(MealPlanItem::class, 'meal_plan_id');
    }
}
