<?php

namespace App\Http\Controllers\Api;

use DateTime;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\ExercisePlanDay;
use App\Models\FitnessLevel;
use App\Models\Goal;
use App\Models\State;
use App\Models\User;
use App\Models\UserAllergies;
use App\Models\UserDietRegime;
use App\Models\UserGoal;
use App\Models\UserMedicalIssue;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Support\AnthropicMessageClient;
use App\Models\CookingPrep;
use App\Models\Cuisine;
use App\Models\Recipe;
use App\Models\MealCount;
use App\Models\CookingTime;
use App\Models\Appliance;
use App\Models\MealPrepSchedule;
use App\Models\AiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



use Illuminate\Support\Facades\Log;


class ChatController extends Controller
{


public function generateUserMeal(Request $request)
{
    try {

        //  Validate Required Parameters
        $request->validate([
            'user_id'     => 'required',
            'type'       => 'required|string',
            'prep_type'  => 'required|string',
            'date'       => 'required|date'
        ]);

        $userId   = $request->userid;
        $type     = strtolower($request->type);
        $prepType = strtoupper($request->prep_type);
        $date     = Carbon::parse($request->date)->format('Y-m-d');

        Log::info("Generate Meal Request", [
            'userid' => $userId,
            'type' => $type,
            'prep_type' => $prepType,
            'date' => $date
        ]);

        /*
        |--------------------------------------------------------------------------
        | Decision Logic
        |--------------------------------------------------------------------------
        */

        //  DAY WISE FLOW
        if ($prepType === 'DAY_WISE_MEAL') {

            if ($type === 'surprise_meal') {

                return $this->getSurpriseMeal($request);

            } else {

                return $this->getFullDietPlanFromChatGPTforUser($request);
            }
        }

        //  BATCH FLOW we shall implement in future
        // if ($prepType === 'BATCH_MEAL') {

        //     return $this->generateBatchPrep($request);
        // }

        // Invalid prep_type
        return [
            'status' => 'error',
            'message' => 'Invalid preparation type'
        ];

    } catch (\Exception $e) {

        Log::error("Generate Meal Error: " . $e->getMessage());

        return [
            'status' => 'error',
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ];
    }
}


    public function getWeeklyDietPlanFromChatGPT3(Request $request)
    {
        $userId = (isset($request->user_id) && $request->user_id > 0) ? $request->user_id : 0;
        if ($userId > 0) {
            $userObj = User::find($userId);
            if ($userObj) {
                // Fetch User full detail and make message string for chat gpt

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

                $messages = [];
                $messages['user_id'] = $userObj->id;
                $messages['weight'] = $userObj->weight;
                if ($userGoalObj) {
                    $messages['target weight'] = $userGoalObj->target_value;
                }
                if ($cityObj) {
                    $messages['city'] = $cityObj->name;
                }
                if ($stateObj) {
                    $messages['state'] = $stateObj->name;
                }
                if ($countryObj) {
                    $messages['country'] = $countryObj->name;
                }
                $messages['height'] = $userObj->height;
                $messages['gender'] = $userObj->gender == 'M' ? 'Male' : 'Women';
                $messages['age'] = $userObj->age;
                if ($goal) {
                    $messages['goal'] = $goal->name;
                }
                if ($fitnessLevel) {
                    $messages['fitness_level'] = $fitnessLevel->name;
                }
                if ($exercisePlanDay) {
                    $messages['exercise plan days'] = $exercisePlanDay->name;
                }
                if ($userMedicalIssues->count() > 0) {
                    $medIssues = '';
                    foreach ($userMedicalIssues as $issue) {
                        $medIssues .= $issue->name . ', ';
                    }
                    $messages['medical issues'] = trim($medIssues, ', ');
                }

                if ($userAllergens->count() > 0) {
                    $allergies = '';
                    foreach ($userAllergens as $alr) {
                        $allergies .= $alr->name . ', ';
                    }
                    $messages['allergies'] = trim($allergies, ', ');
                }

                if ($userDietRegimes->count() > 0) {
                    $dietRegimes = '';
                    foreach ($userDietRegimes as $dr) {
                        $dietRegimes .= $dr->title . ', ';
                    }
                    $messages['diet regimes'] = trim($dietRegimes, ', ');
                }

                if ($userPreferences->count() > 0) {
                    $userTypes = '';
                    foreach ($userPreferences as $up) {
                        $userTypes .= $up->name . ', ';
                    }
                    $messages['diet types'] = trim($userTypes, ', ');
                }

                // dd($messages);
                $dietPlanFinalData = [];
                $dayWiseDietPlanData = [];
                for ($i = 1; $i <= 1; $i++) {
                    $messages['day'] = 'Day ' . $i . ' of weekly diet plan';
                    dd(json_encode($messages));
                    $response = $this->getDayWiseDietPlanFromChatGPT($messages);
                    $jsonString = $response->getContent();
                    $responseInArray = json_decode($jsonString, true);
                    if (isset($responseInArray['status']) && $responseInArray['status'] == 'success') {
                        $dayWiseDietPlanData[] = $responseInArray['data']['diet_chart'];
                    }
                }
                if (count($dayWiseDietPlanData) > 0) {
                    $dietPlanFinalData = ['weekly_diet_chart' => $dayWiseDietPlanData];

                    return response()->json($dietPlanFinalData);
                } else {
                    $this->getWeeklyDietPlanFromChatGPT($request);
                }
            } else {
            }
        } else {
        }
    }

    private function getDayWiseDietPlanFromChatGPT3($message)
    {
        Log::info('User ID received: ' . $message);
        $expectedStructure =
            [
                'diet_chart' => [
                    [
                        'day' => 'Day 1',
                        'meals' => [
                            [
                                'meal_time' => 'Breakfast',
                                'food_items' => [
                                    [
                                        'name' => 'Oats',
                                        'quantity' => '1 cup',
                                        'data' => [
                                            'cal' => 150,
                                            'fat' => 3.00,
                                            'sat' => 0.5,
                                            'trs' => 0.0,
                                            'mno' => 0.2,
                                            'ply' => 0.1,
                                            'chl' => 0.00,
                                            'sod' => 2.00,
                                            'car' => 27.00,
                                            'fbr' => 3.00,
                                            'sgr' => 1.00,
                                            'ads' => 0.00,
                                            'pro' => 5.00,
                                            'a' => 0.00,
                                            'c' => 0.00,
                                            'ca' => 20.00,
                                            'irn' => 1.5,
                                            'pot' => 150.00,
                                            'd' => 0.00,
                                            'b6' => 0.2,
                                            'b12' => 0.0,
                                            'mag' => 40.00,
                                            'gix' => 55.00,
                                            'com' => 'Rich in complex carbs and dietary fiber',
                                            'typ' => 'VE',
                                        ],
                                    ],
                                    [
                                        'name' => 'Skimmed Milk',
                                        'quantity' => '1 cup',
                                        'data' => [
                                            'cal' => 90,
                                            'fat' => 0.00,
                                            'sat' => 0.0,
                                            'trs' => 0.0,
                                            'mno' => 0.0,
                                            'ply' => 0.0,
                                            'chl' => 5.00,
                                            'sod' => 100.00,
                                            'car' => 12.00,
                                            'fbr' => 0.00,
                                            'sgr' => 12.00,
                                            'ads' => 0.00,
                                            'pro' => 8.00,
                                            'a' => 500.00,
                                            'c' => 0.00,
                                            'ca' => 300.00,
                                            'irn' => 0.0,
                                            'pot' => 380.00,
                                            'd' => 2.00,
                                            'b6' => 0.1,
                                            'b12' => 1.0,
                                            'mag' => 30.00,
                                            'gix' => 32.00,
                                            'com' => 'High in calcium and low in fat',
                                            'typ' => 'VG',
                                        ],
                                    ],
                                    [
                                        'name' => 'Almonds',
                                        'quantity' => '10 pieces',
                                        'data' => [
                                            'cal' => 70,
                                            'fat' => 6.00,
                                            'sat' => 0.5,
                                            'trs' => 0.0,
                                            'mno' => 3.5,
                                            'ply' => 1.0,
                                            'chl' => 0.00,
                                            'sod' => 0.00,
                                            'car' => 2.00,
                                            'fbr' => 1.00,
                                            'sgr' => 0.5,
                                            'ads' => 0.00,
                                            'pro' => 3.00,
                                            'a' => 0.00,
                                            'c' => 0.00,
                                            'ca' => 30.00,
                                            'irn' => 0.5,
                                            'pot' => 100.00,
                                            'd' => 0.00,
                                            'b6' => 0.1,
                                            'b12' => 0.0,
                                            'mag' => 60.00,
                                            'gix' => 15.00,
                                            'com' => 'Rich in healthy fats and fiber',
                                            'typ' => 'VG',
                                        ],
                                    ],
                                    [
                                        'name' => 'Boiled Eggs',
                                        'quantity' => '2',
                                        'data' => [
                                            'cal' => 140,
                                            'fat' => 10.00,
                                            'sat' => 3.0,
                                            'trs' => 0.0,
                                            'mno' => 4.0,
                                            'ply' => 1.0,
                                            'chl' => 210.00,
                                            'sod' => 70.00,
                                            'car' => 1.00,
                                            'fbr' => 0.00,
                                            'sgr' => 0.5,
                                            'ads' => 0.00,
                                            'pro' => 12.00,
                                            'a' => 270.00,
                                            'c' => 0.00,
                                            'ca' => 50.00,
                                            'irn' => 1.0,
                                            'pot' => 60.00,
                                            'd' => 1.00,
                                            'b6' => 0.1,
                                            'b12' => 1.0,
                                            'mag' => 10.00,
                                            'gix' => 0.00,
                                            'com' => 'Excellent source of protein',
                                            'typ' => 'EG',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'meal_time' => 'Morning Snack',
                                'food_items' => [
                                    [
                                        'name' => 'Apple',
                                        'quantity' => '1 medium',
                                        'data' => [
                                            'cal' => 95,
                                            'fat' => 0.3,
                                            'sat' => 0.1,
                                            'trs' => 0.0,
                                            'mno' => 0.0,
                                            'ply' => 0.0,
                                            'chl' => 0.0,
                                            'sod' => 1.0,
                                            'car' => 25.0,
                                            'fbr' => 4.0,
                                            'sgr' => 19.0,
                                            'ads' => 0.0,
                                            'pro' => 0.5,
                                            'a' => 54.0,
                                            'c' => 8.4,
                                            'ca' => 6.0,
                                            'irn' => 0.1,
                                            'pot' => 195.0,
                                            'd' => 0.0,
                                            'b6' => 0.1,
                                            'b12' => 0.0,
                                            'mag' => 5.0,
                                            'gix' => 36.0,
                                            'com' => 'Good source of fiber and natural sugars',
                                            'typ' => 'VE',
                                        ],
                                    ],
                                    [
                                        'name' => 'Whey Protein',
                                        'quantity' => '1 scoop',
                                        'data' => [
                                            'cal' => 120,
                                            'fat' => 1.0,
                                            'sat' => 0.5,
                                            'trs' => 0.0,
                                            'mno' => 0.2,
                                            'ply' => 0.1,
                                            'chl' => 30.0,
                                            'sod' => 50.0,
                                            'car' => 3.0,
                                            'fbr' => 0.0,
                                            'sgr' => 1.5,
                                            'ads' => 0.0,
                                            'pro' => 24.0,
                                            'a' => 0.0,
                                            'c' => 0.0,
                                            'ca' => 150.0,
                                            'irn' => 0.5,
                                            'pot' => 120.0,
                                            'd' => 0.0,
                                            'b6' => 0.5,
                                            'b12' => 0.4,
                                            'mag' => 20.0,
                                            'gix' => 15.0,
                                            'com' => 'High in protein and low in fats',
                                            'typ' => 'VG',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'meal_time' => 'Lunch',
                                'food_items' => [
                                    [
                                        'name' => 'Grilled Chicken',
                                        'quantity' => '150g',
                                        'data' => [
                                            'cal' => 300,
                                            'fat' => 16.0,
                                            'sat' => 4.0,
                                            'trs' => 0.0,
                                            'mno' => 5.0,
                                            'ply' => 3.0,
                                            'chl' => 90.0,
                                            'sod' => 80.0,
                                            'car' => 0.0,
                                            'fbr' => 0.0,
                                            'sgr' => 0.0,
                                            'ads' => 0.0,
                                            'pro' => 35.0,
                                            'a' => 0.0,
                                            'c' => 0.0,
                                            'ca' => 20.0,
                                            'irn' => 1.5,
                                            'pot' => 230.0,
                                            'd' => 0.0,
                                            'b6' => 0.7,
                                            'b12' => 1.0,
                                            'mag' => 30.0,
                                            'gix' => 0.0,
                                            'com' => 'Rich in protein',
                                            'typ' => 'NV',
                                        ],
                                    ],
                                    [
                                        'name' => 'Brown Rice',
                                        'quantity' => '1 cup',
                                        'data' => [
                                            'cal' => 215,
                                            'fat' => 2.0,
                                            'sat' => 0.5,
                                            'trs' => 0.0,
                                            'mno' => 0.5,
                                            'ply' => 0.5,
                                            'chl' => 0.0,
                                            'sod' => 10.0,
                                            'car' => 45.0,
                                            'fbr' => 8.0,
                                            'sgr' => 0.5,
                                            'ads' => 0.0,
                                            'pro' => 5.0,
                                            'a' => 0.0,
                                            'c' => 0.0,
                                            'ca' => 10.0,
                                            'irn' => 0.8,
                                            'pot' => 150.0,
                                            'd' => 0.0,
                                            'b6' => 0.2,
                                            'b12' => 0.0,
                                            'mag' => 85.0,
                                            'gix' => 50.0,
                                            'com' => 'Complex carbs source',
                                            'typ' => 'VE',
                                        ],
                                    ],
                                    [
                                        'name' => 'Steamed Vegetables',
                                        'quantity' => '1 cup',
                                        'data' => [
                                            'cal' => 50,
                                            'fat' => 0.0,
                                            'sat' => 0.0,
                                            'trs' => 0.0,
                                            'mno' => 0.0,
                                            'ply' => 0.0,
                                            'chl' => 0.0,
                                            'sod' => 40.0,
                                            'car' => 10.0,
                                            'fbr' => 3.0,
                                            'sgr' => 2.0,
                                            'ads' => 0.0,
                                            'pro' => 2.0,
                                            'a' => 500.0,
                                            'c' => 10.0,
                                            'ca' => 20.0,
                                            'irn' => 0.5,
                                            'pot' => 180.0,
                                            'd' => 0.0,
                                            'b6' => 0.1,
                                            'b12' => 0.0,
                                            'mag' => 10.0,
                                            'gix' => 15.0,
                                            'com' => 'Low calorie, high in fiber',
                                            'typ' => 'VG',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'meal_time' => 'Evening Snack',
                                'food_items' => [
                                    [
                                        'name' => 'Paneer Tikka',
                                        'quantity' => '100g',
                                        'data' => [
                                            'cal' => 300,
                                            'fat' => 22.0,
                                            'sat' => 12.0,
                                            'trs' => 0.0,
                                            'mno' => 5.0,
                                            'ply' => 2.0,
                                            'chl' => 40.0,
                                            'sod' => 60.0,
                                            'car' => 8.0,
                                            'fbr' => 0.0,
                                            'sgr' => 2.0,
                                            'ads' => 0.0,
                                            'pro' => 15.0,
                                            'a' => 100.0,
                                            'c' => 0.0,
                                            'ca' => 300.0,
                                            'irn' => 0.8,
                                            'pot' => 100.0,
                                            'd' => 0.0,
                                            'b6' => 0.3,
                                            'b12' => 0.5,
                                            'mag' => 40.0,
                                            'gix' => 30.0,
                                            'com' => 'Good protein source, moderate fat',
                                            'typ' => 'VG',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'meal_time' => 'Dinner',
                                'food_items' => [
                                    [
                                        'name' => 'Fish Curry',
                                        'quantity' => '150g',
                                        'data' => [
                                            'cal' => 250,
                                            'fat' => 12.0,
                                            'sat' => 3.0,
                                            'trs' => 0.0,
                                            'mno' => 5.0,
                                            'ply' => 2.0,
                                            'chl' => 60.0,
                                            'sod' => 150.0,
                                            'car' => 5.0,
                                            'fbr' => 1.0,
                                            'sgr' => 1.0,
                                            'ads' => 0.0,
                                            'pro' => 28.0,
                                            'a' => 200.0,
                                            'c' => 0.0,
                                            'ca' => 30.0,
                                            'irn' => 2.0,
                                            'pot' => 250.0,
                                            'd' => 0.0,
                                            'b6' => 0.5,
                                            'b12' => 2.5,
                                            'mag' => 30.0,
                                            'gix' => 0.0,
                                            'com' => 'Rich in protein and omega-3',
                                            'typ' => 'NV',
                                        ],
                                    ],
                                    [
                                        'name' => 'Quinoa',
                                        'quantity' => '1 cup',
                                        'data' => [
                                            'cal' => 222,
                                            'fat' => 4.0,
                                            'sat' => 0.4,
                                            'trs' => 0.0,
                                            'mno' => 1.5,
                                            'ply' => 0.5,
                                            'chl' => 0.0,
                                            'sod' => 13.0,
                                            'car' => 39.0,
                                            'fbr' => 5.0,
                                            'sgr' => 0.0,
                                            'ads' => 0.0,
                                            'pro' => 4.0,
                                            'a' => 0.0,
                                            'c' => 0.0,
                                            'ca' => 17.0,
                                            'irn' => 1.5,
                                            'pot' => 172.0,
                                            'd' => 0.0,
                                            'b6' => 0.1,
                                            'b12' => 0.0,
                                            'mag' => 59.0,
                                            'gix' => 53.0,
                                            'com' => 'Complete protein, low GI',
                                            'typ' => 'V',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

        // Define your desired structured response
        $desiredResponseStructure = [
            'status' => 'success',
            'message' => 'Response retrieved successfully',
            'data' => $expectedStructure,
            'timestamp' => now()->toDateTimeString(),
        ];

        // Create a system message to guide the model
        $systemMessage = "Please generate a complete 1-day diet chart in the following JSON format:\n" . json_encode($desiredResponseStructure, JSON_PRETTY_PRINT);

        // Prepare the user input message
        $userInputMessage = $message;

        // Previous OpenAI request kept commented for reference.
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $apiKey,
        // ])->post('https://api.openai.com/v1/chat/completions', [...]);

        $response = AnthropicMessageClient::send($systemMessage, $userInputMessage, 1500);

        // Handle the Claude API response
        if ($response['status'] === 'success') {
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

                return response()->json($decodedContent);
            } else {
                // Populate the data if the response is valid
                $desiredResponseStructure['data'] = $decodedContent['data'] ?? [];
            }

            // Return the structured response
            return response()->json($desiredResponseStructure);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $response['message'] ?? 'Failed to connect to Claude.',
                'data' => null,
                'timestamp' => now()->toDateTimeString(),
            ]);
        }
    }





    /**
     * API endpoint to generate a full diet plan.
     * Handles the HTTP request, fetches user data, and calls the helper function.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFullDietPlanFromChatGPTforUser(Request $request)

    {
        Log::info('User ID received: 123');

        // 1. Get data from the request
        $dayDuration = $request->input('day', 1); // Default to 1 day
        $isRefresh = $request->isrefresh;
        $requestDate = $request->date;

        // 2. Find user with your provided logic
        $userId = (isset($request->user_id) && $request->user_id > 0) ? $request->user_id : 0;

        if ($userId > 0) {
            $userObj = User::find($userId);
            Log::info('userid : ' . print_r($userObj->id, true));


            if ($userObj) {
                // --- This is the User Details code block you provided ---
                // Log::info('user obj : ' . print_r($userObj, true));


                $goal = Goal::find($userObj->goal_id);
                $fitnessLevel = FitnessLevel::find($userObj->fitness_level_id);

                $exercisePlanDay = ExercisePlanDay::find($userObj->exercise_plan_day_id);
                // $userGoalObj = UserGoal::where('user_id', $userObj->id)->orderBy('id', 'desc')->first();
                // $cityObj = City::find($userObj->city_id);
                // $stateObj = State::find($userObj->state_id);
                // $countryObj = Country::find($userObj->country_id);

                // --- ADD THE NEW QUERIES HERE ---
                $cuisine = Cuisine::find($userObj->cuisine_id);
                $cookingTime = CookingTime::find($userObj->cooking_time_id);
                // $appliance = Appliance::find($userObj->appliance_id);
                $mealPrepSchedule = MealPrepSchedule::find($userObj->meal_prep_schedule_id);
                $mealCount = MealCount::find($userObj->meal_count_id);

                // My addition based on our previous conversation
                $cookingPrep = CookingPrep::find($userObj->cooking_prep_id);

                // $userMedicalIssues = UserMedicalIssue::select('mi.name')
                //     ->join('medical_issues as mi', 'mi.id', 'user_medical_issues.medical_issue_id')
                //     ->where('user_medical_issues.user_id', $userObj->id)
                //     ->get();

                $userAllergens = UserAllergies::select('a.name')
                    ->join('allergens as a', 'a.id', 'user_allergies.allergy_id')
                    ->where('user_allergies.user_id', $userObj->id)
                    ->get();


                // $userDietRegimes = UserDietRegime::select('dr.title')
                //     ->join('diet_regimes as dr', 'dr.id', 'user_diet_regimes.diet_regime_id')
                //     ->where('user_diet_regimes.user_id', $userObj->id)
                //     ->get();

                $userPreferences = UserPreference::select('dt.name')
                    ->join('diet_types as dt', 'dt.id', 'user_preferences.diet_type_id')
                    ->where('user_preferences.user_id', $userObj->id)
                    ->get();

                // $userPreferences = UserPreference::select('dt.name')
                //     ->join('diet_types as dt', 'dt.id', 'user_preferences.diet_type_id')
                //     ->where('user_preferences.user_id', $userObj->id)
                //     ->get();

                $userAppliances = Appliance::select('appliances.name') // Select the name from the 'appliances' table
                    ->join('user_appliances as ua', 'ua.appliance_id', '=', 'appliances.id') // Join the pivot table
                    ->where('ua.user_id', $userObj->id) // Filter by the specific user ID
                    ->get();
                Log::info('user_appliances : ' . print_r($userAppliances, true));




                $messages = [];

                $messages['name'] = $userObj->full_name;
                $messages['user_id'] = $userObj->id;
                $messages['email'] = $userObj->email;
                $messages['weight'] = $userObj->weight;

                $messages['excercise in a week'] = $exercisePlanDay ? $exercisePlanDay->name : 'Not specified';
                // if ($userGoalObj) {
                //     $messages['target weight'] = $userGoalObj->target_value;
                // }
                // if ($cityObj) {
                //     $messages['city'] = $cityObj->name;
                // }
                // if ($stateObj) {
                //     $messages['state'] = $stateObj->name;
                // }
                // if ($countryObj) {
                //     $messages['country'] = $countryObj->name;
                // }

                // My addition
                if ($cookingPrep) {
                    $messages['cooking_prep_preference'] = $cookingPrep->name;
                }

                $messages['height'] = $userObj->height;
                $messages['gender'] = $userObj->gender == 'M' ? 'Male' : 'Women';
                $messages['age'] = $userObj->age;
                if ($goal) {
                    $messages['goal'] = $goal->name;
                }
                if ($fitnessLevel) {
                    $messages['fitness_level'] = $fitnessLevel->name;
                }
                // if ($exercisePlanDay) {
                //     $messages['exercise plan days'] = $exercisePlanDay->name;
                // }

                // if ($userMedicalIssues->count() > 0) {
                //     $medIssues = '';
                //     foreach ($userMedicalIssues as $issue) {
                //         $medIssues .= $issue->name . ', ';
                //     }
                //     $messages['medical issues'] = trim($medIssues, ', ');
                // }

                if ($userAllergens->count() > 0) {
                    $allergies = '';
                    foreach ($userAllergens as $alr) {
                        $allergies .= $alr->name . ', ';
                    }
                    // Remove the trailing comma and space
                    $messages['allergies'] = trim($allergies, ', ');
                } elseif (!empty($userobj->other_allergies)) {
                    // Check if other_allergies has a value and assign it
                    $messages['allergies'] = $userobj->other_allergies;
                } else {
                    $messages['allergies'] = 'None';
                }

                // if ($userDietRegimes->count() > 0) {
                //     $dietRegimes = '';
                //     foreach ($userDietRegimes as $dr) {
                //         $dietRegimes .= $dr->title . ', ';
                //     }
                //     $messages['diet regimes'] = trim($dietRegimes, ', ');
                // }

                if ($userPreferences->count() > 0) {
                    $userTypes = '';
                    foreach ($userPreferences as $up) {
                        $userTypes .= $up->name . ', ';
                    }
                    // Remove trailing comma and assign
                    $messages['diet types'] = trim($userTypes, ', ');
                } elseif (!empty($userObj->other_food_preferences)) {
                    // Fallback to other_food_preferences if count is 0
                    $messages['diet types'] = $userObj->other_food_preferences;
                }


                if ($userAppliances->count() > 0) {
                    $appliancesString = '';
                    foreach ($userAppliances as $appliance) {
                        // Access the 'name' property of the Appliance model
                        $appliancesString .= $appliance->name . ', ';
                    }
                    // Remove the trailing comma and space
                    $messages['available_appliances'] = trim($appliancesString, ', ');
                }
                // 1. Use input() to check the VALUE
                if ($request->input('day') <= 1 && $request->has('date')) {
                    Log::info('inside');

                    // 2. Wrap the entire ternary operator in parentheses
                    $messages['date'] = 'requires one day'
                        . ($request->has('issurprisingmeal') ? ' surprising meal' : '') // Added a space
                        . ' meals starting from ' . $request->input('date');
                }

                if ($cuisine) {
                    Log::info('got cusines : ' . print_r($cuisine, true));

                    $messages['preferred_cuisine'] = $cuisine->name;
                }
                if ($cookingTime) {
                    $messages['preferred_cooking_time'] = $cookingTime->name;
                }
                // if ($appliance) {
                //     $messages['available_appliances'] = $appliance->name;
                // }
                if ($mealPrepSchedule) {
                    $messages['meal_prep_schedule'] = $mealPrepSchedule->name;
                }
                if ($mealCount) {
                    $messages['preferred_meal_count'] = $mealCount->name;
                }
                if ($userObj->daily_calorie_intake != null) {
                    $messages['total_kcal_limit'] = $userObj->daily_calorie_intake;
                }

                /**
                 * adding date time stamp to get unique to get  different response for each request
                 */
                $messages['request_timestamp'] = $request->input('date');

                // --- End of your User Details code block ---


                // 3. Call your new private helper function
                // The $messages array *is* the $userDetails
                Log::info('user result is : ' . print_r($messages, true));

                //$result = $this->generateDietPlan($messages, $dayDuration); //old gpt call

                $result = $this->getOrCreateAiResponse( //this check in db if response exist or not 
                    $userObj->id,
                    'diet_plan',
                    $request->prep_type,
                    $isRefresh,
                    $requestDate,
                    function () use ($messages, $dayDuration) {
                        return $this->generateDietPlan($messages, $dayDuration);
                    }
                );




                // 4. Handle the result from the helper and send the HTTP response
                if ($result['status'] === 'success') {
                    // Success: return the data
                    return response()->json(['status' => true, 'data' => $result['data']]);
                } else {
                    // Error: return the error message and status code
                    $statusCode = $result['code'] ?? 500;
                    return response()->json(['status' => false, 'msg' => $result['message']], $statusCode);
                }
            } else {
                return response()->json(['status' => false, 'msg' => 'User not found.'], 404);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Invalid User ID.'], 400);
        }
    }


    /**
     * Private helper function to generate a diet plan from ChatGPT.
     *
     * @param array $userDetails
     * @param int $dayDuration
     * @return array
     */
    private function generateDietPlan(array $userDetails, int $dayDuration): array
    {

        //Log::info('generateDietPlan funtion' . print_r($userDetails, true));

        // 1. Add requested duration to the user details
        $userDetails['requested_plan_duration'] = $dayDuration . ' day(s)';

        //take prev recipes from db
        $userId = $userDetails['user_id'];
        $previousResponses = $this->getLast7DaysAiResponses($userId, 'diet_plan');
        $usedRecipes = $this->extractUsedRecipes($previousResponses);
        //Log::info('Previous Responses:', ['data' => $previousResponses]);
        //Log::info('Used Recipes:', ['data' => $usedRecipes]);


        // Extract preferred meal count from user details (e.g., "3 Meals" -> 3)
        $mealCountStr = $userDetails['preferred_meal_count'] ?? '3';
        preg_match('/\d+/', $mealCountStr, $matches);
        $mealCount = isset($matches[0]) ? (int)$matches[0] : 3;

        // 2. Define the expected JSON structure for the AI
        $sampleGroceryItem = [
            'name' => 'Eggs',
            'amount' => '3',
            'isVegetarian' => true,
        ];

        // Updated sample recipe to include grocery_list specific to the meal
        // Updated sample recipe with DETAILED instructions
        $sampleRecipe = [
            'uniqueKey' => '1_2025-11-03T01:30:00.000000Z',
            'recipeName' => 'Creamy Scrambled Eggs & Whole Wheat Toast',
            'prepTime' => '10 min',
            'calories' => '350 kcal',
            'isVegetarian' => true,
            'recipePoints' => "1. Crack 3 large eggs into a mixing bowl. Add a splash of milk (approx. 1 tbsp), a pinch of salt, and freshly ground black pepper. Whisk vigorously for 45 seconds until the mixture is uniform and airy.\n2. Place a non-stick skillet over medium-low heat. Add 1 tsp of butter and allow it to melt completely until it begins to foam slightly, coating the bottom of the pan.\n3. Pour the egg mixture into the center of the pan. Let it sit undisturbed for about 15 seconds until the edges just begin to set.\n4. Using a silicone spatula, gently push the eggs from the edges toward the center, forming large, soft curds. Repeat this folding motion for 2-3 minutes. Remove from heat while the eggs are still slightly glossy and moist (they will finish cooking on the plate).\n5. While the eggs are cooking, toast 2 slices of whole-wheat bread in a toaster or under the broiler until golden brown and crisp.\n6. Plate the scrambled eggs immediately alongside the hot toast. Serve hot.",
            'grocery_list' => [
                $sampleGroceryItem,
                ['name' => 'Whole-wheat toast', 'amount' => '2 slices', 'isVegetarian' => true],
                ['name' => 'Butter', 'amount' => '1 tsp', 'isVegetarian' => true],
                // You might want to add Milk here if it's not in the main sample item
                ['name' => 'Milk', 'amount' => '1 tbsp', 'isVegetarian' => true],
            ]
        ];

        // Build the meals structure based on meal count
        $mealsStructure = [];
        if ($mealCount == 2) {
            $mealsStructure = [
                'Lunch' => $sampleRecipe,
                'Dinner' => $sampleRecipe,
            ];
        } elseif ($mealCount == 4) {
            $mealsStructure = [
                'Breakfast' => $sampleRecipe,
                'Lunch' => $sampleRecipe,
                'Evening Snack' => $sampleRecipe,
                'Dinner' => $sampleRecipe,
            ];
        } else {
            // Default to 3 meals (Breakfast, Lunch, Dinner)
            $mealsStructure = [
                'Breakfast' => $sampleRecipe,
                'Lunch' => $sampleRecipe,
                'Dinner' => $sampleRecipe,
            ];
        }

        $sampleDay = [
            'day' => 'Day 1',
            'meals' => $mealsStructure,
            'daily_summary' => [
                'total_calories' => 'Approx 2000 kcal',
                'protein' => 'Approx 150g',
            ],
            // Removed consolidated 'grocery_list' from here
        ];

        $expectedStructure = [
            'diet_plan' => [
                $sampleDay,
            ]
        ];

        // 3. Create the system message to guide the AI
        // Updated instructions to require grocery list per meal
        $systemMessage = "You are a professional dietitian.

        IMPORTANT RULES:
        - The diet plan MUST be different from previous days.
        - DO NOT repeat recipe names, meal structures, or main ingredients from the recent history.
        - Prefer new cuisines, cooking styles, and protein sources.
        - Keep nutrition balanced.
        - The TOTAL calories of all generated recipes MUST STRICTLY MATCH the target of daily_calorie_intake of the user.
        - Recipe instructions must be detailed and step-by-step. Include specific heat levels (e.g., 'medium-high'), visual cues (e.g., 'until golden brown'), and techniques (e.g., 'whisk vigorously')
        - Return RAW JSON only.
        - Do NOT wrap the response in markdown or code fences.
        - Do NOT add explanations before or after the JSON.

        Previously used recipes (DO NOT REPEAT):
        " . json_encode($usedRecipes) . "

        The request time is " . now()->toDateTimeString() . ".

        Respond ONLY with valid JSON in the following format:
        \n\n" . json_encode($expectedStructure, JSON_PRETTY_PRINT);


        Log::info('system messege is : ' . print_r($systemMessage, true));


        // 4. Prepare the user input message
        $userInputMessage = "Here is my profile. Please generate a diet plan for me.\n\n"
            . json_encode($userDetails, JSON_PRETTY_PRINT);

        // Previous OpenAI request kept commented for reference.
        // $response = Http::timeout(120)
        //     ->withHeaders([
        //         'Authorization' => 'Bearer ' . $apiKey,
        //     ])->post('https://api.openai.com/v1/chat/completions', [...]);

        $maxTokens = max(4096, min(12000, $dayDuration * 5000));
        $response = AnthropicMessageClient::send(
            $systemMessage,
            $userInputMessage,
            $maxTokens,
            ['timeout' => 180]
        );

        // 7. Handle the Claude API response
        if ($response['status'] === 'success') {
            $content = $response['content'];
            $decodedContent = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $message = ($response['stop_reason'] ?? null) === 'max_tokens'
                    ? 'Claude response was cut off because it hit the output token limit. Please retry with fewer days or a higher token budget.'
                    : 'Invalid JSON response from AI';

                return ['status' => 'error', 'message' => $message, 'raw_response' => $content, 'code' => 500];
            }

            // Loop through and add our own unique keys
            if (isset($decodedContent['diet_plan']) && is_array($decodedContent['diet_plan'])) {
                $dayIndex = 1;
                foreach ($decodedContent['diet_plan'] as &$day) {
                    $date = new \DateTime(); // Use \DateTime for global namespace
                    if (isset($day['meals']) && is_array($day['meals'])) {
                        $mealIndex = 1;
                        foreach ($day['meals'] as &$recipe) {
                            if (is_array($recipe)) {
                                $recipe['uniqueKey'] = $dayIndex . '_' . $mealIndex . '_' . $date->format('c');
                                $mealIndex++;
                            }
                        }
                    }
                    $dayIndex++;
                }
            }

            // Return the data array on success
            return ['status' => 'success', 'data' => $decodedContent];
        } else {
            return [
                'status' => 'error',
                'message' => $response['message'] ?? 'Failed to connect to Claude.',
                'code' => $response['code'] ?? 500
            ];
        }
    }
    /**
     * API endpoint to generate a single surprise meal based ONLY on available appliances.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function getSurpriseMeal(Request $request)
    {
        Log::info('Surprise Meal Request received');

        $isRefresh = $request->isrefresh;
        $requestDate = $request->date;

        // 1. Identify the user
        $userId = (isset($request->user_id) && $request->user_id > 0) ? $request->user_id : 0;

        if ($userId > 0) {
            $userObj = User::find($userId);

            if ($userObj) {
                // 2. Fetch ONLY available appliances
                $userAppliances = $userObj->appliances;

                $messages = [];

                $messages['user_id'] = $userObj->id;

                // Construct appliances string
                if ($userAppliances->count() > 0) {
                    $appliancesString = $userAppliances->pluck('name')->implode(', ');
                    $messages['available_appliances'] = $appliancesString;
                } else {
                    $messages['available_appliances'] = 'No specific appliances listed (assume basic kitchen tools)';
                }

                // 3. Add Timestamp to the user message data
                $messages['request_timestamp'] = now()->toDateTimeString();

                // 4. Add a random seed to force unique response from AI
                $messages['random_seed'] = uniqid('surprise_', true);

                // 5. Call the helper function to generate the meal
                //$result = $this->generateSurpriseMeal($messages);
                $result = $this->getOrCreateAiResponse(
                    $userObj->id,
                    'surprise_meal',
                    $request->prep_type,
                    $isRefresh,
                    $requestDate,
                    function () use ($messages) {
                        return $this->generateSurpriseMeal($messages);
                    }
                );


                // 6. Return response
                if ($result['status'] === 'success') {
                    return response()->json(['status' => true, 'data' => $result['data']]);
                } else {
                    $statusCode = $result['code'] ?? 500;
                    return response()->json(['status' => false, 'msg' => $result['message']], $statusCode);
                }
            } else {
                return response()->json(['status' => false, 'msg' => 'User not found.'], 404);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Invalid User ID.'], 400);
        }
    }

    private function generateSurpriseMeal(array $userDetails): array
    {
        Log::info('generateSurpriseMeal function ' . print_r($userDetails, true));

        $userId = $userDetails['user_id'];
        $previousResponses = $this->getLast7DaysAiResponses($userId, 'surprise_meal');
        $usedRecipes = $this->extractUsedRecipes($previousResponses);
        //Log::info('Previous Responses:', ['data' => $previousResponses]);
        //Log::info('Used Recipes:', ['data' => $usedRecipes]);



        // 1. Define the expected JSON structure for a SINGLE meal, matching the user's requested format
        $sampleRecipe = [
            'uniqueKey' => '1_1_' . now()->format('c'),
            'recipeName' => 'Pan-Seared Chicken with Rice',
            'prepTime' => '20 min',
            'calories' => '450 kcal',
            'isVegetarian' => false,
            'recipePoints' => "1. Heat the pan on the stove.\n2. Season the chicken and cook for 6-7 minutes per side.\n3. Serve with steamed rice.",
        ];

        $sampleGroceryItem = [
            'name' => 'Chicken Breast',
            'amount' => '200g',
            'isVegetarian' => false,
        ];

        $expectedStructure = [
            'diet_plan' => [
                [
                    'day' => 'Surprise Meal',
                    'meals' => [
                        'Surprise Meal' => $sampleRecipe
                    ],
                    'daily_summary' => [
                        'total_calories' => 'Approx 450 kcal',
                        'protein' => 'Approx 30g',
                    ],
                    'grocery_list' => [
                        $sampleGroceryItem,
                        ['name' => 'Rice', 'amount' => '1 cup', 'isVegetarian' => true],
                    ]
                ]
            ]
        ];

        // 2. Create the system message to guide the AI
        $systemMessage = "You are a creative chef.

                        STRICT RULES:
                        - Generate a completely NEW surprise meal.
                        - DO NOT repeat any previous recipes, proteins, or cooking methods.
                        - Prefer unusual but practical combinations.
                        - Respect available appliances only.
                                                
                        Previously generated surprise meals (AVOID):
                        " . json_encode($usedRecipes) . "
                                                
                        System timestamp: " . now()->toDateTimeString() . "
                                                
                        Respond ONLY with valid JSON in the following format:
                        \n\n" . json_encode($expectedStructure, JSON_PRETTY_PRINT);


        // 3. Prepare the user input message
        $userInputMessage = "Here are my available appliances. Please generate a surprise meal recipe. Random Seed: " . $userDetails['random_seed'] . ". Request Timestamp: " . $userDetails['request_timestamp'] . "\n\n"
            . json_encode($userDetails, JSON_PRETTY_PRINT);

        // Previous OpenAI request kept commented for reference.
        // $response = Http::timeout(120)
        //     ->withHeaders([
        //         'Authorization' => 'Bearer ' . $apiKey,
        //     ])->post('https://api.openai.com/v1/chat/completions', [...]);

        $response = AnthropicMessageClient::send(
            $systemMessage,
            $userInputMessage,
            1500,
            [],
            ['temperature' => 1.0]
        );

        // 6. Handle the Claude API response
        if ($response['status'] === 'success') {
            $content = $response['content'];
            $decodedContent = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['status' => 'error', 'message' => 'Invalid JSON response from AI', 'raw_response' => $content, 'code' => 500];
            }

            // Add unique keys if needed, though the prompt asks for it
            if (isset($decodedContent['diet_plan'][0]['meals']['Surprise Meal'])) {
                $decodedContent['diet_plan'][0]['meals']['Surprise Meal']['uniqueKey'] = 'surprise_' . uniqid() . '_' . now()->format('c');
            }

            return ['status' => 'success', 'data' => $decodedContent];
        } else {
            return [
                'status' => 'error',
                'message' => $response['message'] ?? 'Failed to connect to Claude.',
                'code' => $response['code'] ?? 500
            ];
        }
    }
    //this funtion check if response already exist or not and also store it
private function getOrCreateAiResponse(
    string $userId,
    string $type,
    string $preparationType,
    int $isRefresh,
    string $requestDate,
    callable $gptCallback
) {

    $formattedDate = Carbon::parse($requestDate)->format('Y-m-d');

    // Get existing ACTIVE recipes
    $existingRecipes = Recipe::where('userid', $userId)
        ->where('date', $formattedDate)
        ->where('type', $type)
        ->where('preparation_type', $preparationType)
        ->where('isDeleted', 0)
        ->where('is_active', 1)
        ->get();

    // If recipes exist and no refresh → return active version
    if ($existingRecipes->count() > 0 && $isRefresh == 0) {

        Log::info("Serving recipes from DB");

        return [
            'status' => 'success',
            'data'   => $this->combineMeals($existingRecipes)
        ];
    }

    /*
    |--------------------------------------------------------------------------
    |  REFRESH LOGIC - Version Handling
    |--------------------------------------------------------------------------
    */

    $newVersion = 1;

    if ($isRefresh == 1 && $existingRecipes->count() > 0) {

        // Get current max version
        $latestVersion = Recipe::where('userid', $userId)
            ->where('date', $formattedDate)
            ->where('type', $type)
            ->where('preparation_type', $preparationType)
            ->max('version');

        $newVersion = $latestVersion ? $latestVersion + 1 : 1;

        // Deactivate previous active recipes
        Recipe::where('userid', $userId)
            ->where('date', $formattedDate)
            ->where('type', $type)
            ->where('preparation_type', $preparationType)
            ->where('isDeleted', 0)
            ->where('is_active', 1)
            ->update([
                'is_active' => 0
            ]);
    }

    // Call GPT
    Log::info("Calling GPT for new recipes");

    $gptRawResponse = $gptCallback();

    Log::info('GPT Response:', $gptRawResponse);

    if (($gptRawResponse['status'] ?? 'error') !== 'success') {
        return [
            'status' => 'error',
            'message' => $gptRawResponse['message'] ?? 'GPT request failed',
            'code' => $gptRawResponse['code'] ?? 500,
        ];
    }

    $gptResponse = $gptRawResponse['data'] ?? null;

    if (!$gptResponse || !isset($gptResponse['diet_plan'])) {
        return [
            'status'  => 'error',
            'message' => 'Invalid GPT response'
        ];
    }

    // Store meals (pass version + active flag)
    $this->storeMealsFromResponse(
        $gptResponse,
        $userId,
        $formattedDate,
        $type,
        $preparationType,
        $newVersion,      // pass version
        1                 // is_active = 1
    );

    // Fetch newly stored ACTIVE recipes
    $newRecipes = Recipe::where('userid', $userId)
        ->where('date', $formattedDate)
        ->where('type', $type)
        ->where('preparation_type', $preparationType)
        ->where('isDeleted', 0)
        ->where('is_active', 1)
        ->get();

    return [
        'status' => 'success',
        'data'   => $this->combineMeals($newRecipes)
    ];
}




    private function getLast7DaysAiResponses(int $userId, string $type): array
    {
        return AiResponse::where('user_id', $userId)
            ->where('type', $type)
            ->whereDate('response_date', '>=', Carbon::now()->subDays(7))
            ->orderBy('response_date', 'desc')
            ->limit(7)
            ->pluck('response_json')
            ->toArray();
    }

    private function extractUsedRecipes(array $previousResponses): array
    {
        $recipes = [];

        foreach ($previousResponses as $response) {

            // If JSON string, decode first
            if (is_string($response)) {
                $response = json_decode($response, true);
            }

            if (!isset($response['data']['diet_plan'])) continue;

            foreach ($response['data']['diet_plan'] as $day) {

                if (!isset($day['meals'])) continue;

                foreach ($day['meals'] as $meal) {

                    if (isset($meal['recipeName'])) {
                        $recipes[] = $meal['recipeName'];
                    }
                }
            }
        }

        return array_values(array_unique($recipes));
    }



private function storeMealsFromResponse(
    array $response,
    string $userId,
    string $date,
    string $type,
    string $preparationType,
    int $newVersion,
    bool $is_active
) {

    foreach ($response['diet_plan'] as $dayPlan) {

        if (!isset($dayPlan['meals'])) continue;

        foreach ($dayPlan['meals'] as $mealKey => $mealData) {

            // Normalize meal type (Surprise Meal → SURPRISE_MEAL)
            $mealType = strtoupper(str_replace(' ', '_', trim($mealKey)));

            if (!in_array($mealType, [
                'BREAKFAST',
                'LUNCH',
                'SNACKS',
                'DINNER',
                'SURPRISE_MEAL'
            ])) {
                continue;
            }

            Recipe::create([
                'userid'           => $userId,
                'date'             => $date,
                'meal_type'        => $mealType,
                'preparation_type' => $preparationType,
                'type'             => $type,
                'version'          => $newVersion,
                'is_active'        => $is_active,
                'recipeName'       => $mealData['recipeName'] ?? null,
                'prepTime'         => $mealData['prepTime'] ?? null,
                'calories'         => $mealData['calories'] ?? null,
                'isVegetarian'     => $mealData['isVegetarian'] ?? false,
                'recipePoints'     => $mealData['recipePoints'] ?? null,

                // Handle grocery_list both inside meal and outside
                'grocery_list'     => $mealData['grocery_list']
                                      ?? $dayPlan['grocery_list']
                                      ?? [],

                'isFavorate'       => 0,
                'isDeleted'        => 0,
                'createDate'       => now(),
                'modifyDate'       => now(),
            ]);
        }
    }
}



private function combineMeals($recipes)
{
    if ($recipes->isEmpty()) {
        return [
            'diet_plan' => []
        ];
    }

    $meals = [];
    $date = null;

    foreach ($recipes as $recipe) {

        $date = $recipe->date;

        $mealKey = ucfirst(strtolower(str_replace('_', ' ', $recipe->meal_type)));

        $meals[$mealKey] = [
            'recipeName'   => $recipe->recipeName,
            'prepTime'     => $recipe->prepTime,
            'calories'     => $recipe->calories,
            'isVegetarian' => (bool) $recipe->isVegetarian,
            'recipePoints' => $recipe->recipePoints,
            'grocery_list' => $recipe->grocery_list ?? [],
            'isFavorate'   => (bool) $recipe->isFavorate
        ];
    }

    return [
        'diet_plan' => [
            [
                'day'   => $date ? \Carbon\Carbon::parse($date)->format('l') : null,
                'meals' => $meals
            ]
        ]
    ];
}

}
