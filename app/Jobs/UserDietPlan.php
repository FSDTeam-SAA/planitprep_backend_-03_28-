<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use App\Models\ExercisePlanDay;
use App\Models\FitnessLevel;
use App\Models\Goal;
use App\Models\MealPlanItem;
use App\Models\State;
use App\Models\User;
use App\Models\UserAllergies;
use App\Models\UserDietRegime;
use App\Models\UserGoal;
use App\Models\UserMealPlan;
use App\Models\UserMedicalIssue;
use App\Models\UserPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserDietPlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user_id;

    // public $tries = 4;
    // public $timeout = 99999999999999999;
    // public $numprocs = 8;
    /**
     * Create a new job instance.
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '18048M');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userId = $this->user_id;
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

                // $messages = "I am providing you user's details for the diet plan. Make sure the diet plan is region specific as per the area provided by the user. Also the units provided in the diet food items should in grams,cups or counts but not handful as handful is an ambiguous unit.";
                // $messages = "User details are provided. Make the diet plan region-specific. Use only grams, counts as units — avoid 'handful' or 'cups' or 'cups' as it's ambiguous.";
                $messages = "User details provided. Make diet region-specific. Use grams, ml and counts as units only — avoid 'handful', 'cups', and 'bowls' (ambiguous).";
                $messages .= 'weight='.$userObj->weight.',';
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
                $messages .= ' gender='.($userObj->gender == 'M' ? 'Male' : 'Female').',';
                $messages .= ' age='.$userObj->age;
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

                // dd($messages);
                if ($userObj->weight > 0 || $userObj->height > 0 || $userObj->age > 0) {
                    // $day = 1;
                    $userMealPlan = UserMealPlan::where('user_id', $userId)->orderBy('id', 'desc')->first();
                    // if ($userMealPlan) {
                    //     $planDay = MealPlanItem::where('meal_plan_id', $userMealPlan->meal_plan_id)->max('day');
                    //     if ($planDay) {
                    //         $day = $planDay + 1;
                    //     }
                    // }

                    // if ($day <= 7) {
                    // for ($i = 1; $i <= 1; $i++) {
                    //    UserDayDietPlan::dispatch($userId, $day, $messages)->delay(now()->addSeconds(40));
                    // }else{
                     $meal_plan_id=0;
                    if($userMealPlan){
                      $meal_plan_id=$userMealPlan->meal_plan_id;
                    }
                    $totalDays = MealPlanItem::where('meal_plan_id', $meal_plan_id)->groupBy('day')->pluck('day')->count();

                    if ($totalDays <= 7) {
                        for ($x = 1; $x <= 7; $x++) {
                            $isRecord = MealPlanItem::where('meal_plan_id', $meal_plan_id)->where('day', $x)->first();
                            if (! $isRecord) {
                                UserDayDietPlan::dispatch($userId, $x, $messages)->delay(now()->addSeconds(40));
                            }
                        }

                    }
                  }
                    // }

            } else {
            }
        } else {
        }
    }
}
