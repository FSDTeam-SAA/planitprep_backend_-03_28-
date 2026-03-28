<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    use HasFactory;

    protected $table = 'recipes';

    protected $fillable = [
        'userid',
        'date',
        'meal_type',
        'preparation_type',
        'type',
        'version',
        'is_active',
        'recipeName',
        'prepTime',
        'calories',
        'isVegetarian',
        'recipePoints',
        'grocery_list',
        'isFavorate',
        'isDeleted',
        'createDate',
        'modifyDate'
    ];

    protected $casts = [
        'date' => 'date',
        'isVegetarian' => 'boolean',
        'isFavorate' => 'boolean',
        'isDeleted' => 'boolean',
        'is_active' => 'boolean',
        'grocery_list' => 'array'
    ];
}