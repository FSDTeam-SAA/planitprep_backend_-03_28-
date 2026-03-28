<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\Appliance;
use App\Models\City;
use App\Models\Country;
use App\Models\DietRegime;
use App\Models\DietType;
use App\Models\ExercisePlanDay;
use App\Models\FitnessLevel;
use App\Models\Goal;
use App\Models\LoginSetting;
use App\Models\MedicalIssue;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\State;
use App\Models\Test;
use App\Models\CookingPrep;
use App\Models\Cuisine;
use App\Models\CookingTime;
use App\Models\MealCount;
use App\Models\MealPrepSchedule;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function appConfig(Request $request)
    {
        $data['app_name'] = 'Dietitian App';
        $data['app_version'] = '1.0.0';

        $fitnessLevels = FitnessLevel::select('id', 'name', 'image')->where('active', 1)->get();
        $data['fitness_levels'] = $fitnessLevels;

        $exercisePlanDays = ExercisePlanDay::select('id', 'name', 'image')->where('active', 1)->get();
        $data['exercise_plan_days'] = $exercisePlanDays;

        $goals = Goal::select('id', 'name', 'image')->where('active', 1)->get();
        $data['goals'] = $goals;

        $medicalIssues = MedicalIssue::select('id', 'name')->where('active', 1)->orderBy('name')->get();
        $data['medical_issues'] = $medicalIssues;

        $dietTypes = DietType::select('id', 'name', 'image')->where('active', 1)->orderBy('name')->get();
        $data['diet_types'] = $dietTypes;

        $allergens = Allergen::select('id', 'name')->where('active', 1)->orderBy('name')->get();
        $data['allergens'] = $allergens;

        $countries = Country::select('id', 'iso_code_2', 'name')->orderBy('name')->get();
        $data['countries'] = $countries;

        $dietRegimes = DietRegime::where('active', 1)->get();
        $data['diet_regimes'] = $dietRegimes;

        $tests = Test::where('active', 1)->get();
        $data['tests'] = $tests;

        $paymentMethods = PaymentMethod::where('active', 1)->get();
        $data['payment_method'] = $paymentMethods;

        $cookingPreps = CookingPrep::where('active', 1)->get();
        $data['cooking_preps'] = $cookingPreps;

        $cuisines = Cuisine::where('active', 1)->get();
        $data['cuisines'] = $cuisines;

        $cooking_time = CookingTime::all();
        $data['cooking_time'] = $cooking_time;

        $appliance = Appliance::where('isActive', 1)->get();
        $data['appliances'] = $appliance;

        $meal_prep_schedule = MealPrepSchedule::all();
        $data['meal_prep_schedule'] = $meal_prep_schedule;

        $meal_count = MealCount::all();
        $data['meal_count'] = $meal_count;



        $loginSettings = LoginSetting::first();
        $email_login = false;
        $mobile_login = false;
        $google_login = false;
        $facebook_login = false;
        $apple_login = false;
        if (isset($loginSettings) && $loginSettings->email == 1) {
            $email_login = true;
        }
        if (isset($loginSettings) && $loginSettings->phone == 1) {
            $mobile_login = true;
        }

        if (isset($loginSettings) && $loginSettings->google == 1) {
            $google_login = true;
        }
        if (isset($loginSettings) && $loginSettings->facebook == 1) {
            $facebook_login = true;
        }

        if (isset($loginSettings) && $loginSettings->apple == 1) {
            $apple_login = true;
        }
        $data['email_login'] = $email_login;
        $data['mobile_login'] = $mobile_login;
        $data['google_login'] = $google_login;
        $data['facebook_login'] = $facebook_login;
        $data['apple_login'] = $apple_login;

        $data['testing_android'] = false;
        $data['testing_ios'] = $request->version == '1.0.0+18' ? true : false;

        $setting = Setting::first();
        $data['contact_no'] = $setting->contact_no;
        $data['enable_free_membership'] = $setting->enable_free_membership == 1 ? true : false;
        $data['show_payments_memberships'] = false;

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function getCountries(Request $request)
    {

        $countries = Country::select('id', 'iso_code_2', 'name')->orderBy('name')->get();
        // $data['states'] = $states;
//i change iso2_cc to iso_code_2
        return response()->json(['status' => true, 'data' => $countries]);
    }

    public function getStates(Request $request)

    {
        $countryId = $request->country_id;
        $states = State::select('id', 'name', 'country_id')->where('country_id', $countryId)->orderBy('name')->get();
        // $data['states'] = $states;

        return response()->json(['status' => true, 'data' => $states]);
    }

    public function getCities(Request $request)
    {
        $stateId = $request->state_id;
        $cities = City::select('id', 'name', 'state_id')->where('state_id', $stateId)->orderBy('name')->get();

        // $data['states'] = $states;
        return response()->json(['status' => true, 'data' => $cities]);
    }
}
