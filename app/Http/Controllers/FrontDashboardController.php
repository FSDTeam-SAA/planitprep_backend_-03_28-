<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\ExercisePlanDay;
use App\Models\FitnessLevel;
use App\Models\FoodGroup;
use App\Models\FoodItem;
use App\Models\Goal;
use App\Models\IntakeFoodItem;
use App\Models\Meal;
use App\Models\State;
use App\Models\User;
use App\Models\UserAllergies;
use App\Models\UserDietRegime;
use App\Models\UserGoal;
use App\Models\UserMealPlan;
use App\Models\UserMedicalIssue;
use App\Models\UserMembership;
use App\Models\UserPreference;
use DateInterval;
use DatePeriod;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Support\AnthropicMessageClient;

class FrontDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $this->getAveragDailyDietGoals($user->id);

        $data = $this->fetchDashboardData($request);

        // Calculate weight diff and diff percentage.
        if ($data['initial_weight'] > 0) {
            $data['weight_diff'] = abs($data['initial_weight'] - $data['my_current_weight']);
            $data['weight_diff_percentage'] = (($data['my_current_weight'] - $data['initial_weight']) / $data['initial_weight']) * 100;
        } else {
            $data['weight_diff_percentage'] = 0;
            $data['weight_diff'] = 0;
        }

        $intake = $this->fetch_intake();
        $food_groups = FoodGroup::select('id', 'name', 'image')->where('active', 1)->get();

        return view('dashboard', compact('user', 'data', 'intake', 'food_groups'));
    }

    public function add_food(Request $request, $meal_id)
    {
        $food_items = FoodItem::select('id', 'name', 'calories')->where('active', 1)->get();

        // Fetch intake items for today.
        $dt = date('Y-m-d');

        $intake = IntakeFoodItem::where('meal_id', $meal_id)
            ->where('date', $dt)
            ->get()
            ->toArray();
        $arr = [];

        foreach ($intake as $item) {
            $arr[$item['food_id']] = [
                'record_id' => $item['id'],
                'qty' => (int) $item['quantity'],
            ];
        }

        $intake = $arr;
        $intake_keys = array_keys($intake);

        $food_items = $food_items->sortByDesc(function ($item) use ($intake_keys) {
            return in_array($item['id'], $intake_keys) ? 0 : 1;
        });

        $food_items = $food_items->reverse();

        return view('add-food', compact('meal_id', 'food_items', 'intake', 'intake_keys'));
    }

    public function food_item_details(Request $request)
    {
        $meal_id = $request->meal_id;
        $food_item_id = $request->food_item_id;

        $food_item = FoodItem::where('id', $food_item_id)->first();

        if ($food_item->image != '') {
            $path = 'food-items/'.$food_item->image;

            if (Storage::disk('public')->exists($path)) {
                $food_item->image = asset(Storage::url('food-items/'.$food_item->image));
            } else {
                $food_item->image = '';
            }
        }

        return view('food-item-details', compact('meal_id', 'food_item'))->render();
    }

    public function remove_intake_item(Request $request)
    {
        IntakeFoodItem::where('id', $request->id)->delete();
    }

    public function save_intake(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meal_id' => 'required',
            'food_item_id' => 'required',
            'qty' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'false',
                'errors' => $validator->errors(),
            ];
        }

        $intakeFood = IntakeFoodItem::where('food_id', $request->food_item_id)
            ->where('meal_id', $request->meal_id)
            ->where('user_id', Auth::user()->id)
            ->where('date', date('Y-m-d'))
            ->first();

        if (! $intakeFood) {
            $intakeFood = new IntakeFoodItem;
        }

        $user_id = Auth::id();

        if ($request->qty == 0) {
            IntakeFoodItem::where('user_id', $user_id)
                ->where('date', date('Y-m-d'))
                ->where('meal_id', $request->meal_id)
                ->where('food_id', $request->food_item_id)
                ->delete();
        } else {
            $intakeFood->food_id = $request->food_item_id;
            $intakeFood->meal_id = $request->meal_id;
            $intakeFood->user_id = $user_id;
            $intakeFood->quantity = $request->qty;
            $intakeFood->date = date('Y-m-d');
            $intakeFood->save();
        }
    }

    public function fetch_intake()
    {
        $dt = date('Y-m-d');

        $intake = IntakeFoodItem::join('food_items', 'food_items.id', '=', 'intake_food_items.food_id')
            ->join('meals', 'meals.id', '=', 'intake_food_items.meal_id')
            ->where('intake_food_items.date', $dt)
            ->select(
                'meals.id as id',
                'meals.name as name',
                'meals.image as image',
                \DB::raw('CAST(SUM(intake_food_items.quantity * food_items.calories) AS UNSIGNED) AS calories'),
                \DB::raw('CAST(SUM(intake_food_items.quantity * food_items.protein) AS UNSIGNED) AS protein'),
                \DB::raw('CAST(SUM(intake_food_items.quantity * food_items.total_carbohydrates) AS UNSIGNED) AS carbohydrates'),
                \DB::raw('CAST(SUM(intake_food_items.quantity * food_items.total_fat) AS UNSIGNED) AS fat'),
                \DB::raw('CAST(SUM(intake_food_items.quantity * food_items.dietary_fiber) AS UNSIGNED) AS fiber')
            )
            ->groupBy('id', 'meals.name', 'meals.image')
            ->get()
            ->keyBy('id');

        $intake = $intake->toArray();

        // Calculate total
        $total = [];
        $total['calories'] = 0;
        $total['protein'] = 0;
        $total['carbohydrates'] = 0;
        $total['fat'] = 0;
        $total['fiber'] = 0;

        foreach ($intake as $x) {
            $total['calories'] += $x['calories'];
            $total['protein'] += $x['protein'];
            $total['carbohydrates'] += $x['carbohydrates'];
            $total['fat'] += $x['fat'];
            $total['fiber'] += $x['fiber'];
        }

        // Make an array of meals with nutirtion values for each meal.
        $meals = Meal::select('id', 'name', 'image')
            ->where('active', 1)
            ->get()
            ->toArray();
        $arr = [];

        foreach ($meals as $meal) {
            if (isset($intake[$meal['id']])) {
                $arr[] = $intake[$meal['id']];
            } else {
                $meal['calories'] = 0;
                $meal['protein'] = 0;
                $meal['carbohydrates'] = 0;
                $meal['fat'] = 0;
                $meal['fiber'] = 0;
                $arr[] = $meal;
            }
        }

        $meals = $arr;

        $data = [
            'meals' => $meals,
            'total' => $total,
        ];

        return $data;
    }

    public function getAveragDailyDietGoals($user_id)
    {
        $userId = $user_id;

        if ($userId > 0) {
            $userObj = User::find($userId);

            if ($userObj) {
                $goal = Goal::find($userObj->goal_id);
                $fitnessLevel = FitnessLevel::find($userObj->fitness_level_id);
                $exercisePlanDay = ExercisePlanDay::find($userObj->exercise_plan_day_id);
                $userGoalObj = UserGoal::where('user_id', $userObj->id)->orderBy('id', 'desc')->first();
                $cityObj = City::find($userObj->city_id);
                $stateObj = State::find($userObj->state_id);
                $countryObj = Country::find($userObj->country_id);
                $userMedicalIssues = UserMedicalIssue::select('mi.name')
                    ->join('medical_issues as mi', 'mi.id', 'user_medical_issues.medical_issue_id')
                    ->where('user_medical_issues.user_id', $userObj->id)
                    ->get();

                $userAllergens = UserAllergies::select('a.name')
                    ->join('allergens as a', 'a.id', 'user_allergies.allergy_id')
                    ->where('user_allergies.user_id', $userObj->id)
                    ->get();

                $userDietRegimes = UserDietRegime::select('dr.title')
                    ->join('diet_regimes as dr', 'dr.id', 'user_diet_regimes.diet_regime_id')
                    ->where('user_diet_regimes.user_id', $userObj->id)
                    ->get();

                $userPreferences = UserPreference::select('dt.name')
                    ->join('diet_types as dt', 'dt.id', 'user_preferences.diet_type_id')
                    ->where('user_preferences.user_id', $userObj->id)
                    ->get();

                $messages = "I am providing you user's details for which I need a particular average daily requirements of calories in KCal and carbs,fiber,fats, proteins in grams and daily water requirement in litres as per the goal to achieved in 1-2 months time frame provided in the user's details like current weight and target weight.";
                $messages = 'weight='.$userObj->weight.',';
                if ($userGoalObj) {
                    $messages .= 'target weight='.$userGoalObj->target_value.',';
                }
                if ($cityObj) {
                    $messages .= 'city='.$cityObj->name.',';
                }
                if ($stateObj) {
                    $messages .= 'state='.$stateObj->name.',';
                }
                if ($countryObj) {
                    $messages .= 'country='.$countryObj->name.',';
                }
                $messages .= 'height='.$userObj->height.',';
                $messages .= 'gender='.$userObj->gender == 'M' ? 'Male' : 'Women';
                $messages .= 'age='.$userObj->age;
                if ($goal) {
                    $messages .= 'goal='.$goal->name.',';
                }
                if ($fitnessLevel) {
                    $messages .= 'fitness level='.$fitnessLevel->name.',';
                }
                if ($exercisePlanDay) {
                    $messages .= 'weekly exercise days='.$exercisePlanDay->name.',';
                }
                if ($userMedicalIssues->count() > 0) {
                    $medIssues = '';
                    foreach ($userMedicalIssues as $issue) {
                        $medIssues .= $issue->name.', ';
                    }
                    $messages .= "medical issues='".trim($medIssues, ', ')."',";
                }

                if ($userAllergens->count() > 0) {
                    $allergies = '';
                    foreach ($userAllergens as $alr) {
                        $allergies .= $alr->name.', ';
                    }
                    $messages .= "allergies='".trim($allergies, ', ')."',";
                }

                if ($userDietRegimes->count() > 0) {
                    $dietRegimes = '';
                    foreach ($userDietRegimes as $dr) {
                        $dietRegimes .= $dr->title.', ';
                    }
                    $messages .= "diet regimes followed earlier='".trim($dietRegimes, ', ')."',";
                }

                if ($userPreferences->count() > 0) {
                    $userTypes = '';
                    foreach ($userPreferences as $up) {
                        $userTypes .= $up->name.', ';
                    }
                    $messages .= "diet types='".trim($userTypes, ', ')."',";
                }
                $expectedStructure = [
                    'daily_calorie_intake' => 2800,
                    'daily_fat_intake' => 80.00,
                    'daily_protein_intake' => 150.0,
                    'daily_carb_intake' => 200.0,
                    'daily_fiber_intake' => 18.0,
                    'daily_water_intake' => 3.0,
                ];
                $desiredResponseStructure = [
                    'status' => 'success',
                    'message' => 'Response retrieved successfully',
                    'data' => $expectedStructure,
                    'timestamp' => now()->toDateTimeString(),
                ];

                // Create a system message to guide the model
                $systemMessage = "Please provide the required daily average information as per the following JSON format:\n".json_encode($desiredResponseStructure);

                // Prepare the user input message
                $userInputMessage = $messages;

                // Previous OpenAI request kept commented for reference.
                // $response = Http::withOptions([
                //     'verify' => false,
                // ])
                //     ->withHeaders([
                //         'Authorization' => 'Bearer '.$apiKey,
                //     ])->post('https://api.openai.com/v1/chat/completions', [...]);

                $response = AnthropicMessageClient::send(
                    $systemMessage,
                    $userInputMessage,
                    1500,
                    ['verify' => false]
                );

                // Handle the Claude API response
                if (($response['status'] ?? 'error') === 'success') {
                    $content = $response['content'];

                    // Attempt to decode the content to ensure it's in the correct format
                    $decodedContent = json_decode($content, true);

                    // If decoding fails, revert to the desired structure with an error message
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $decodedContent = [
                            'status' => 'error',
                            'message' => 'Invalid response format',
                            'data' => null,
                            'timestamp' => now()->toDateTimeString(),
                        ];
                        // return response()->json($decodedContent);
                    } else {
                        // Populate the data if the response is valid
                        $desiredData = $decodedContent['data'] ?? [];
                        $userObj->daily_calorie_intake = $desiredData['daily_calorie_intake'];
                        $userObj->daily_fats_intake = $desiredData['daily_fat_intake'];
                        $userObj->daily_protein_intake = $desiredData['daily_protein_intake'];
                        $userObj->daily_carbs_intake = $desiredData['daily_carb_intake'];
                        $userObj->daily_fiber_intake = $desiredData['daily_fiber_intake'];
                        $userObj->daily_water = $desiredData['daily_water_intake'];
                        $userObj->save();
                    }
                }
            }
        }
    }

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
            $latestMealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();

            if ($latestMealPlan) {
                $dayWiseMacroQuery = DB::table(\DB::raw('meal_plan_items as mpi'))->select(\DB::raw('sum(mpi.quantity * (fi.calories/fi.serving_size)) as total_calories,sum(mpi.quantity * (fi.total_carbohydrates/fi.serving_size)) as total_carbs,sum(mpi.quantity * (fi.total_fat/fi.serving_size)) as total_fat,sum(mpi.quantity * (fi.protein/fi.serving_size)) as total_protein,sum(mpi.quantity * (fi.dietary_fiber/fi.serving_size)) as total_fiber'))
                    ->join('food_items as fi', 'fi.id', 'mpi.food_item_id')
                    ->where('mpi.meal_plan_id', $latestMealPlan->meal_plan_id)
                    ->where('mpi.day', $day)
                    ->first();
                $data['today_macros']['carb'] = $dayWiseMacroQuery->total_carbs == 0 ? $user->daily_carbs_intake : $dayWiseMacroQuery->total_carbs;
                $data['today_macros']['fat'] = $dayWiseMacroQuery->total_fat == 0 ? $user->daily_fats_intake : $dayWiseMacroQuery->total_fat;
                $data['today_macros']['protein'] = $dayWiseMacroQuery->total_protein == 0 ? $user->daily_protein_intake : $dayWiseMacroQuery->total_protein;
                $data['today_macros']['fiber'] = $dayWiseMacroQuery->total_fiber == 0 ? $user->daily_fiber_intake : $dayWiseMacroQuery->total_fiber;
                $data['today_macros']['calories'] = $dayWiseMacroQuery->total_calories == 0 ? $user->daily_calories_intake : $dayWiseMacroQuery->total_calories;

                $mealMacrosSql = Meal::select(\DB::raw('meals.id,meals.name'))
                    ->join('meal_plan_items as mpi', 'meals.id', 'mpi.meal_id')
                    ->groupBy('meals.id', 'meals.name')
                    ->where('mpi.meal_plan_id', $latestMealPlan->meal_plan_id)
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
            if (count($weeklyMacrosConsumedList) > 0) {
                $data['weekly_macros_consumed'] = $weeklyMacrosConsumedList;
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
                    ->groupBy('m.id', 'm.name')
                    ->where('user_id', $user_id)
                    ->where('meal_id', $meal->id)
                    ->where('intake_food_items.date', $date)
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

            return $data;
        } else {
            return null;
        }
    }
}
