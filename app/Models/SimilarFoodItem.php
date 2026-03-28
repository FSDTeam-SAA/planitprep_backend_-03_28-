<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimilarFoodItem extends Model
{
    public function food_item(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class, 'similar_item_id');
    }
}
