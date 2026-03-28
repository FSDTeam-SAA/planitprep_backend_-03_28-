<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntakeFoodItem extends Model
{
    use HasFactory;

    public function food_item(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class, 'food_id');
    }
}
