<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodItem extends Model
{
    use HasFactory;

    public function similar_food_items(): HasMany
    {
        return $this->hasMany(SimilarFoodItem::class, 'food_item_id');
    }

    public function serving_unit()
    {
        return $this->belongsTo(ServingUnit::class, 'unit');
    }
}
