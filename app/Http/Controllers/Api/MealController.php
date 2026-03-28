<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use App\Models\FoodItem;
use App\Models\MealPlan;
use App\Models\UserMealPlan;
use App\Models\MealPlanItem;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\UserPreference;
use App\Models\User;
use App\Models\Goal;
use App\Models\FitnessLevel;
use App\Models\ExercisePlanDay;
use App\Models\UserGoal;
use App\Models\UserMedicalIssue;
use App\Models\UserAllergies;
use App\Models\UserDietRegime;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\IntakeMealsCollection;
use App\Http\Resources\MealsCollection;
use App\Jobs\UserDietPlan;
use App\Models\IntakeFoodItem;
use App\Models\Membership;
use App\Models\UserMembership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MealController extends Controller
{
    public function saveUserDietPlan($request)
    {
        $validator = Validator::make($request, [
            'data' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $this->errorStr($validator->errors()->all()),
            ]);
        }
        // $user = Auth::user();
        $user_id = $request['user_id'];
        if (isset($request->data['weekly_diet_chart'])) {
            if (isset($user->goal)) {
                $userMealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();
                if($userMealPlan){
                    $mealPlanId=$userMealPlan->meal_plan_id;
                    $planDay = MealPlanItem::where('meal_plan_id', $userMealPlan->meal_plan_id)->max('day');
                    if($planDay==7){
                        $mealPlanId = MealPlan::insertGetId(['name' => $user->goal->name, 'created_at' => date("Y-m-d h:i:s"), 'updated_at' => date("Y-m-d h:i:s")]);
                        UserMealPlan::insertGetId(['user_id' => $user_id, 'meal_plan_id' => $mealPlanId, 'active' => 1, 'created_at' => date("Y-m-d h:i:s"), 'updated_at' => date("Y-m-d h:i:s")]);
                    }
                }else{
                    $mealPlanId = MealPlan::insertGetId(['name' => $user->goal->name, 'created_at' => date("Y-m-d h:i:s"), 'updated_at' => date("Y-m-d h:i:s")]);
                    UserMealPlan::insertGetId(['user_id' => $user_id, 'meal_plan_id' => $mealPlanId, 'active' => 1, 'created_at' => date("Y-m-d h:i:s"), 'updated_at' => date("Y-m-d h:i:s")]);
                }

               if($mealPlanId>0){
                    foreach ($request->data['weekly_diet_chart'] as $key1 => $value) {
                        $day = (int) preg_replace('/[^0-9]/', '', $value['day']);
                        if ($day > 0) {
                            if (isset($value['meals']) && count($value['meals']) > 0) {
                                foreach ($value['meals'] as $key2 => $meal) {
                                    $mealSql = Meal::select('id')->where(\DB::raw("LOWER(name)"), strtolower($meal['meal_time']))->first();
                                    if ($mealSql) {
                                        if (isset($meal['food_items']) && count($meal['food_items']) > 0) {
                                            foreach ($meal['food_items'] as $key3 => $fItem) {
                                                $foodItemsData = [];
                                                $foodItemsData['name'] = $fItem['name'];
                                                $foodItemsData['serving_size'] = (int) preg_replace('/[^0-9]/', '', $fItem['quantity']);
                                                $foodItemsData['serving_unit'] = preg_replace('/[^a-zA-Z]/', '', $fItem['quantity']);
                                                if (isset($fItem['data'])) {
                                                    if (isset($fItem['data']['cal'])) {
                                                        $foodItemsData['calories'] = (float) $fItem['data']['cal'];
                                                    }
                                                    if (isset($fItem['data']['pro'])) {
                                                        $foodItemsData['protein'] = (float) $fItem['data']['pro'];
                                                    }
                                                    if (isset($fItem['data']['car'])) {
                                                        $foodItemsData['total_carbohydrates'] = (float) $fItem['data']['car'];
                                                    }
                                                    if (isset($fItem['data']['fat'])) {
                                                        $foodItemsData['total_fat'] = (float) $fItem['data']['fat'];
                                                    }

                                                    if (isset($fItem['data']['sat'])) {
                                                        $foodItemsData['saturated_fat'] = (float) $fItem['data']['sat'];
                                                    }
                                                    if (isset($fItem['data']['trs'])) {
                                                        $foodItemsData['trans_fat'] = (float) $fItem['data']['trs'];
                                                    }
                                                    if (isset($fItem['data']['mno'])) {
                                                        $foodItemsData['monounsaturated_fat'] = (float) $fItem['data']['mno'];
                                                    }
                                                    if (isset($fItem['data']['ply'])) {
                                                        $foodItemsData['polyunsaturated_fat'] = (float) $fItem['data']['ply'];
                                                    }
                                                    if (isset($fItem['data']['chl'])) {
                                                        $foodItemsData['cholesterol'] = (float) $fItem['data']['chl'];
                                                    }
                                                    if (isset($fItem['data']['sod'])) {
                                                        $foodItemsData['sodium'] = (float) $fItem['data']['sod'];
                                                    }
                                                    if (isset($fItem['data']['fbr'])) {
                                                        $foodItemsData['dietary_fiber'] = (float) $fItem['data']['fbr'];
                                                    }
                                                    if (isset($fItem['data']['sgr'])) {
                                                        $foodItemsData['sugars'] = (float) $fItem['data']['sgr'];
                                                    }
                                                    if (isset($fItem['data']['ads'])) {
                                                        $foodItemsData['added_sugars'] = (float) $fItem['data']['ads'];
                                                    }
                                                    if (isset($fItem['data']['a'])) {
                                                        $foodItemsData['vitamin_a'] = (float) $fItem['data']['a'];
                                                    }
                                                    if (isset($fItem['data']['c'])) {
                                                        $foodItemsData['vitamin_c'] = (float) $fItem['data']['c'];
                                                    }
                                                    if (isset($fItem['data']['ca'])) {
                                                        $foodItemsData['calcium'] = (float) $fItem['data']['ca'];
                                                    }
                                                    if (isset($fItem['data']['irn'])) {
                                                        $foodItemsData['iron'] = (float) $fItem['data']['irn'];
                                                    }
                                                    if (isset($fItem['data']['pot'])) {
                                                        $foodItemsData['potassium'] = (float) $fItem['data']['pot'];
                                                    }
                                                    if (isset($fItem['data']['d'])) {
                                                        $foodItemsData['vitamin_d'] = (float) $fItem['data']['d'];
                                                    }
                                                    if (isset($fItem['data']['b6'])) {
                                                        $foodItemsData['vitamin_b6'] = (float) $fItem['data']['b6'];
                                                    }
                                                    if (isset($fItem['data']['b12'])) {
                                                        $foodItemsData['vitamin_b12'] = (float) $fItem['data']['b12'];
                                                    }
                                                    if (isset($fItem['data']['mag'])) {
                                                        $foodItemsData['magnesium'] = (float) $fItem['data']['mag'];
                                                    }
                                                    if (isset($fItem['data']['gix'])) {
                                                        $foodItemsData['glycemic_index'] = (float) $fItem['data']['gix'];
                                                    }
                                                    if (isset($fItem['data']['com'])) {
                                                        $foodItemsData['comments'] = $fItem['data']['com'];
                                                    }
                                                    if (isset($fItem['data']['typ'])) {
                                                        $foodItemsData['type'] = $fItem['data']['typ'];
                                                    }
                                                    $foodItemsData['created_at'] = date("Y-m-d h:i:s");
                                                    $foodItemsData['updated_at'] = date("Y-m-d h:i:s");
                                                }

                                                $foodItemsSql = FoodItem::select('id')->where(\DB::raw("LOWER(name)"), strtolower($fItem['name']))->first();
                                                if (!$foodItemsSql) {
                                                    $foodItemId = FoodItem::insertGetId($foodItemsData);
                                                } else {
                                                    $foodItemId = $foodItemsSql->id;
                                                }
                                                $mealPlanItemData = [];
                                                $mealPlanItemData['meal_id'] = $mealSql->id;
                                                $mealPlanItemData['meal_plan_id'] = $mealPlanId;
                                                $mealPlanItemData['food_item_id'] = $foodItemId;
                                                $mealPlanItemData['quantity'] = (int) preg_replace('/[^0-9]/', '', $fItem['quantity']);
                                                $mealPlanItemData['day'] = $day;
                                                $mealPlanItemData['created_at'] = date("Y-m-d h:i:s");
                                                $mealPlanItemData['updated_at'] = date("Y-m-d h:i:s");
                                                MealPlanItem::insertGetId($mealPlanItemData);
                                            }
                                        } else {
                                            return response()->json([
                                                'status' => false,
                                                'msg' => 'Food items are missing!',
                                            ]);
                                        }
                                    }
                                }
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'msg' => 'Meals are missing!',
                                ]);
                            }
                        } else {
                            return response()->json([
                                'status' => false,
                                'msg' => 'Invalid days!',
                            ]);
                        }
                    }
                    return response()->json([
                        'status' => true,
                        'msg' => 'Weekly diet plan added successfully!',
                    ]);
                }else{
                    return response()->json([
                        'status' => true,
                        'msg' => 'No meal plan found!',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Your goal is not set!',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Weekly diet chart should be of 7 days!',
            ]);
        }
    }

    public function fetchMeals(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'day' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            }
            $user = Auth::user();
            $user_id = $user->id;
            $day = $request->day;

            $mealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();

            $meals = Meal::whereHas('meal_plan_items', function ($q) use ($mealPlan) {
                $q->where('meal_plan_id', $mealPlan->meal_plan_id);
            })->with(['meal_plan_items' => function ($q) use ($mealPlan, $day) {
                $q->where('day', $day)
                    ->whereHas('meal_plan') // Ensures that meal_plan is not null
                    ->with(['food_item'])
                    ->where('meal_plan_id', $mealPlan->meal_plan_id);
            }])->get();
          
          $meals=$meals->each(function ($meal) {
              $meal->setRelation('meal_plan_items', $meal->meal_plan_items->unique('food_item_id')->values());
          });
            return response()->json(['status' => true, 'data' => MealsCollection::collection($meals)]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        }
    }

    // Helper function to get meal time based on meal_id
    private function getMealTime($mealId)
    {
        $mealTimes = [
            1 => 'Breakfast',
            2 => 'Morning Snack',
            3 => 'Lunch',
            4 => 'Evening Snack',
            5 => 'Dinner',
        ];

        return $mealTimes[$mealId] ?? 'Unknown';
    }

    function getFoodItems(Request $request)
    {
        $url = asset(Storage::url('food-items'));
        $foodItems = FoodItem::select(\DB::raw("*,concat('" . $url . "','/',image) as image"))->where("active", 1);

        if (isset($request->search) && $request->search != "") {

            $foodItems = $foodItems->where(\DB::raw("LOWER(name)"), 'like', "%{$request->search}%");
        }

        $foodItems = $foodItems->paginate($request->page_size == null ? 20 : $request->page_size);
        $total = $foodItems->total();

        if (count($foodItems) > 0) {
            return response()->json(['status' => true, 'data' => $foodItems->items(), 'total' => $total]);
        } else {
            return response()->json(['status' => false, 'msg' => "No items found"]);
        }
    }

    public function errorStr($errors)
    {
        $str = '';
        $errorsCount = count($errors);

        for ($i = 0; $i < $errorsCount; $i++) {
            if ($i > 0) {
                $str .= '<br>';
            }
            $str .= $errors[$i];
        }
        return $str;
    }

    public function saveIntakeFoodItem(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'food_id' => 'required',
                'meal_id' => 'required',
                'quantity' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            }

            $intakeFood = IntakeFoodItem::where('food_id', $request->food_id)
                ->where('meal_id', $request->meal_id)
                ->where('user_id', Auth::user()->id)
                ->where('date', isset($request->date) ? $request->date : date('Y-m-d'))
                ->first();

            if (!$intakeFood) {
                $intakeFood = new IntakeFoodItem();
            }
            $intakeFood->food_id = $request->food_id;
            $intakeFood->meal_id = $request->meal_id;
            $intakeFood->user_id = Auth::user()->id;
            $intakeFood->quantity = $request->quantity;
            $intakeFood->date = isset($request->date) ? $request->date : date('Y-m-d');
            $intakeFood->save();

            return response()->json(['status' => true, 'msg' => 'Record Saved!', 'id' => $intakeFood->id]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        }
    }

    public function updateWaterIntake(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            }

            $waterFoodId = FoodItem::where(\DB::raw('LOWER(name)'), 'water')->first();
            if ($waterFoodId) {
                $intakeFood = IntakeFoodItem::where('food_id', $waterFoodId->id)
                    ->where('user_id', Auth::user()->id)
                    ->where('date', isset($request->date) ? $request->date : date('Y-m-d'))
                    ->first();

                if (!$intakeFood) {
                    $intakeFood = new IntakeFoodItem();
                }

                $intakeFood->food_id = $waterFoodId->id;
                $intakeFood->meal_id = 0;
                $intakeFood->user_id = Auth::user()->id;
                $intakeFood->quantity = $request->quantity;
                $intakeFood->date = isset($request->date) ? $request->date : date('Y-m-d');
                $intakeFood->save();

                return response()->json(['status' => true, 'msg' => 'Record Saved!', 'quantity' => $intakeFood->quantity]);
            } else {
                return response()->json(['status' => false, 'msg' => 'No water record exists!']);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        }
    }

    public function fetchMyMeals(Request $request)
    {
        if (Auth::user()) {
            $date = isset($request->date) ? $request->date : date('Y-m-d');
            $user_id = Auth::user()->id;
            $meals = Meal::whereHas('intake_food_items', function ($q) use ($user_id, $date) {
                $q->where('user_id', $user_id);
                $q->where('date', $date);
            })
            ->with(['intake_food_items' => function ($q) use ($user_id, $date) {
                $q->where('user_id', $user_id);
                $q->where('date', $date);
                $q->with(['food_item']);
            }])
            ->get();
            return response()->json(['status' => true, 'data' => IntakeMealsCollection::collection($meals)]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        }
    }

    public function fetchMacroTrackerData(Request $request)
    {
        if (Auth::user()) {

            $validator = Validator::make($request->all(), [
                'start_date' => 'required',
                'end_date' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            }

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $user_id = Auth::user()->id;
            $waterFoodId = FoodItem::where(\DB::raw('LOWER(name)'), 'water')->first();
         $macroTrackerData = IntakeFoodItem::select(\DB::raw('
                intake_food_items.date, 
                ROUND(SUM(intake_food_items.quantity * (fi.calories / fi.serving_size)), 1) as calories,
                ROUND(SUM(intake_food_items.quantity * (fi.total_fat / fi.serving_size)), 1) as fat, 
                ROUND(SUM(intake_food_items.quantity * (fi.total_carbohydrates / fi.serving_size)), 1) as carb,
                ROUND(SUM(intake_food_items.quantity * (fi.dietary_fiber / fi.serving_size)), 1) as fiber,
                ROUND(SUM(intake_food_items.quantity * (fi.protein / fi.serving_size)), 1) as protein,
                ROUND(SUM(CASE WHEN fi2.id IS NOT NULL THEN intake_food_items.quantity ELSE 0 END), 1) as water
            '))
            ->join("food_items as fi", "fi.id", "intake_food_items.food_id")
            ->leftJoin("food_items as fi2", function($q) use($waterFoodId) {
                $q->on("fi2.id", "intake_food_items.food_id")
                  ->where('food_id', $waterFoodId->id);
            })
            ->where("user_id", $user_id)
            ->whereBetween(\DB::raw('DATE(date)'), [$start_date, $end_date])
            ->groupBy("intake_food_items.date")
            ->orderBy("intake_food_items.date")
            ->get();

            $macroTrackerData = $macroTrackerData->map(function ($item) {
                $item->calories = (float) $item->calories;
                $item->fat      = (float) $item->fat;
                $item->carb     = (float) $item->carb;
                $item->fiber    = (float) $item->fiber;
                $item->protein  = (float) $item->protein;
                $item->water    = (float) $item->water;
                return $item;
            });
            return response()->json(['status' => true, 'data' => $macroTrackerData]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        }
    }

    public function getWeeklyDietPlanFromChatGPT(Request $request)
    {
        $memberships=UserMembership::get();
     
        foreach($memberships as $membership){
            $userId = (isset($membership->user_id) && $membership->user_id > 0) ? $membership->user_id : 0;
            UserDietPlan::dispatch($userId);
        }     
        
        echo "success";
        exit;
    }

    // public function getDayWiseDietPlanFromChatGPT($messages)
    // {
    //     $apiKey = env('OPENAI_API_KEY'); // Ensure this matches the key in your .env file

    //     //     Example structure as per table fields
    //     //     $array = [
    //     //     "cal" => 300,             // calories
    //     //     "fat" => 16.00,           // total_fat
    //     //     "sat" => 4.0,             // saturated_fat
    //     //     "trs" => 0.0,             // trans_fat
    //     //     "mno" => 5.0,             // monounsaturated_fat
    //     //     "ply" => 3.0,             // polyunsaturated_fat
    //     //     "chl" => 90.00,           // cholesterol
    //     //     "sod" => 80.00,           // sodium
    //     //     "car" => 0.00,            // total_carbohydrates
    //     //     "fbr" => 0.00,            // dietary_fiber
    //     //     "sgr" => 0.00,            // sugars
    //     //     "ads" => 0.00,            // added_sugars
    //     //     "pro" => 35.00,           // protein
    //     //     "a" => 0.00,              // vitamin_a
    //     //     "c" => 0.00,              // vitamin_c
    //     //     "ca" => 20.00,            // calcium
    //     //     "irn" => 1.5,             // iron
    //     //     "pot" => 230.00,          // potassium
    //     //     "d" => 0.00,              // vitamin_d
    //     //     "b6" => 0.7,              // vitamin_b6
    //     //     "b12" => 1.0,             // vitamin_b12
    //     //     "mag" => 30.00,           // magnesium
    //     //     "gix" => 0.00,            // glycemic_index
    //     //     "com" => "Rich in protein", // comments
    //     //     "typ" => "NV"             // type
    //     // ];



    //     $expectedStructure = ["diet_chart" => [["day" => "Day 1", "meals" => [["meal_time" => "Breakfast", "food_items" => [["name" => "Oats", "quantity" => "1 cup", "data" => ["cal" => 150, "fat" => 3.00, "sat" => 0.5, "trs" => 0.0, "mno" => 0.2, "ply" => 0.1, "chl" => 0.00, "sod" => 2.00, "car" => 27.00, "fbr" => 3.00, "sgr" => 1.00, "ads" => 0.00, "pro" => 5.00, "a" => 0.00, "c" => 0.00, "ca" => 20.00, "irn" => 1.5, "pot" => 150.00, "d" => 0.00, "b6" => 0.2, "b12" => 0.0, "mag" => 40.00, "gix" => 55.00, "com" => "Rich in complex carbs and dietary fiber", "typ" => "VE"]], ["name" => "Skimmed Milk", "quantity" => "1 cup", "data" => ["cal" => 90, "fat" => 0.00, "sat" => 0.0, "trs" => 0.0, "mno" => 0.0, "ply" => 0.0, "chl" => 5.00, "sod" => 100.00, "car" => 12.00, "fbr" => 0.00, "sgr" => 12.00, "ads" => 0.00, "pro" => 8.00, "a" => 500.00, "c" => 0.00, "ca" => 300.00, "irn" => 0.0, "pot" => 380.00, "d" => 2.00, "b6" => 0.1, "b12" => 1.0, "mag" => 30.00, "gix" => 32.00, "com" => "High in calcium and low in fat", "typ" => "VG"]], ["name" => "Almonds", "quantity" => "10 pieces", "data" => ["cal" => 70, "fat" => 6.00, "sat" => 0.5, "trs" => 0.0, "mno" => 3.5, "ply" => 1.0, "chl" => 0.00, "sod" => 0.00, "car" => 2.00, "fbr" => 1.00, "sgr" => 0.5, "ads" => 0.00, "pro" => 3.00, "a" => 0.00, "c" => 0.00, "ca" => 30.00, "irn" => 0.5, "pot" => 100.00, "d" => 0.00, "b6" => 0.1, "b12" => 0.0, "mag" => 60.00, "gix" => 15.00, "com" => "Rich in healthy fats and fiber", "typ" => "VG"]], ["name" => "Boiled Eggs", "quantity" => "2", "data" => ["cal" => 140, "fat" => 10.00, "sat" => 3.0, "trs" => 0.0, "mno" => 4.0, "ply" => 1.0, "chl" => 210.00, "sod" => 70.00, "car" => 1.00, "fbr" => 0.00, "sgr" => 0.5, "ads" => 0.00, "pro" => 12.00, "a" => 270.00, "c" => 0.00, "ca" => 50.00, "irn" => 1.0, "pot" => 60.00, "d" => 1.00, "b6" => 0.1, "b12" => 1.0, "mag" => 10.00, "gix" => 0.00, "com" => "Excellent source of protein", "typ" => "EG"]]]], ["meal_time" => "Morning Snack", "food_items" => [["name" => "Apple", "quantity" => "1 medium", "data" => ["cal" => 95, "fat" => 0.3, "sat" => 0.1, "trs" => 0.0, "mno" => 0.0, "ply" => 0.0, "chl" => 0.0, "sod" => 1.0, "car" => 25.0, "fbr" => 4.0, "sgr" => 19.0, "ads" => 0.0, "pro" => 0.5, "a" => 54.0, "c" => 8.4, "ca" => 6.0, "irn" => 0.1, "pot" => 195.0, "d" => 0.0, "b6" => 0.1, "b12" => 0.0, "mag" => 5.0, "gix" => 36.0, "com" => "Good source of fiber and natural sugars", "typ" => "VE"]], ["name" => "Whey Protein", "quantity" => "1 scoop", "data" => ["cal" => 120, "fat" => 1.0, "sat" => 0.5, "trs" => 0.0, "mno" => 0.2, "ply" => 0.1, "chl" => 30.0, "sod" => 50.0, "car" => 3.0, "fbr" => 0.0, "sgr" => 1.5, "ads" => 0.0, "pro" => 24.0, "a" => 0.0, "c" => 0.0, "ca" => 150.0, "irn" => 0.5, "pot" => 120.0, "d" => 0.0, "b6" => 0.5, "b12" => 0.4, "mag" => 20.0, "gix" => 15.0, "com" => "High in protein and low in fats", "typ" => "VG"]]]], ["meal_time" => "Lunch", "food_items" => [["name" => "Grilled Chicken", "quantity" => "150g", "data" => ["cal" => 300, "fat" => 16.0, "sat" => 4.0, "trs" => 0.0, "mno" => 5.0, "ply" => 3.0, "chl" => 90.0, "sod" => 80.0, "car" => 0.0, "fbr" => 0.0, "sgr" => 0.0, "ads" => 0.0, "pro" => 35.0, "a" => 0.0, "c" => 0.0, "ca" => 20.0, "irn" => 1.5, "pot" => 230.0, "d" => 0.0, "b6" => 0.7, "b12" => 1.0, "mag" => 30.0, "gix" => 0.0, "com" => "Rich in protein", "typ" => "NV"]], ["name" => "Brown Rice", "quantity" => "1 cup", "data" => ["cal" => 215, "fat" => 2.0, "sat" => 0.5, "trs" => 0.0, "mno" => 0.5, "ply" => 0.5, "chl" => 0.0, "sod" => 10.0, "car" => 45.0, "fbr" => 8.0, "sgr" => 0.5, "ads" => 0.0, "pro" => 5.0, "a" => 0.0, "c" => 0.0, "ca" => 10.0, "irn" => 0.8, "pot" => 150.0, "d" => 0.0, "b6" => 0.2, "b12" => 0.0, "mag" => 85.0, "gix" => 50.0, "com" => "Complex carbs source", "typ" => "VE"]], ["name" => "Steamed Vegetables", "quantity" => "1 cup", "data" => ["cal" => 50, "fat" => 0.0, "sat" => 0.0, "trs" => 0.0, "mno" => 0.0, "ply" => 0.0, "chl" => 0.0, "sod" => 40.0, "car" => 10.0, "fbr" => 3.0, "sgr" => 2.0, "ads" => 0.0, "pro" => 2.0, "a" => 500.0, "c" => 10.0, "ca" => 20.0, "irn" => 0.5, "pot" => 180.0, "d" => 0.0, "b6" => 0.1, "b12" => 0.0, "mag" => 10.0, "gix" => 15.0, "com" => "Low calorie, high in fiber", "typ" => "VG"]]]], ["meal_time" => "Evening Snack", "food_items" => [["name" => "Paneer Tikka", "quantity" => "100g", "data" => ["cal" => 300, "fat" => 22.0, "sat" => 12.0, "trs" => 0.0, "mno" => 5.0, "ply" => 2.0, "chl" => 40.0, "sod" => 60.0, "car" => 8.0, "fbr" => 0.0, "sgr" => 2.0, "ads" => 0.0, "pro" => 15.0, "a" => 100.0, "c" => 0.0, "ca" => 300.0, "irn" => 0.8, "pot" => 100.0, "d" => 0.0, "b6" => 0.3, "b12" => 0.5, "mag" => 40.0, "gix" => 30.0, "com" => "Good protein source, moderate fat", "typ" => "VG"]]]], ["meal_time" => "Dinner", "food_items" => [["name" => "Fish Curry", "quantity" => "150g", "data" => ["cal" => 250, "fat" => 12.0, "sat" => 3.0, "trs" => 0.0, "mno" => 5.0, "ply" => 2.0, "chl" => 60.0, "sod" => 150.0, "car" => 5.0, "fbr" => 1.0, "sgr" => 1.0, "ads" => 0.0, "pro" => 28.0, "a" => 200.0, "c" => 0.0, "ca" => 30.0, "irn" => 2.0, "pot" => 250.0, "d" => 0.0, "b6" => 0.5, "b12" => 2.5, "mag" => 30.0, "gix" => 0.0, "com" => "Rich in protein and omega-3", "typ" => "NV"]], ["name" => "Quinoa", "quantity" => "1 cup", "data" => ["cal" => 222, "fat" => 4.0, "sat" => 0.4, "trs" => 0.0, "mno" => 1.5, "ply" => 0.5, "chl" => 0.0, "sod" => 13.0, "car" => 39.0, "fbr" => 5.0, "sgr" => 0.0, "ads" => 0.0, "pro" => 4.0, "a" => 0.0, "c" => 0.0, "ca" => 17.0, "irn" => 1.5, "pot" => 172.0, "d" => 0.0, "b6" => 0.1, "b12" => 0.0, "mag" => 59.0, "gix" => 53.0, "com" => "Complete protein, low GI", "typ" => "V"]]]]]]]];


    //     // Define your desired structured response
    //     $desiredResponseStructure = [
    //         "status" => "success",
    //         "message" => "Response retrieved successfully",
    //         "data" => $expectedStructure,
    //         "timestamp" => now()->toDateTimeString()
    //     ];

    //     // Create a system message to guide the model
    //     $systemMessage = "Please generate a complete 1-day diet chart in the following JSON format:\n" . json_encode($desiredResponseStructure, JSON_PRETTY_PRINT);
    //     // Prepare the user input message
    //     $userInputMessage = $messages;
    //     // dd($userInputMessage);
    //     // dd($userInputMessage);
    //     // Call the OpenAI API
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . $apiKey,
    //     ])->post('https://api.openai.com/v1/chat/completions', [
    //         'model' => 'gpt-3.5-turbo',
    //         // 'max_tokens'    => 2500,
    //         'messages' => [
    //             ['role' => 'system', 'content' => $systemMessage],
    //             ['role' => 'user', 'content' => $userInputMessage],
    //         ]
    //     ]);
    //     //dd($response->successful());
    //     // Handle the API response
    //     if ($response->successful()) {
    //         $data = $response->json();
    //         // dd($data['choices'][0]['message']);
    //         $content = $data['choices'][0]['message']['content'];
    //         // dd($content);
    //         // Attempt to decode the content to ensure it's in the correct format
    //         $decodedContent = json_decode($content, true);
    //         // dd($decodedContent);
    //         // If decoding fails, revert to the desired structure with an error message
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             $decodedContent = [
    //                 "status" => "error",
    //                 "message" => "Invalid response format",
    //                 "data" => null,
    //                 "timestamp" => now()->toDateTimeString()
    //             ];
    //             return response()->json($decodedContent);
    //         } else {
    //             //dd($decodedContent);
    //             // Populate the data if the response is valid
    //             $desiredResponseStructure['data'] = $decodedContent['data'] ?? [];
    //         }
    //         //dd($desiredResponseStructure);
    //         // Return the structured response
    //         return response()->json($desiredResponseStructure);
    //     } else {
    //         // Handle API request failure
    //         $error = $response->json();
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $error['error']['message'],
    //             'data' => null,
    //             'timestamp' => now()->toDateTimeString()
    //         ]);
    //     }
    // }
}
