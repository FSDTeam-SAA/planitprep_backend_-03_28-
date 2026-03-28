<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Http\Resources\MembershipCollection;
use App\Http\Resources\PaymentCollection;
use App\Jobs\UserDietPlan;
use App\Models\City;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\CouponUser;
use App\Models\ExercisePlanDay;
use App\Models\FitnessLevel;
use App\Models\FoodItemReplacement;
use App\Models\Goal;
use App\Models\Membership;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SimilarFoodItem;
use App\Models\State;
use App\Models\User;
use App\Models\UserAllergies;
use App\Models\UserDietRegime;
use App\Models\UserGoal;
use App\Models\UserImage;
use App\Models\UserMealPlan;
use App\Models\UserMedicalIssue;
use App\Models\UserMembership;
use App\Models\UserPreference;
use App\Models\UserTestReport;
use App\Models\WeightTracking;
use App\Models\AiResponse;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\AnthropicMessageClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    public function updateUserInformation(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'full_name' => 'nullable',
                'gender' => 'nullable',
                'age' => 'nullable',
                'height' => 'nullable',
                'weight' => 'nullable',
                'country_id' => 'nullable',
                'state_id' => 'nullable',
                'city_id' => 'nullable',
                'fitness_level_id' => 'nullable|exists:fitness_levels,id',
                'exercise_plan_day_id' => 'nullable|exists:exercise_plan_days,id',
                'goal_id' => 'nullable|exists:goals,id',
                'cooking_prep_id' => 'nullable|exists:cooking_preps,id',
                'daily_calorie_intake' => 'nullable',
                'cuisine_id' => 'nullable|exists:cuisines,id',
                'cooking_time_id' => 'nullable|exists:cooking_time,id',
                'meal_prep_schedule_id' => 'nullable|exists:meal_prep_schedules,id',
                'meal_count_id' => 'nullable|exists:meal_counts,id',
                'appliance_ids' => 'nullable',
                'other_food_preferences' => 'nullable',
                'other_allergies' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {
                $user = Auth::user();
                $user_id = $user->id;
                $username = 'DT100' . $user_id;
                $user = User::find($user_id);

                // You don't need User::find($user_id) again, $user is already the model
                $user->username = $username;
                $user->full_name = $request->full_name ? $request->full_name : '';
                $user->gender = $request->gender ? $request->gender : 'M';
                $user->age = $request->age ? $request->age : 0;
                $user->height = $request->height ? $request->height : 0;
                $user->weight = $request->weight ? $request->weight : 0;
                $user->country_id = $request->country_id ? $request->country_id : 0;
                $user->state_id = $request->state_id ? $request->state_id : 0;
                $user->city_id = $request->city_id ? $request->city_id : 0;
                $user->fitness_level_id = $request->fitness_level_id ? $request->fitness_level_id : 0;
                $user->exercise_plan_day_id = $request->exercise_plan_day_id ? $request->exercise_plan_day_id : 0;
                $user->goal_id = $request->goal_id ? $request->goal_id : 0;
                $user->cooking_prep_id = $request->filled('cooking_prep_id') ? $request->cooking_prep_id : null;
                $user->daily_calorie_intake = $request->daily_calorie_intake ? $request->daily_calorie_intake : 1800;

                $user->cuisine_id = $request->filled('cuisine_id') ? $request->cuisine_id : null;
                $user->cooking_time_id = $request->filled('cooking_time_id') ? $request->cooking_time_id : null;

                $user->meal_prep_schedule_id = $request->filled('meal_prep_schedule_id') ? $request->meal_prep_schedule_id : null;
                $user->meal_count_id = $request->filled('meal_count_id') ? $request->meal_count_id : null;

                $user->other_food_preferences = $request->other_food_preferences ? $request->other_food_preferences : null;
                $user->other_allergies = $request->other_allergies ? $request->other_allergies : null;



                $user->save();
                Log::info('user saved into db');

                $userGoal = new UserGoal;
                $userGoal->user_id = $user_id;
                $userGoal->goal_id = $user->goal_id;
                $userGoal->current_value = $request->weight ? $request->weight : 0;
                $userGoal->target_value = $request->target_value ? $request->target_value : 0;
                $userGoal->start_date = date('Y-m-d');
                $userGoal->end_date = date('Y-m-d', strtotime('+2 months'));
                $userGoal->save();

                $weightTracking = new WeightTracking;
                $weightTracking->user_id = $user_id;
                $weightTracking->current_value = $request->weight ? $request->weight : 0;
                $weightTracking->target_value = $request->target_value ? $request->target_value : 0;
                $weightTracking->save();

                if (isset($request->medical_issue_ids) && $request->medical_issue_ids != '') {
                    UserMedicalIssue::where('user_id', $user_id)->delete();
                    $medIssueIds = explode(',', $request->medical_issue_ids);
                    if (count($medIssueIds) > 0) {
                        $medIds = [];
                        foreach ($medIssueIds as $key => $value) {
                            $medIds[$key]['medical_issue_id'] = $value;
                            $medIds[$key]['user_id'] = $user_id;
                        }
                        UserMedicalIssue::insert($medIds);
                    }
                }

                if (isset($request->allergy_ids) && $request->allergy_ids != '') {
                    UserAllergies::where('user_id', $user_id)->delete();
                    $allergyIds = explode(',', $request->allergy_ids);
                    if (count($allergyIds) > 0) {
                        $alIds = [];
                        foreach ($allergyIds as $key => $value) {
                            $alIds[$key]['allergy_id'] = $value;
                            $alIds[$key]['user_id'] = $user_id;
                        }
                        UserAllergies::insert($alIds);
                        Log::info('user allergy_ids db');
                    }
                }

                if (isset($request->user_pref_id) && $request->user_pref_id != '') {
                    UserPreference::where('user_id', $user_id)->delete();
                    $pref = new UserPreference;
                    $pref->diet_type_id = $request->user_pref_id;
                    $pref->user_id = $user_id;
                    $pref->save();
                    Log::info('user user_pref_id db');
                }

                if (isset($request->regime_ids) && $request->regime_ids != '') {
                    UserDietRegime::where('user_id', $user_id)->delete();
                    $regimeIds = explode(',', $request->regime_ids);

                    if (count($regimeIds) > 0) {
                        $regIds = [];
                        foreach ($regimeIds as $key => $value) {
                            $regIds[$key]['diet_regime_id'] = $value;
                            $regIds[$key]['user_id'] = $user_id;
                        }
                        UserDietRegime::insert($regIds);
                        Log::info('user regime_ids db');
                    }
                }

                if (isset($request->appliance_ids) && $request->appliance_ids != '') {
                    // Turn "1,2,3" string into an array [1, 2, 3]
                    $appIds = explode(',', $request->appliance_ids);

                    // 'sync' automatically handles deleting old ones and adding new ones
                    $user->appliances()->sync($appIds);
                    Log::info('user appliance_ids db');
                }

                // if ($user->country_id > 0 && $user->state_id > 0 && $user->city_id > 0) {
                //     UserDietPlan::dispatch($user_id);
                // }
                // $user = $this->userResponse($user_id);

                // $this->getAveragDailyDietGoals($user_id);

                $setting = Setting::first();
                $enable_free_membership = $setting->enable_free_membership;
                Log::info('getting user settings');


                if ($enable_free_membership == 1) {
                    $membership = Membership::where('price', 0)->first();
                    Log::info('user free membership is available');
                } else {
                    $membership = Membership::find($request->membership_id);
                    Log::info('finding user membership');
                }
                if ($membership) {
                    Log::info('user membership is ');
                    $this->buyUserMembership($membership, $request);
                    Log::info('buy user membership');
                }

                /**
                 * as user profile changes , then the previous meal plans for the user will not be relevant as the user details are changed, so we need to delete the previous meal plans and generate new meal plans as per the updated user profile details, so deleting the previous meal plans
                 */
                // AiResponse::where('user_id', $user_id)
                //     ->where('response_date', '>=', now()->toDateString())
                //     ->delete();

                // Log::info('Current and future AI responses deleted');

                Log::info('Future AI responses deleted');

                Log::info('all calls are successfull, returning user ');


                return response()->json(['status' => true, 'msg' => 'Information Updated!', 'data' => $user]);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    // generate diet plan
    public function generateUserDietPlan(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'country_id' => 'nullable',
                'state_id' => 'nullable',
                'city_id' => 'nullable',
                'phone' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {
                $user = Auth::user();
                $user_id = $user->id;
                $user = User::find($user_id);
                $user->country_id = $request->country_id ? $request->country_id : 0;
                $user->state_id = $request->state_id ? $request->state_id : 0;
                $user->city_id = $request->city_id ? $request->city_id : 0;
                $user->phone = $request->phone;
                $user->save();

                UserDietPlan::dispatch($user_id);

                return response()->json(['status' => true, 'msg' => 'Your diet plan is being generated!']);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    // public function getAveragDailyDietGoals($userId)
    public function getAveragDailyDietGoals()
    {
        // if (\Auth::user()) {
        $apiKey = env('OPENAI_API_KEY'); // Ensure this matches the key in your .env file
        // $user = \Auth::user();
        // $userId = $user->id;
        $userId = 37;

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

                $messages = "I am providing you user's details for which I need a particular average daily requirements of calories in KCal and carbs,fiber,fats, proteins in grams and daily water requirement in litres as per the goal to achieved in 1-2 months time frame provided in the user's details like current weight and target weight.";
                $messages = 'weight=' . $userObj->weight . ',';
                if ($userGoalObj) {
                    $messages .= 'target weight=' . $userGoalObj->target_value . ',';
                }
                if ($cityObj) {
                    $messages .= 'city=' . $cityObj->name . ',';
                }
                if ($stateObj) {
                    $messages .= 'state=' . $stateObj->name . ',';
                }
                if ($countryObj) {
                    $messages .= 'country=' . $countryObj->name . ',';
                }
                $messages .= 'height=' . $userObj->height . ',';
                $messages .= 'gender=' . $userObj->gender == 'M' ? 'Male' : 'Women';
                $messages .= 'age=' . $userObj->age;
                if ($goal) {
                    $messages .= 'goal=' . $goal->name . ',';
                }
                if ($fitnessLevel) {
                    $messages .= 'fitness level=' . $fitnessLevel->name . ',';
                }
                if ($exercisePlanDay) {
                    $messages .= 'weekly exercise days=' . $exercisePlanDay->name . ',';
                }
                if ($userMedicalIssues->count() > 0) {
                    $medIssues = '';
                    foreach ($userMedicalIssues as $issue) {
                        $medIssues .= $issue->name . ', ';
                    }
                    $messages .= "medical issues='" . trim($medIssues, ', ') . "',";
                }

                if ($userAllergens->count() > 0) {
                    $allergies = '';
                    foreach ($userAllergens as $alr) {
                        $allergies .= $alr->name . ', ';
                    }
                    $messages .= "allergies='" . trim($allergies, ', ') . "',";
                }

                if ($userDietRegimes->count() > 0) {
                    $dietRegimes = '';
                    foreach ($userDietRegimes as $dr) {
                        $dietRegimes .= $dr->title . ', ';
                    }
                    $messages .= "diet regimes followed earlier='" . trim($dietRegimes, ', ') . "',";
                }

                if ($userPreferences->count() > 0) {
                    $userTypes = '';
                    foreach ($userPreferences as $up) {
                        $userTypes .= $up->name . ', ';
                    }
                    $messages .= "diet types='" . trim($userTypes, ', ') . "',";
                }
                $expectedStructure = [
                    'daily_calorie_intake' => 2800,
                    'daily_fat_intake' => 80.00,
                    'daily_protein_intake' => 150.0,
                    'daily_carb_intake' => 200.0,
                    'daily_fiber_intake' => 18.0,
                    'daily_water_intake' => 3.0,
                ];
                // Define your desired structured response
                $desiredResponseStructure = [
                    'status' => 'success',
                    'message' => 'Response retrieved successfully',
                    'data' => $expectedStructure,
                    'timestamp' => now()->toDateTimeString(),
                ];

                // Create a system message to guide the model
                $systemMessage = "Please provide the required daily average information as per the following JSON format:\n" . json_encode($desiredResponseStructure);

                // Prepare the user input message
                $userInputMessage = $messages;

                // Previous OpenAI request kept commented for reference.
                // $response = Http::withHeaders([
                //     'Authorization' => 'Bearer ' . $apiKey,
                // ])->post('https://api.openai.com/v1/chat/completions', [...]);

                $response = AnthropicMessageClient::send($systemMessage, $userInputMessage, 1500);

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
                        // dd($desiredResponseStructure['data']);
                        $userObj->daily_calorie_intake = $desiredData['daily_calorie_intake'];
                        $userObj->daily_fats_intake = $desiredData['daily_fat_intake'];
                        $userObj->daily_protein_intake = $desiredData['daily_protein_intake'];
                        $userObj->daily_carbs_intake = $desiredData['daily_carb_intake'];
                        $userObj->daily_fiber_intake = $desiredData['daily_fiber_intake'];
                        $userObj->daily_water = $desiredData['daily_water_intake'];
                        $userObj->save();
                        // dd($desiredData);

                    }

                    // Return the structured response
                    // return response()->json($desiredResponseStructure);
                } else {
                    // Handle API request failure
                    // $error = $response->json();
                    // return response()->json([
                    //     'status' => 'error',
                    //     'message' => $error['error']['message'],
                    //     'data' => null,
                    //     'timestamp' => now()->toDateTimeString()
                    // ]);
                }
            } else {
            }
        } else {
        }
        // }
        // else {
        //       return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        // }
    }

    public function testDietPlanApi(Request $request)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $user_id = $user->id;

            UserDietPlan::dispatch($user_id);

            return response()->json(['status' => true, 'msg' => 'Diet Plan Added']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function addGoal(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'current_value' => 'nullable',
                'target_value' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {

                $user = Auth::user();
                $user_id = $user->id;

                $userGoal = new UserGoal;
                $userGoal->user_id = $user_id;
                $userGoal->goal_id = $user->goal_id;
                $userGoal->current_value = $request->current_value;
                $userGoal->target_value = $request->target_value;
                $userGoal->start_date = date('Y-m-d');
                $userGoal->end_date = date('Y-m-d', strtotime('+2 months'));
                $userGoal->status = $request->current_value == $request->target_value ? 'C' : 'A';
                $userGoal->save();

                $userObj = User::find($user_id);
                $userObj->weight = $request->current_value ? $request->current_value : 0;
                $userObj->save();

                return response()->json(['status' => true, 'msg' => 'Goal added!']);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function updateCurrentWeight(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'current_value' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {

                $user = Auth::user();
                $user_id = $user->id;

                $latestWeightTrackingData = WeightTracking::where('user_id', $user_id)->orderBy('id', 'desc')->first();
                if ($latestWeightTrackingData) {
                    $weightTracking = new WeightTracking;
                    $weightTracking->user_id = $user_id;
                    $weightTracking->current_value = $request->current_value;
                    $weightTracking->target_value = $latestWeightTrackingData->target_value;
                    $weightTracking->save();
                }

                $userObj = User::find($user_id);
                $userObj->weight = $request->current_value ? $request->current_value : 0;
                $userObj->save();

                return response()->json(['status' => true, 'msg' => 'Weight updated!']);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function updateTargetWeight(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'current_value' => 'required',
                'target_value' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {

                $user = Auth::user();
                $user_id = $user->id;

                $weightTracking = new WeightTracking;
                $weightTracking->user_id = $user_id;
                $weightTracking->current_value = $request->current_value;
                $weightTracking->target_value = $request->target_value;
                $weightTracking->save();

                $userObj = User::find($user_id);
                $userObj->weight = $request->current_value ? $request->current_value : 0;
                $userObj->save();

                return response()->json(['status' => true, 'msg' => 'Weight updated!']);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function fetchWeightHistory(Request $request)
    {
        if (Auth::user()) {

            $user = Auth::user();
            $user_id = $user->id;

            $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : date('Y-m-d', strtotime('-7 day'));
            $end_date = $request->end_date ? date('Y-m-d', strtotime('1 day', strtotime($request->end_date))) : date('Y-m-d', strtotime('1 day'));
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod(new DateTime($start_date), $interval, new DateTime($end_date));
            $data = [];
            $weightTrackingDataTemp = new WeightTracking;
            foreach ($period as $dt) {
                $weightTrackingData = WeightTracking::where('user_id', $user_id)->where(\DB::raw('DATE(created_at)'), $dt->format('Y-m-d'))->orderBy('id', 'desc')->first();
                if ($weightTrackingData) {
                    $weightTrackingDataTemp = $weightTrackingData;
                    $data[] = [
                        'date' => $dt->format('Y-m-d'),
                        'current_weight' => $weightTrackingData->current_value,
                        'target_weight' => $weightTrackingData->target_value,
                    ];
                } else {
                    if ($weightTrackingDataTemp && $weightTrackingDataTemp->current_value != null) {
                        $data[] = [
                            'date' => $dt->format('Y-m-d'),
                            'current_weight' => $weightTrackingDataTemp->current_value,
                            'target_weight' => $weightTrackingDataTemp->target_value,
                        ];
                    } else {
                        $latestWeightTrackingData = WeightTracking::where('user_id', $user_id)->where(\DB::raw('DATE(created_at)'), '<', $start_date)->orderBy('id', 'desc')->first();
                        if ($latestWeightTrackingData) {
                            $data[] = [
                                'date' => $dt->format('Y-m-d'),
                                'current_weight' => $latestWeightTrackingData->current_value,
                                'target_weight' => $latestWeightTrackingData->target_value,
                            ];
                        }
                    }
                }
            }

            return response()->json(['status' => true, 'data' => $data], 200, [], JSON_NUMERIC_CHECK);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function userResponse($user_id)
    {
        $user = User::find($user_id);
        if ($user->image != '') {
            $user->image = asset(Storage::url('public/uploads/users/' . $user->id . '/' . $user->image));
        } else {
            $user->image = asset('assets/images/defaultUser.png');
        }
        $membership = UserMembership::where('user_id', $user_id)->orderBy('id', 'desc')->first();
        $user->membership_start_date = '';
        $user->membership_end_date = '';
        if ($membership) {
            $user->membership_start_date = $membership->start_date;
            $user->membership_end_date = $membership->end_date;
        }

        return $user;
    }

    public function updateFcmToken(Request $request)
    {
        if (Auth::user()) {
            $user_id = Auth()->user()->id;
            $user = User::find($user_id);
            $user->fcm_token = $request->fcm_token;
            $user->save();

            $response = ['status' => true, 'msg' => 'Fcm token updated successfully.'];
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Unauthorized user!',
            ]);
        }

        return response()->json($response);
    }

    public function buyMembership(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'membership_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {
                $setting = Setting::first();
                $enable_free_membership = $setting->enable_free_membership;

                if ($enable_free_membership == 1) {
                    $membership = Membership::where('price', 0)->first();
                } else {
                    $membership = Membership::find($request->membership_id);
                }

                if ($membership) {

                    return $this->buyUserMembership($membership, $request);
                }
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function buyUserMembership($membership, $request)
    {
        $setting = Setting::first();
        $enable_free_membership = $setting->enable_free_membership;
        $free_upto = $setting->free_upto;
        $user = Auth::user();

        $user_id = $user->id;
        $date = date('Y-m-d');
        $userMembership = new UserMembership;
        $userMembership->user_id = $user_id;
        $userMembership->membership_id = $membership->id;
        $userMembership->start_date = $date;
        if ($enable_free_membership == 1) {
            $userMembership->end_date = $free_upto;
        } else {
            $userMembership->end_date = date('Y-m-d', strtotime($date . ' +' . $membership->month . ' month'));
        }

        $userMembership->active = ($request->status == 'S') ? 1 : 0;
        $userMembership->save();
        $userMembershipId = $userMembership->id;

        $coupon_id = 0;
        $discount_amount = 0;
        if ($request->coupon_id) {
            $coupon_id = $request->coupon_id;
            $amount = $membership->discounted_price;
            $coupon = Coupon::find($coupon_id);
            if ($coupon->discount_type == 'flat') {
                $discount_amount = $coupon->amount;
            } elseif ($coupon->discount_type == 'percent') {
                $discount_amount = $amount * ($coupon->amount / 100);
            }
        }

        $payment = new Payment;
        $payment->user_membership_id = $userMembershipId;
        $payment->payment_method = ($request->payment_method) ? $request->payment_method : '';
        $payment->transaction_id = isset($request->transaction_id) ? $request->transaction_id : '';
        $payment->response = isset($request->response) ? $request->response : '';
        $payment->price = $membership->discounted_price;
        $payment->user_id = $user_id;
        $payment->status = ($request->status) ? $request->status : 'S';
        $payment->coupon_id = $coupon_id;
        $payment->discount_amount = $discount_amount;
        $payment->save();

        return response()->json(['status' => true, 'msg' => 'Success.']);
    }

    public function replaceFoodItem(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'food_item_id' => 'required',
                'replace_food_item_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {
                $user = Auth::user();
                $user_id = $user->id;
                $mealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();
                $food_item_id = $request->food_item_id;
                $replace_food_item_id = $request->replace_food_item_id;
                $similerFood = SimilarFoodItem::where('food_item_id', $food_item_id)
                    ->where('similar_item_id', $replace_food_item_id)
                    ->first();
                if ($mealPlan && $similerFood) {
                    $replacement = new FoodItemReplacement;
                    $replacement->user_id = $user_id;
                    $replacement->food_item_id = $request->food_item_id;
                    $replacement->replace_food_item_id = $request->replace_food_item_id;
                    $replacement->quantity = $similerFood->quantity;
                    $replacement->meal_plan_id = $mealPlan->meal_plan_id;
                    $replacement->save();

                    return response()->json(['status' => true, 'msg' => 'Success.']);
                } else {
                    return response()->json(['status' => true, 'msg' => 'No record.']);
                }
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function uploadImage(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'image' => 'required',
            ]);

            if ($validator->fails()) {
                // dd($validator->errors()->all());
                return response()->json([
                    'status' => false,
                    'message' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {
                $user_id = Auth::user()->id;
                $mealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();

                if ($request->hasFile('image')) {
                    $path = 'public/users/' . $user_id . '/images';
                    $file = $request->file('image');
                    $imagename = time() . '_' . $file->getClientOriginalName();
                    $request->file('image')->storeAs($path, $imagename);

                    $userImage = new UserImage;
                    $userImage->meal_plan_id = ($mealPlan) ? $mealPlan->id : 0;
                    $userImage->user_id = $user_id;
                    $userImage->image = $imagename;
                    $userImage->save();
                    $image = asset(Storage::url($path . '/' . $imagename));

                    return response()->json(['status' => true, 'msg' => 'Image Uploaded.', 'id' => $userImage->id, 'image' => $image, 'uploaded_at' => date('Y-m-d H:i:s')]);
                }
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function uploadProfileImage(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'image' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {
                $user_id = Auth::user()->id;
                if ($request->hasFile('image')) {
                    $path = 'public/users/' . $user_id;
                    $file = $request->file('image');
                    $imagename = time() . '_' . $file->getClientOriginalName();
                    $request->file('image')->storeAs($path, $imagename);
                    User::where('id', $user_id)->update(['image' => $imagename]);
                    $image = asset(Storage::url($path . '/' . $imagename));

                    return response()->json(['status' => true, 'msg' => 'Image Uploaded.', 'image' => $image]);
                }
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function userProgressImages(Request $request)
    {
        if (Auth::user()) {
            $user_id = Auth::user()->id;
            $url = asset(Storage::url('users/' . $user_id . '/images'));
            $userImages = UserImage::select(DB::raw("id,concat('" . $url . "','/',image) as image,created_at"))
                ->where('user_id', $user_id)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json(['status' => true, 'data' => $userImages]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function updateProfile(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'full_name' => 'nullable',
                'email' => 'nullable',
                'phone' => 'nullable',
                'gender' => 'nullable',
                'age' => 'nullable',
                'height' => 'nullable',
                // 'weight' => 'nullable',
                // 'country_id' => 'nullable',
                // 'state_id' => 'nullable',
                // 'city_id' => 'nullable',
                // 'fitness_level_id' => 'nullable',
                // 'exercise_plan_day_id' => 'nullable'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            } else {

                $user = Auth::user();
                $user_id = $user->id;
                // $username = 'DT100' . $user_id;
                $user = User::find($user_id);
                // $user->username = $username;
                $user->full_name = $request->full_name ? $request->full_name : '';
                $user->email = $request->email ? $request->email : '';
                $user->phone = $request->phone ? $request->phone : '';
                $user->age = $request->age ? $request->age : 0;
                $user->height = $request->height ? $request->height : 0;
                /*$user->gender = $request->gender ? $request->gender : 'M';
                $user->weight = $request->weight ? $request->weight : 0;
                $user->country_id = $request->country_id ? $request->country_id : 0;
                $user->state_id = $request->state_id ? $request->state_id : 0;
                $user->city_id = $request->city_id ? $request->city_id : 0;
                $user->fitness_level_id = $request->fitness_level_id ? $request->fitness_level_id : 0;
                $user->exercise_plan_day_id = $request->exercise_plan_day_id ? $request->exercise_plan_day_id : 0;
                $user->goal_id = $request->goal_id ? $request->goal_id : 0;*/
                $user->save();

                $userGoal = new UserGoal;
                $userGoal->user_id = $user_id;
                $userGoal->goal_id = $user->goal_id;
                $userGoal->current_value = $request->weight ? $request->weight : 0;
                $userGoal->target_value = $request->target_value ? $request->target_value : 0;
                $userGoal->start_date = date('Y-m-d');
                $userGoal->end_date = date('Y-m-d', strtotime('+2 months'));
                $userGoal->save();

                $weightTracking = new WeightTracking;
                $weightTracking->user_id = $user_id;
                $weightTracking->current_value = $request->weight ? $request->weight : 0;
                $weightTracking->target_value = $request->target_value ? $request->target_value : 0;
                $weightTracking->save();

                if (isset($request->medical_issue_ids) && $request->medical_issue_ids != '') {
                    UserMedicalIssue::where('user_id', $user_id)->delete();
                    $medIssueIds = explode(',', $request->medical_issue_ids);
                    if (count($medIssueIds) > 0) {
                        $medIds = [];
                        foreach ($medIssueIds as $key => $value) {
                            $medIds[$key]['medical_issue_id'] = $value;
                            $medIds[$key]['user_id'] = $user_id;
                        }
                        UserMedicalIssue::insert($medIds);
                    }
                }

                if (isset($request->allergy_ids) && $request->allergy_ids != '') {
                    UserAllergies::where('user_id', $user_id)->delete();
                    $allergyIds = explode(',', $request->allergy_ids);
                    if (count($allergyIds) > 0) {
                        $alIds = [];
                        foreach ($allergyIds as $key => $value) {
                            $alIds[$key]['medical_issue_id'] = $value;
                            $alIds[$key]['user_id'] = $user_id;
                        }
                        UserAllergies::insert($alIds);
                    }
                }

                $user = $this->userResponse($user_id);

                return response()->json(['status' => true, 'msg' => 'Information Updated!', 'data' => $user]);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function myPayments(Request $request)
    {
        if (Auth::user()) {
            $user_id = Auth::user()->id;
            $payments = Payment::with(['user_membership'])->where('user_id', $user_id);
            $total = $payments->get()->count();
            $payments = $payments->paginate(10);

            return response()->json(['status' => true, 'data' => PaymentCollection::collection($payments), 'total' => $total]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function myPackages(Request $request)
    {
        if (Auth::user()) {
            $user_id = Auth::user()->id;
            $memberships = UserMembership::where('user_id', $user_id);
            $total = $memberships->get()->count();
            $memberships = $memberships->paginate(10);

            return response()->json(['status' => true, 'data' => MembershipCollection::collection($memberships), 'total' => $total]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User.']);
        }
    }

    public function myCoupons(Request $request)
    {
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $status = isset($request->status) ? $request->status : '';
            $coupons = Coupon::whereHas('coupon_users', function ($q) use ($user_id, $status) {
                $q->where('user_id', $user_id);
                if ($status == 1 || $status == 0) {
                    $q->where('active', $status);
                }
                if ($status == 2) {
                    $q->whereDate('expiry_date', '<', date('Y-m-d H:i:s'));
                }
            })->paginate(10);

            $response = ['status' => true, 'data' => CouponResource::collection($coupons)];
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Unauthorized user!',
            ]);
        }

        return response()->json($response);
    }

    public function applyCoupon(Request $request)
    {
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $coupon_code = $request->coupon_code;
            $coupon = Coupon::where('code', $coupon_code)->first();
            if ($coupon) {
                $coupon_id = $coupon->id;
                if ($coupon->type == 'A') {

                    $payment = Payment::where('user_id', $user_id)->where('coupon_id', $coupon_id)
                        ->where('status', 'S')->first();
                    if ($payment) {
                        return response()->json([
                            'status' => false,
                            'msg' => 'Coupon is already used!',
                        ]);
                    }

                    return response()->json([
                        'status' => true,
                        'msg' => 'Coupon Applied!',
                        'discount_type' => $coupon->discount_type,
                        'amount' => strval($coupon->amount),
                        'coupon_id' => intval($coupon_id),
                    ]);
                } else {
                    $isUserCoupon = CouponUser::where('coupon_id', $coupon_id)
                        ->where('user_id', $user_id)
                        ->first();

                    if ($isUserCoupon->active == 1) {
                        if ($isUserCoupon->expiry_date < date('Y-m-d H:i:s')) {
                            return response()->json([
                                'status' => false,
                                'msg' => 'Coupon Expired!',
                            ]);
                        }

                        return response()->json([
                            'status' => true,
                            'msg' => 'Coupon Applied!',
                            'discount_type' => $coupon->discount_type,
                            'amount' => strval($coupon->amount),
                            'coupon_id' => intval($coupon_id),
                        ]);
                    }

                    if ($isUserCoupon->active == 0) {
                        return response()->json([
                            'status' => false,
                            'msg' => 'Coupon is already used!',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Invalid Coupon!',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Unauthorized user!',
            ]);
        }
    }

    public function notifications(Request $request)
    {
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::select(DB::raw("*,DATE_FORMAT(created_at,'%d %b %y %l:%i %p') as added_on"))->where('notify_to', $user_id);
            $total = $notifications->get()->count();
            $notifications = $notifications->orderBy('id', 'desc')->paginate(10);

            return response()->json([
                'status' => true,
                'data' => $notifications->items(),
                'total' => $total,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Unauthorized user!',
            ]);
        }
    }

    public function deleteProfile(Request $request)
    {
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            User::where('id', $user_id)->delete();

            return response()->json([
                'status' => true,
                'msg' => 'Your profile has been deleted.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Unauthorized user!',
            ]);
        }
    }
}
