<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class IntakeMealsCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->image == '') {
            $image = '';
        } else {
            $image = asset(Storage::url('meals')).'/'.$this->image;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $image,
            'meal_plan_items' => MealItemsCollection::collection($this->intake_food_items),
        ];
    }
}
