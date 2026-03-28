<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class IntakeMealItemsCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->food_item->image == '') {
            $image = '';
        } else {
            $image = asset(Storage::url('food-items')).'/'.$this->food_item->image;
        }

        return [
            'id' => $this->food_item->id,
            'name' => $this->food_item->name,
            'food_group_id' => $this->food_item->food_group_id,
            'serving_size' => $this->food_item->serving_size,
            'serving_unit' => $this->food_item->serving_unit,
            'calories' => $this->food_item->calories,
            'total_fat' => $this->food_item->total_fat,
            'saturated_fat' => $this->food_item->saturated_fat,
            'trans_fat' => $this->food_item->trans_fat,
            'monounsaturated_fat' => $this->food_item->monounsaturated_fat,
            'polyunsaturated_fat' => $this->food_item->polyunsaturated_fat,
            'cholesterol' => $this->food_item->cholesterol,
            'sodium' => $this->food_item->sodium,
            'total_carbohydrates' => $this->food_item->total_carbohydrates,
            'dietary_fiber' => $this->food_item->dietary_fiber,
            'sugars' => $this->food_item->sugars,
            'added_sugars' => $this->food_item->added_sugars,
            'protein' => $this->food_item->protein,
            'vitamin_a' => $this->food_item->vitamin_a,
            'vitamin_c' => $this->food_item->vitamin_c,
            'calcium' => $this->food_item->calcium,
            'iron' => $this->food_item->iron,
            'potassium' => $this->food_item->potassium,
            'vitamin_d' => $this->food_item->vitamin_d,
            'vitamin_b6' => $this->food_item->vitamin_b6,
            'vitamin_b12' => $this->food_item->vitamin_b12,
            'magnesium' => $this->food_item->magnesium,
            'glycemic_index' => $this->food_item->glycemic_index,
            'comments' => $this->food_item->comments,
            'active' => $this->food_item->active,
            'image' => $image,
        ];
    }
}
