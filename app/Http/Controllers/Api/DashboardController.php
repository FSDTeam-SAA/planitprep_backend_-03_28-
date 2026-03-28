<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IntakeMealsCollection;
// use App\Models\MealPlan;
use App\Models\FoodItem;
use App\Models\IntakeFoodItem;
use App\Models\Meal;
use App\Models\UserGoal;
use App\Models\UserMealPlan;
use App\Models\UserMembership;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function fetchDashboardData(Request $request)
    {

        if (Auth::user()) {

            $user = Auth::user();
            $user_id = $user->id;

            $data = [
                'today_macros' => [
                    'carb' => $user->daily_carbs_intake,
                    'fat' => $user->daily_fats_intake,
                    'protein' => $user->daily_protein_intake,
                    'fiber' => $user->daily_fiber_intake,
                    'calories' => $user->daily_calorie_intake,
                    'water' => $user->daily_water,
                ],
                'today_macros_consumed' => [
                    'carb' => '0.0',
                    'fat' => '0.0',
                    'protein' => '0.0',
                    'fiber' => '0.0',
                    'calories' => '0.0',
                    'water' => '0.0',
                ],
                'weekly_macros_consumed' => null,
                'meal_macros_consumed' => null,
                'my_current_weight' => $user->weight,
                'my_target_weight' => '0.0',
                'initial_weight' => '0.0',
                'target_date' => '',
            ];

            $myTargetWeight = UserGoal::where('user_id', $user_id)->orderBy('id', 'desc')->first();

            if ($myTargetWeight) {
                $data['my_target_weight'] = $myTargetWeight->target_value;
                $data['initial_weight'] = $myTargetWeight->current_value;
                $data['target_date'] = date('d M y', strtotime($myTargetWeight->end_date));
            }

            $date = isset($request->date) ? $request->date : date('Y-m-d');
            $day = date('w', strtotime($date));
            $day = ($day == 0) ? 7 : $day;
            $latestMealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();
            // dd($latestMealPlan->meal_plan_id);
            if ($latestMealPlan) {
                // $dayWiseMacroQuery = \DB::table(\DB::raw("meal_plan_items as mpi"))->select(\DB::raw('sum(mpi.quantity * (fi.calories/fi.serving_size)) as total_calories,sum(mpi.quantity * (fi.total_carbohydrates/fi.serving_size)) as total_carbs,sum(mpi.quantity * (fi.total_fat/fi.serving_size)) as total_fat,sum(mpi.quantity * (fi.protein/fi.serving_size)) as total_protein,sum(mpi.quantity * (fi.dietary_fiber/fi.serving_size)) as total_fiber'))
                //                     ->join("food_items as fi","fi.id","mpi.food_item_id")
                //                     ->where("mpi.meal_plan_id",$latestMealPlan->meal_plan_id)
                //                     ->where("mpi.day",$day)
                //   					->groupBy('mpi.meal_id')
                //                     ->first();

                $sub = \DB::table('meal_plan_items as mpi')
                    ->select(
                        'mpi.id',
                        'mpi.meal_id',
                        'mpi.food_item_id',
                        'mpi.quantity',
                        'fi.calories',
                        'fi.serving_size',
                        'fi.total_carbohydrates',
                        'fi.total_fat',
                        'fi.protein',
                        'fi.dietary_fiber',
                        \DB::raw('ROW_NUMBER() OVER (PARTITION BY mpi.meal_id, mpi.food_item_id ORDER BY mpi.id ASC) as row_num')
                    )
                    ->join('food_items as fi', 'fi.id', '=', 'mpi.food_item_id')
                    ->where('mpi.meal_plan_id', $latestMealPlan->meal_plan_id)
                    ->where('mpi.day', $day);

                $dayWiseMacroQuery = \DB::table(\DB::raw("({$sub->toSql()}) as sub"))
                    ->mergeBindings($sub)
                    ->select(
                        \DB::raw('SUM(quantity * (calories / serving_size)) AS total_calories'),
                        \DB::raw('SUM(quantity * (total_carbohydrates / serving_size)) AS total_carbs'),
                        \DB::raw('SUM(quantity * (total_fat / serving_size)) AS total_fat'),
                        \DB::raw('SUM(quantity * (protein / serving_size)) AS total_protein'),
                        \DB::raw('SUM(quantity * (dietary_fiber / serving_size)) AS total_fiber')
                    )
                    ->where('row_num', 1)
                    ->first();

                $data['today_macros']['carb'] = $dayWiseMacroQuery->total_carbs == 0 ? $user->daily_carbs_intake : $dayWiseMacroQuery->total_carbs;
                $data['today_macros']['fat'] = $dayWiseMacroQuery->total_fat == 0 ? $user->daily_fats_intake : $dayWiseMacroQuery->total_fat;
                $data['today_macros']['protein'] = $dayWiseMacroQuery->total_protein == 0 ? $user->daily_protein_intake : $dayWiseMacroQuery->total_protein;
                $data['today_macros']['fiber'] = $dayWiseMacroQuery->total_fiber == 0 ? $user->daily_fiber_intake : $dayWiseMacroQuery->total_fiber;
                $data['today_macros']['calories'] = $dayWiseMacroQuery->total_calories == 0 ? $user->daily_calories_intake : $dayWiseMacroQuery->total_calories;

                $mealMacrosSql = Meal::select(\DB::raw('meals.id,meals.name'))
                    ->join('meal_plan_items as mpi', 'meals.id', 'mpi.meal_id')
                    // ->groupBy("meals.id")
                    ->where('mpi.meal_plan_id', $latestMealPlan->meal_plan_id)
                    // ->groupBy('mpi.food_item_id')
                    ->get();

                if ($mealMacrosSql->count() > 0) {
                    $mealMacrosConsumedTemp = [];
                    foreach ($mealMacrosSql as $value) {
                        $mealMacrosConsumedTemp[] = [
                            'id' => $value->id,
                            'name' => $value->name,
                            'macros' => [
                                'carb' => '0.0',
                                'fat' => '0.0',
                                'protein' => '0.0',
                                'fiber' => '0.0',
                                'calories' => '0.0',
                            ],
                        ];
                    }
                    $data['meal_macros_consumed'] = $mealMacrosConsumedTemp;
                }
            }

            $todayConsumedMacro = IntakeFoodItem::select(\DB::raw('ifnull(sum(distinct ift.quantity),0.00) as total_water, sum(intake_food_items.quantity * (fi.calories/fi.serving_size)) as total_calories,sum(intake_food_items.quantity * (fi.total_fat/fi.serving_size)) as total_fat, sum(intake_food_items.quantity * (fi.total_carbohydrates/fi.serving_size)) as total_carbs,sum(intake_food_items.quantity * (fi.protein/fi.serving_size)) as total_protein, sum(intake_food_items.quantity * (fi.dietary_fiber/fi.serving_size)) as total_fiber'))
                ->join('food_items as fi', 'fi.id', 'intake_food_items.food_id')
                ->leftJoin('intake_food_items as ift', function ($q) {
                    $q->on('fi.id', 'ift.food_id');
                    $q->where('fi.name', 'Water');
                })
                ->groupBy('intake_food_items.date')
                ->where('intake_food_items.user_id', $user_id)->where('intake_food_items.date', $date)
                ->first();

            if ($todayConsumedMacro) {
                $data['today_macros_consumed']['carb'] = $todayConsumedMacro->total_carbs;
                $data['today_macros_consumed']['fat'] = $todayConsumedMacro->total_fat;
                $data['today_macros_consumed']['protein'] = $todayConsumedMacro->total_protein;
                $data['today_macros_consumed']['fiber'] = $todayConsumedMacro->total_fiber;
                $data['today_macros_consumed']['calories'] = $todayConsumedMacro->total_calories;
                $data['today_macros_consumed']['water'] = $todayConsumedMacro->total_water;
            }

            $week_start_date = date('Y-m-d', strtotime('-7 day', strtotime($date)));
            $week_end_date = $date;

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod(new DateTime($week_start_date), $interval, new DateTime($week_end_date));
            $weeklyMacrosConsumedList = [];
            $dailtWeeklyMacrosConsumedList = [];
            foreach ($period as $dt) {
                $weeklyMacrosConsumedData = IntakeFoodItem::select(\DB::raw('intake_food_items.date, sum(intake_food_items.quantity * (fi.calories/fi.serving_size)) as total_calories,sum(intake_food_items.quantity * (fi.total_fat/fi.serving_size)) as total_fat, sum(intake_food_items.quantity * (fi.total_carbohydrates/fi.serving_size)) as total_carbs,sum(intake_food_items.quantity * (fi.protein/fi.serving_size)) as total_protein, sum(intake_food_items.quantity * (fi.dietary_fiber/fi.serving_size)) as total_fiber'))
                    ->join('food_items as fi', 'fi.id', 'intake_food_items.food_id')
                    ->groupBy('intake_food_items.date')
                    ->where('user_id', $user_id)->where(\DB::raw('DATE(date)'), $dt->format('Y-m-d'))
                    ->first();

                if ($weeklyMacrosConsumedData) {
                    $weeklyMacrosConsumedList[] = [
                        'date' => $dt->format('Y-m-d'),

                        'carb' => $weeklyMacrosConsumedData->total_carbs,
                        'fat' => $weeklyMacrosConsumedData->total_fat,
                        'protein' => $weeklyMacrosConsumedData->total_protein,
                        'fiber' => $weeklyMacrosConsumedData->total_fiber,
                        'calories' => $weeklyMacrosConsumedData->total_calories,

                    ];
                } else {
                    $weeklyMacrosConsumedList[] = [
                        'date' => $dt->format('Y-m-d'),

                        'carb' => '0.0',
                        'fat' => '0.0',
                        'protein' => '0.0',
                        'fiber' => '0.0',
                        'calories' => '0.0',

                    ];
                }

            }

            // daily week loop
            $week_start_date = date('Y-m-d', strtotime('-14 day', strtotime($date)));
            $week_end_date = $date;

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod(new DateTime($week_start_date), $interval, new DateTime($week_end_date));

            $latest_meal_plan_id=0;
            if($latestMealPlan){
            $latest_meal_plan_id=$latestMealPlan->meal_plan_id;
            }
            foreach ($period as $dt) {
                // week day
                $wday = date('w', strtotime($dt->format('Y-m-d')));
                $wday = ($wday == 0) ? 7 : $wday;
                $wsub = \DB::table('meal_plan_items as mpi')
                    ->select(
                        'mpi.id',
                        'mpi.meal_id',
                        'mpi.food_item_id',
                        'mpi.quantity',
                        'fi.calories',
                        'fi.serving_size',
                        'fi.total_carbohydrates',
                        'fi.total_fat',
                        'fi.protein',
                        'fi.dietary_fiber',
                        \DB::raw('ROW_NUMBER() OVER (PARTITION BY mpi.meal_id, mpi.food_item_id ORDER BY mpi.id ASC) as row_num')
                    )
                    ->join('food_items as fi', 'fi.id', '=', 'mpi.food_item_id')
                    ->where('mpi.meal_plan_id', $latest_meal_plan_id)
                    ->where('mpi.day', $wday);

                $dayWiseMacroQuery = \DB::table(\DB::raw("({$wsub->toSql()}) as wsub"))
                    ->mergeBindings($wsub)
                    ->select(
                        \DB::raw('SUM(quantity * (calories / serving_size)) AS total_calories'),
                        \DB::raw('SUM(quantity * (total_carbohydrates / serving_size)) AS total_carbs'),
                        \DB::raw('SUM(quantity * (total_fat / serving_size)) AS total_fat'),
                        \DB::raw('SUM(quantity * (protein / serving_size)) AS total_protein'),
                        \DB::raw('SUM(quantity * (dietary_fiber / serving_size)) AS total_fiber')
                    )
                    ->where('row_num', 1)
                    ->first();

                $dailtWeeklyMacrosConsumedList[] = [
                    'date' => $dt->format('Y-m-d'),

                    'carb' => $dayWiseMacroQuery->total_carbs,
                    'fat' => $dayWiseMacroQuery->total_fat,
                    'protein' => $dayWiseMacroQuery->total_protein,
                    'fiber' => $dayWiseMacroQuery->total_fiber,
                    'calories' => $dayWiseMacroQuery->total_calories,

                ];

                // end week day
            }
            // daily week loop end
            if (count($weeklyMacrosConsumedList) > 0) {
                $data['weekly_macros_consumed'] = $weeklyMacrosConsumedList;
            }
            if (count($dailtWeeklyMacrosConsumedList) > 0) {
                $data['daily_weekly_macros'] = $dailtWeeklyMacrosConsumedList;
            }
            $meals = Meal::where('active', 1)->get();
            $mealMacrosConsumedTemp = [];
            foreach ($meals as $key => $meal) {
                $mealArray = [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'macros' => [
                        'carb' => 0.0,
                        'fat' => 0.0,
                        'protein' => 0.0,
                        'fiber' => 0.0,
                        'calories' => 0.0,
                    ],
                ];
                $mealMacrosConsumedData = IntakeFoodItem::select(\DB::raw('m.id,m.name, sum(intake_food_items.quantity * (fi.calories/fi.serving_size)) as total_calories,sum(intake_food_items.quantity * (fi.total_fat/fi.serving_size)) as total_fat, sum(intake_food_items.quantity * (fi.total_carbohydrates/fi.serving_size)) as total_carbs,sum(intake_food_items.quantity * (fi.protein/fi.serving_size)) as total_protein, sum(intake_food_items.quantity * (fi.dietary_fiber/fi.serving_size)) as total_fiber'))
                    ->join('food_items as fi', 'fi.id', 'intake_food_items.food_id')
                    ->join('meals as m', 'm.id', 'intake_food_items.meal_id')
                    ->where('user_id', $user_id)
                    ->where('meal_id', $meal->id)
                    ->where('intake_food_items.date', $date)
                    ->groupBy('m.id', 'm.name')
                    ->get();

                if ($mealMacrosConsumedData->count() > 0) {

                    foreach ($mealMacrosConsumedData as $value) {
                        $mealArray = [
                            'id' => $value->id,
                            'name' => $value->name,
                            'macros' => [
                                'carb' => $value->total_carbs,
                                'fat' => $value->total_fat,
                                'protein' => $value->total_protein,
                                'fiber' => $value->total_fiber,
                                'calories' => $value->total_calories,
                            ],
                        ];
                    }
                }
                $mealMacrosConsumedTemp[] = $mealArray;
            }
            $data['meal_macros_consumed'] = $mealMacrosConsumedTemp;
            $membership = UserMembership::where('user_id', $user_id)->orderBy('id', 'desc')->first();

            $data['package_start_date'] = ($membership) ? date('Y-m-d', strtotime($membership->start_date)) : null;
            $data['package_end_date'] = ($membership) ? date('Y-m-d', strtotime($membership->end_date)) : null;

            return response()->json(['status' => true, 'data' => $data], 200, [], JSON_NUMERIC_CHECK);
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

    public function getFoodItems(Request $request)
    {

        $foodItems = FoodItem::where('active', 1);

        if (isset($request->search) && $request->search != '') {

            $foodItems = $foodItems->where(\DB::raw('LOWER(name)'), 'like', "%{$request->search}%");
        }

        $foodItems = $foodItems->paginate($request->page_size == null ? 20 : $request->page_size);
        $total = $foodItems->total();

        if (count($foodItems) > 0) {
            return response()->json(['status' => true, 'data' => $foodItems->items(), 'total' => $total]);
        } else {
            return response()->json(['status' => false, 'msg' => 'No items found']);
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
                'end_date' => 'required',
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
            $macroTrackerData = IntakeFoodItem::select(\DB::raw('intake_food_items.date, FORMAT(sum(intake_food_items.quantity * (fi.calories/fi.serving_size)),1) as total_calories,FORMAT(sum(intake_food_items.quantity * (fi.total_fat/fi.serving_size)),1) as total_fat, FORMAT(sum(intake_food_items.quantity * (fi.total_carbohydrates/fi.serving_size)),1) as total_carbs,FORMAT(sum(intake_food_items.quantity * (fi.protein/fi.serving_size)),1) as total_protein'))
                ->join('food_items as fi', 'fi.id', 'intake_food_items.food_id')
                ->groupBy('intake_food_items.date')
                ->where('user_id', $user_id)->whereBetween(\DB::raw('DATE(date)'), [$start_date, $end_date])
                ->get();

            return response()->json(['status' => true, 'data' => $macroTrackerData]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        }

        
    }
}
