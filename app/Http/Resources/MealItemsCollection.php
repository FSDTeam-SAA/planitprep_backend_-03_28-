<?php

namespace App\Http\Resources;

use App\Models\FoodItem;
use App\Models\FoodItemReplacement;
use App\Models\UserMealPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MealItemsCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $food_item = $this->food_item;
        $quantity = $this->quantity;

        // FORMAT(sum(intake_food_items.quantity * (fi.calories/fi.serving_size)),1) as total_calories,FORMAT(sum(intake_food_items.quantity * (fi.total_fat/fi.serving_size)),1) as total_fat, FORMAT(sum(intake_food_items.quantity * (fi.total_carbohydrates/fi.serving_size)),1) as total_carbs,FORMAT(sum(intake_food_items.quantity * (fi.protein/fi.serving_size)),1) as total_protien
        if (Auth::user()) {
            $user = Auth::user();
            $user_id = $user->id;
            $mealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();
            if ($mealPlan) {
                $meal_plan_id = $mealPlan->meal_plan_id;
                $replacement = FoodItemReplacement::where('meal_plan_id', $meal_plan_id)
                    ->where('user_id', $user_id)
                    ->where('food_item_id', $this->food_item->id)
                    ->first();

                if ($replacement) {
                    $food_item = FoodItem::find($replacement->replace_food_item_id);
                    $quantity = $replacement->quantity;
                }
            }
        }

        if ($food_item->image == '') {
            $image = '';
        } else {
            $image = asset(Storage::url('food-items')).'/'.$food_item->image;
        }

        $total_calories = strval(floor(($quantity * ($food_item->calories / $food_item->serving_size)) * 100) / 100);
        $total_fat = strval(floor(($quantity * ($food_item->total_fat / $food_item->serving_size)) * 100) / 100);
        $saturated_fat = strval(floor(($quantity * ($food_item->saturated_fat / $food_item->serving_size)) * 100) / 100);
        $trans_fat = strval(floor(($quantity * ($food_item->trans_fat / $food_item->serving_size)) * 100) / 100);
        $monounsaturated_fat = strval(floor(($quantity * ($food_item->monounsaturated_fat / $food_item->serving_size)) * 100) / 100);
        $polyunsaturated_fat = strval(floor(($quantity * ($food_item->polyunsaturated_fat / $food_item->serving_size)) * 100) / 100);
        $cholesterol = strval(floor(($quantity * ($food_item->cholesterol / $food_item->serving_size)) * 100) / 100);
        $sodium = strval(floor(($quantity * ($food_item->sodium / $food_item->serving_size)) * 100) / 100);
        $total_carbohydrates = strval(floor(($quantity * ($food_item->total_carbohydrates / $food_item->serving_size)) * 100) / 100);
        $dietary_fiber = strval(floor(($quantity * ($food_item->dietary_fiber / $food_item->serving_size)) * 100) / 100);
        $sugars = strval(floor(($quantity * ($food_item->sugars / $food_item->serving_size)) * 100) / 100);
        $added_sugars = strval(floor(($quantity * ($food_item->added_sugars / $food_item->serving_size)) * 100) / 100);
        $protein = strval(floor(($quantity * ($food_item->protein / $food_item->serving_size)) * 100) / 100);
        $vitamin_a = strval(floor(($quantity * ($food_item->vitamin_a / $food_item->serving_size)) * 100) / 100);
        $vitamin_c = strval(floor(($quantity * ($food_item->vitamin_c / $food_item->serving_size)) * 100) / 100);
        $calcium = strval(floor(($quantity * ($food_item->calcium / $food_item->serving_size)) * 100) / 100);
        $iron = strval(floor(($quantity * ($food_item->iron / $food_item->serving_size)) * 100) / 100);
        $potassium = strval(floor(($quantity * ($food_item->potassium / $food_item->serving_size)) * 100) / 100);
        $vitamin_d = strval(floor(($quantity * ($food_item->vitamin_d / $food_item->serving_size)) * 100) / 100);
        $vitamin_b6 = strval(floor(($quantity * ($food_item->vitamin_b6 / $food_item->serving_size)) * 100) / 100);
        $vitamin_b12 = strval(floor(($quantity * ($food_item->vitamin_b12 / $food_item->serving_size)) * 100) / 100);
        $magnesium = strval(floor(($quantity * ($food_item->magnesium / $food_item->serving_size)) * 100) / 100);
        $glycemic_index = strval(floor(($quantity * ($food_item->glycemic_index / $food_item->serving_size)) * 100) / 100);

        return [
            'id' => $food_item->id,
            'name' => $food_item->name,
            'food_group_id' => $food_item->food_group_id,
            'serving_size' => $quantity,
            'serving_unit' => $food_item->serving_unit,
            'calories' => $total_calories,
            'total_fat' => $total_fat,
            'saturated_fat' => $saturated_fat,
            'trans_fat' => $trans_fat,
            'monounsaturated_fat' => $monounsaturated_fat,
            'polyunsaturated_fat' => $polyunsaturated_fat,
            'cholesterol' => $cholesterol,
            'sodium' => $sodium,
            'total_carbohydrates' => $total_carbohydrates,
            'dietary_fiber' => $dietary_fiber,
            'sugars' => $sugars,
            'added_sugars' => $added_sugars,
            'protein' => $protein,
            'vitamin_a' => $vitamin_a,
            'vitamin_c' => $vitamin_c,
            'calcium' => $calcium,
            'iron' => $iron,
            'potassium' => $potassium,
            'vitamin_d' => $vitamin_d,
            'vitamin_b6' => $vitamin_b6,
            'vitamin_b12' => $vitamin_b12,
            'magnesium' => $magnesium,
            'glycemic_index' => $glycemic_index,
            'comments' => $food_item->comments,
            'active' => $food_item->active,
            'type' => $food_item->type,
            'image' => $image,
        ];
    }
}
