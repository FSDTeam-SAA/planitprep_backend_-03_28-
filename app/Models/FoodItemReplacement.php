<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodItemReplacement extends Model
{
    use HasFactory;

    public function replace_item(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class, 'replace_food_item_id');
    }
}
