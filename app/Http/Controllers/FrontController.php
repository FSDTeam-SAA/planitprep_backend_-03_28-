<?php

namespace App\Http\Controllers;

use App\Jobs\UserDietPlan;
use App\Models\City;
use App\Models\Country;
use App\Models\DailyIntake;
use App\Models\DietType;
use App\Models\ExercisePlanDay;
use App\Models\FitnessLevel;
use App\Models\Goal;
use App\Models\Meal;
use App\Models\MealPlanItem;
use App\Models\MedicalIssue;
use App\Models\Membership;
use App\Models\State;
use App\Models\User;
use App\Models\UserExercisePlanDay;
use App\Models\UserGoal;
use App\Models\UserMealPlan;
use App\Models\UserMedicalIssue;
use App\Models\UserPreference;
use App\Models\UserTestReport;
use App\Models\WeightTracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class FrontController extends Controller
{
    /**
     * If no active login then redirect to social login. Else redirect to dashboard.
     */
    public function login()
    {
        if (Auth::id()) {
            return to_route('front-dashboard');
        } else {
            return Socialite::driver('google')->redirect();
        }
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();

        // Check if the user already exists in the database
        $existingUser = User::where('google_id', $user->getId())->first();

        if ($existingUser) {
            // Login user.
            Auth::login($existingUser);

            return redirect()->intended(route('front-dashboard'));
        } else {
            // Add new user.
            $newUser = User::create([
                'full_name' => $user->getName(),
                'email' => $user->getEmail(),
                'google_id' => $user->getId(),
            ]);

            $username = 'DT100'.$newUser->id;

            while (User::where('username', $username)->exists()) {
                $username = 'DT100'.$newUser->id.rand(1, 100);
            }

            $newUser->username = $username;
            $newUser->save();

            // Log the new user in
            Auth::login($newUser);

            return redirect()->intended(route('front-dashboard'));
        }
    }

    /**
     * Open front dashboard.
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    // Logout user
    public function logout()
    {
        Auth::logout();

        return to_route('sign-in');
    }

    public function step1()
    {
        session()->forget(['step1', 'step2', 'step3', 'step4', 'step5', 'step6', 'step7', 'step8', 'step9', 'step10', 'step11', 'step12']);

        return view('register.step1');
    }

    public function store_step1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
        ], [
            'name' => 'Kindly enter your full name.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step1_error_bag')->withInput();
        }

        $data = [
            'name' => $request->name,
        ];

        $request->session()->put('step1', $data);

        return to_route('step2');
    }

    public function step2()
    {
        return view('register.step2');
    }

    public function store_step2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gender' => 'required',
        ], [
            'gender' => 'Kindly select your gender.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step2_error_bag')->withInput();
        }

        $data = [
            'gender' => $request->gender,
        ];

        $request->session()->put('step2', $data);

        return to_route('step3');
    }

    public function step3()
    {
        return view('register.step3');
    }

    public function store_step3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'age' => 'required|numeric|min:0|max:200',
        ], [
            'age' => 'Kindly enter your age.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step3_error_bag')->withInput();
        }

        $data = [
            'age' => $request->age,
        ];

        $request->session()->put('step3', $data);

        return to_route('step4');
    }

    public function step4()
    {
        return view('register.step4');
    }

    public function store_step4(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feet' => 'required',
            'inch' => 'nullable',
        ], [
            'feet' => 'Kindly enter your height.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step4_error_bag')->withInput();
        }

        $data = [
            'feet' => $request->feet,
            'inch' => $request->inch,
        ];

        $request->session()->put('step4', $data);

        return to_route('step5');
    }

    public function step5()
    {
        return view('register.step5');
    }

    public function store_step5(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_weight' => 'required|min:1|max:999',
            'target_weight' => 'required|min:1|max:999',
        ], [
            'current_weight' => 'Kindly enter your current weight.',
            'target_weight' => 'Kindly enter your target weight.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step5_error_bag')->withInput();
        }

        $data = [
            'current_weight' => $request->current_weight,
            'target_weight' => $request->target_weight,
        ];

        $request->session()->put('step5', $data);

        return to_route('step6');
    }

    public function step6()
    {
        $countries = $this->get_countries();

        return view('register.step6', compact('countries'));
    }

    public function store_step6(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
        ], [
            'country.required' => 'Kindly select your country.',
            'state.required' => 'Kindly select your state.',
            'city.required' => 'Kindly select your city.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step6_error_bag')->withInput();
        }

        $data = [
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
        ];

        $request->session()->put('step6', $data);

        return to_route('step7');
    }

    public function step7()
    {
        $goals = Goal::where('active', 1)->get();

        return view('register.step7', compact('goals'));
    }

    public function store_step7(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goal' => 'required',
        ], [
            'goal' => 'Kindly select your main goal.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step7_error_bag')->withInput();
        }

        $data = [
            'goal' => $request->goal,
        ];

        $request->session()->put('step7', $data);

        return to_route('step8');
    }

    public function step8()
    {
        $medical_issues = MedicalIssue::where('active', 1)->get();

        return view('register.step8', compact('medical_issues'));
    }

    public function store_step8(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medical_issue' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step8_error_bag')->withInput();
        }

        $data = [
            'medical_issue' => $request->medical_issue,
        ];

        $request->session()->put('step8', $data);

        return to_route('step9');
    }

    public function step9()
    {
        $diet_types = DietType::where('active', 1)->get();

        return view('register.step9', compact('diet_types'));
    }

    public function store_step9(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'diet_type' => 'nullable',
        ], [
            'diet_type' => 'Kindly select diet type.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step9_error_bag')->withInput();
        }

        $data = [
            'diet_type' => $request->diet_type,
        ];

        $request->session()->put('step9', $data);

        return to_route('step10');
    }

    public function step10()
    {
        $fitness_level = FitnessLevel::where('active', 1)->get();

        return view('register.step10', compact('fitness_level'));
    }

    public function store_step10(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fitness_level' => 'required',
        ], [
            'fitness_level' => 'Kindly select your fitness level.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step10_error_bag')->withInput();
        }

        $data = [
            'fitness_level' => $request->fitness_level,
        ];

        $request->session()->put('step10', $data);

        return to_route('step11');
    }

    public function step11()
    {
        $days = ExercisePlanDay::where('active', 1)->get();

        return view('register.step11', compact('days'));
    }

    public function store_step11(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'required',
        ], [
            'days' => 'Kindly select your plan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step11_error_bag')->withInput();
        }

        $data = [
            'days' => $request->days,
        ];

        $request->session()->put('step11', $data);

        return to_route('step12');
    }

    public function step12()
    {
        $clinical_tests = [
            'Complete Blood Count (CBC)',
            'Basic Metabolic Panel (BMP)',
            'Comprehensive Metabolic Panel (CMP)',
            'Lipid Profile',
            'Liver Function Test (LFT)',
            'Kidney Function Test (KFT)',
            'Thyroid Function Test (TFT)',
            'Urinalysis',
            'Blood Glucose Test',
            'Hemoglobin A1c',
            'Prothrombin Time (PT)',
            'International Normalized Ratio (INR)',
            'Erythrocyte Sedimentation Rate (ESR)',
            'C-Reactive Protein (CRP)',
            'Troponin Test',
            'B-type Natriuretic Peptide (BNP)',
            'Arterial Blood Gases (ABG)',
            'HIV Test',
            'Hepatitis B and C Tests',
            'Pregnancy Test',
            'Pap Smear',
            'Cholesterol Test',
            'Vitamin D Test',
            'Electrolyte Panel',
            'Coagulation Profile',
            'Blood Culture Test',
            'Chest X-ray',
            'ECG (Electrocardiogram)',
            'CT Scan',
            'MRI Scan',
            'Ultrasound',
            'Stool Test',
            'Sputum Culture',
            'Biopsy',
            'Bone Density Test',
            'Pulmonary Function Test (PFT)',
            'Allergy Test',
            'Skin Test for TB',
            'Genetic Testing',
        ];

        return view('register.step12', compact('clinical_tests'));
    }

    public function store_step12(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test' => 'nullable',
            'date' => 'nullable',
            'file' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'step12_error_bag')->withInput();
        }

        $files = $request->file('file');
        $fileCount = 0;
        $data = null;

        if (is_array($files)) {
            $fileCount = count($files);
        } else {
            $fileCount = $files ? 1 : 0;
        }

        if ($fileCount > 0) {
            if ($request->test != null) {
                if ($fileCount == count($request->test)) {
                    $data = [
                        'test' => $request->test,
                        'date' => $request->date,
                        'file' => $request->file,
                    ];

                    $total = count($request->test);

                    if ($request->file) {
                        for ($i = 0; $i < $total; $i++) {
                            $path = 'public/test_reports';
                            if ($request->file[$i]) {
                                $file = $request->file[$i];
                                $filename = 'report-'.rand(1, 999).time().'.'.$file->getClientOriginalExtension();
                                $data['file'][$i] = $file->storeAs($path, $filename);
                            } else {
                                $data['file'][$i] = '';
                            }
                        }
                    } else {
                        for ($i = 0; $i < $total; $i++) {
                            $data['file'][$i] = '';
                        }
                    }
                }
            }
        }

        $request->session()->put('step12', $data);

        $user_id = $this->store();

        // Login user
        $user = User::where('id', $user_id)->first();
        Auth::login($user);
        session()->regenerate();

        return redirect()->route('buy-membership');
    }

    /**
     * Fetch data from session.
     * Add new user
     * Login user
     * Redirect to dashboad.
     */
    public function store()
    {
        // Fetch data for each step.
        $step0 = session('step0');
        $step1 = session('step1');
        $step2 = session('step2');
        $step3 = session('step3');
        $step4 = session('step4');
        $step5 = session('step5');
        $step6 = session('step6');
        $step7 = session('step7');
        $step8 = session('step8');
        $step9 = session('step9');
        $step10 = session('step10');
        $step11 = session('step11');
        $step12 = session('step12');

        // dd(session()->all());

        // Add new user record.
        $user = new User;
        $user->phone = $step0['mobile'];
        $user->full_name = $step1['name'] ? $step1['name'] : '';
        $user->gender = $step2['gender'] ? $step2['gender'] : '';
        $user->age = $step3['age'] ? $step3['age'] : '';
        $user->height = ($step4['feet'] * 12) + $step4['inch'];
        $user->weight = $step5['current_weight'] ? $step5['current_weight'] : '';
        $user->country_id = $step6['country'] ? $step6['country'] : '';
        $user->state_id = $step6['state'] ? $step6['state'] : '';
        $user->city_id = $step6['city'] ? $step6['city'] : '';
        $user->goal_id = $step7['goal'] ? $step7['goal'] : '';
        $user->fitness_level_id = $step10['fitness_level'] ? $step10['fitness_level'] : '';
        $user->save();

        $user_id = $user->id;

        $r = new UserGoal;
        $r->user_id = $user_id;
        $r->goal_id = $user->goal_id;
        $r->current_value = $step5['current_weight'];
        $r->target_value = $step5['target_weight'];
        $r->start_date = Carbon::now();
        $r->save();

        $r = new WeightTracking;
        $r->user_id = $user_id;
        $r->current_value = $user->weight;
        $r->target_value = $step5['target_weight'];
        $r->save();

        if (is_countable($step8['medical_issue'])) {
            $total = count($step8['medical_issue']);

            for ($i = 0; $i < $total; $i++) {
                $r = new UserMedicalIssue;
                $r->user_id = $user_id;
                $r->medical_issue_id = $step8['medical_issue'][$i];
                $r->save();
            }
        }

        // Save user preferences data.
        if ($step9['diet_type'] != null) {
            $r = new UserPreference;
            $r->user_id = $user_id;
            $r->diet_type_id = $step9['diet_type'];
            $r->save();
        }

        // Save user exercise plan days.
        if ($step11['days'] != null) {
            $r = new UserExercisePlanDay;
            $r->user_id = $user_id;
            $r->day_id = $step11['days'];
            $r->save();
        }

        if (is_countable($step12)) {
            $total = count($step12['test']);

            for ($i = 0; $i < $total; $i++) {
                $r = new UserTestReport;
                $r->user_id = $user_id;
                $r->name = $step12['test'][$i] ? $step12['test'][$i] : '';
                $r->test_date = $step12['date'][$i] ? $step12['date'][$i] : '';
                $r->file = $step12['file'][$i] ? $step12['file'][$i] : '';

                if ($r->name != '' && $r->test_date != '') {
                    $r->save();
                }
            }
        }

        session()->forget(['step1', 'step2', 'step3', 'step4', 'step5', 'step6', 'step7', 'step8', 'step9', 'step10', 'step11', 'step12']);

        return $user_id;
    }

    public function buy_membership()
    {
        $memberships = Membership::get();

        return view('buy-membership', compact('memberships'));
    }

    public function get_countries()
    {
        return Country::select('id', 'name')->get();
    }

    public function get_states(Request $request)
    {
        return State::where('country_id', $request->id)->select('id', 'name')->get();
    }

    public function get_cities(Request $request)
    {
        return City::where('state_id', $request->id)->select('id', 'name')->get();
    }

    public function weight_tracker(Request $request)
    {
        $user = Auth::user();
        $goal = UserGoal::where('user_id', $user->id)->first();

        $goal_name = $user->goal->name;
        $current_weight = (int) $goal->current_value;
        $target_weight = (int) $goal->target_value;
        $weight_diff = $current_weight - $target_weight;
        $start_date = $goal->start_date;
        $end_date = date('Y-m-d');
        $weight_tracking_data = WeightTracking::where('user_id', $user->id)->get();
        $temp = [];

        foreach ($weight_tracking_data as $item) {
            $temp['date'][] = date('Y-m-d', strtotime($item->created_at));
            $temp['value'][] = $item->current_value;
        }

        $len = count($temp['value']);
        $temp['target'] = array_fill(0, $len, $target_weight);
        $weight_tracking_data = $temp;

        $current_weight = $temp['value'][$len - 1];

        $target_achieved = (($user->weight - $current_weight) / ($user->weight - $target_weight)) * 100;
        $target_remaining = 100 - $target_achieved;

        return view('weight-tracker', compact(
            'goal_name',
            'current_weight',
            'target_weight',
            'weight_diff',
            'start_date',
            'end_date',
            'weight_tracking_data',
            'target_achieved',
            'target_remaining',
        ));
    }

    public function weight_tracking_date_wise(Request $request)
    {
        $x = $request->start_date;
        $y = $request->end_date;

        $user = Auth::user();

        $startDate = Carbon::createFromFormat('Y-m-d', $x)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $y)->endOfDay();

        $weight_tracking_data = WeightTracking::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $temp = [];
        $goal = UserGoal::where('user_id', $user->id)->first();
        $target_weight = (int) $goal->target_value;

        foreach ($weight_tracking_data as $item) {
            $temp['date'][] = date('Y-m-d', strtotime($item->created_at));
            $temp['value'][] = $item->current_value;
        }

        $len = count($temp['value']);
        $temp['target'] = array_fill(0, $len, $target_weight);
        $weight_tracking_data = $temp;

        return $weight_tracking_data;
    }

    public function update_current_weight(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_weight' => 'required',
            'target_weight' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'false',
                'errors' => $validator->errors(),
            ];
        }

        $r = new WeightTracking;
        $r->user_id = Auth::id();
        $r->current_value = $request->current_weight;
        $r->target_value = $request->target_weight;
        $r->save();
    }

    public function food_tracker(Request $request)
    {
        $user = Auth::user();

        // Fet how much user need to intake daily.
        $dailyCalorie = $user->daily_calorie_intake;
        $dailyCarbs = $user->daily_carbs_intake;
        $dailyFiber = $user->daily_fiber_intake;
        $dailyProtein = $user->daily_protein_intake;
        $dailyFats = $user->daily_fats_intake;

        $start_date = Carbon::today()->subDays(7)->toDateString();
        $end_date = Carbon::today()->toDateString();

        $today = Carbon::today();
        $dates = [];

        $calorieTarget = [];
        $calorieValue = [];

        $carbsTarget = [];
        $carbsValue = [];

        $fiberTarget = [];
        $fiberValue = [];

        $proteinTarget = [];
        $proteinValue = [];

        $fatsTarget = [];
        $fatsValue = [];

        $dates = [];
        $current_date = Carbon::parse($start_date);

        while ($current_date <= Carbon::parse($end_date)) {
            $dates[] = $current_date->toDateString();
            $current_date->addDay();
        }

        $dailyIntake = DailyIntake::where('user_id', $user->id)
            ->where('intake_date', '>=', $start_date)
            ->where('intake_date', '<=', $end_date)
            ->get(['intake_date', 'total_calories', 'total_fat', 'total_carbohydrates', 'total_protein'])
            ->map(function ($item) {
                return [
                    'intake_date' => $item->intake_date,
                    'total_calories' => $item->total_calories,
                    'total_fat' => $item->total_fat,
                    'total_carbohydrates' => $item->total_carbohydrates,
                    'total_protein' => $item->total_protein,
                ];
            })
            ->toArray();

        $calorie_arr = [];
        $fat_arr = [];
        $carbs_arr = [];
        $protein_arr = [];
        $fiber_arr = [];

        foreach ($dailyIntake as $x) {
            $calorie_arr[] = $x['total_calories'];
            $fat_arr[] = $x['total_fat'];
            $carbs_arr[] = $x['total_carbohydrates'];
            $protein_arr[] = $x['total_protein'];
        }

        for ($i = 0; $i < 7; $i++) {
            $calorieTarget[] = $dailyCalorie;
            $calorieValue[] = 100;

            $carbsTarget[] = $dailyCarbs;
            $carbsValue[] = 100;

            $fiberTarget[] = $dailyFiber;
            $fiberValue[] = 100;

            $proteinTarget[] = $dailyProtein;
            $proteinValue[] = 100;

            $fatsTarget[] = $dailyFats;
            $fatsValue[] = 100;
        }

        return view('food-tracker', compact(
            'start_date',
            'end_date',
            'dailyCalorie',
            'dailyCarbs',
            'dailyFiber',
            'dailyProtein',
            'dailyFats',
            'dates',
            'calorieTarget',
            'calorieValue',
            'carbsTarget',
            'carbsValue',
            'fiberTarget',
            'fiberValue',
            'proteinTarget',
            'proteinValue',
            'fatsTarget',
            'fatsValue',
            'calorie_arr',
            'fat_arr',
            'carbs_arr',
            'protein_arr',
            'fiber_arr',
        ));
    }

    public function food_tracking_date_wise(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $user = Auth::user();

        $dailyCalorie = $user->daily_calorie_intake;
        $dailyCarbs = $user->daily_carbs_intake;
        $dailyFiber = $user->daily_fiber_intake;
        $dailyProtein = $user->daily_protein_intake;
        $dailyFats = $user->daily_fats_intake;

        $dailyIntake = DailyIntake::where('user_id', $user->id)
            ->where('intake_date', '>=', $start_date)
            ->where('intake_date', '<=', $end_date)
            ->get(['intake_date', 'total_calories', 'total_fat', 'total_carbohydrates', 'total_protein'])
            ->map(function ($item) {
                return [
                    'intake_date' => $item->intake_date,
                    'total_calories' => $item->total_calories,
                    'total_fat' => $item->total_fat,
                    'total_carbohydrates' => $item->total_carbohydrates,
                    'total_protein' => $item->total_protein,
                ];
            })
            ->toArray();

        $len = count($dailyIntake);

        $dates = [];
        $calorie_arr = [];
        $fat_arr = [];
        $carbs_arr = [];
        $protein_arr = [];
        $fiber_arr = [];

        foreach ($dailyIntake as $x) {
            $dates[] = $x['intake_date'];
            $calorie_arr[] = $x['total_calories'];
            $fat_arr[] = $x['total_fat'];
            $carbs_arr[] = $x['total_carbohydrates'];
            $protein_arr[] = $x['total_protein'];
        }

        return [
            'dates' => $dates,
            'target' => [
                'dailyCalorie' => array_fill(0, $len, $dailyCalorie),
                'dailyCarbs' => array_fill(0, $len, $dailyCarbs),
                'dailyFiber' => array_fill(0, $len, $dailyFiber),
                'dailyProtein' => array_fill(0, $len, $dailyProtein),
                'dailyFats' => array_fill(0, $len, $dailyFats),
            ],
            'values' => [
                'calorie_arr' => $calorie_arr,
                'fat_arr' => $fat_arr,
                'carbs_arr' => $carbs_arr,
                'protein_arr' => $protein_arr,
                'fiber_arr' => $fiber_arr,
            ],
        ];

    }

    public function bmi()
    {
        $user = Auth::user();
        $weight = WeightTracking::where('user_id', $user->id)
            ->latest()
            ->value('current_value');

        $weight = round($weight);
        $height = ($user->height * 0.0254); // Get height in meters.

        $bmi = round(($weight / ($height * $height)));
        $body_fat = 0;
        $water = 0;
        $muscle_mass = 0;
        $bone_mass = 0;
        $metabolism = 0;

        if ($user->gender = 'M') {
            $body_fat = (1.20 * $bmi) + (0.23 * $user->age) - 16.2;
            $water = $weight * 0.60;
            $bone_mass = $weight * 8.5;
            $metabolism = (10 * $weight) + (6.25 * ($height * 100)) - (5 * $user->age + 5);
        } else {
            $body_fat = (1.20 * $bmi) + (0.23 * $user->age) - 5.4;
            $water = $weight * 0.55;
            $bone_mass = $weight * 8;
            $metabolism = (10 * $weight) + (6.25 * ($height * 100)) - (5 * $user->age + 161);
        }

        $muscle_mass = round($weight * (1 - ($body_fat / 100)));
        $body_fat = round($body_fat);
        $visceral_fat = (0.1 * $bmi) + (0.25 * $user->age) - 3;
        $metabolism = round($metabolism);

        $body_age = $user->age + (($body_fat - 10) / 2);

        $stat = [
            'weight' => 'Healthy',
            'bmi' => 'Healthy',
            'body_fat' => 'Healthy',
            'muscle_mass' => 'Healthy',
            'water' => 'Healthy',
            'visceral' => 'Healthy',
            'bone_mass' => 'Healthy',
            'metabolism' => 'Healthy',
            'body_age' => 'Healthy',
        ];

        $bg = [
            'weight' => 'bg-color8',
            'bmi' => 'bg-color8',
            'body_fat' => 'bg-color8',
            'muscle_mass' => 'bg-color8',
            'water' => 'bg-color8',
            'visceral' => 'bg-color8',
            'bone_mass' => 'bg-color8',
            'metabolism' => 'bg-color8',
            'body_age' => 'bg-color8',
        ];

        if ($bmi > 40) {
            $stat['weight'] = 'Very High';
            $stat['bmi'] = 'Very High';
            $stat['body_fat'] = 'Very High';
            $stat['muscle_mass'] = 'High';
            $stat['water'] = 'Low';

            $bg['weight'] = 'bg-color9';
            $bg['bmi'] = 'bg-color9';
            $bg['body_fat'] = 'bg-color9';
            $bg['muscle_mass'] = 'bg-color9';
            $bg['water'] = 'bg-color9';
        } elseif ($bmi > 25) {
            $stat['weight'] = 'High';
            $stat['bmi'] = 'High';
            $stat['body_fat'] = 'High';
            $stat['muscle_mass'] = 'High';
            $stat['water'] = 'Low';

            $bg['weight'] = 'bg-color7';
            $bg['bmi'] = 'bg-color7';
            $bg['body_fat'] = 'bg-color7';
            $bg['muscle_mass'] = 'bg-color7';
            $bg['water'] = 'bg-color9';
        } elseif ($bmi < 24) {
            $stat['weight'] = 'Low';
            $stat['bmi'] = 'Low';
            $stat['body_fat'] = 'Low';
            $stat['muscle_mass'] = 'Low';
            $stat['water'] = 'Low';

            $bg['weight'] = 'bg-color7';
            $bg['bmi'] = 'bg-color7';
            $bg['body_fat'] = 'bg-color7';
            $bg['muscle_mass'] = 'bg-color7';
            $bg['water'] = 'bg-color7';
        }

        if ($body_age <= $user->age) {
            $stat['body_age'] = 'Healthy';
            $bg['body_age'] = 'bg-color8';
        } elseif ($body_age > $user->age + 1) {
            $stat['body_age'] = 'Poor';
            $bg['body_age'] = 'bg-color9';
        }

        if ($visceral_fat >= 1 && $visceral_fat <= 9) {
            $stat['visceral_age'] = 'Normal';
            $bg['visceral_age'] = 'bg-color8';
        } elseif ($visceral_fat >= 10 && $visceral_fat <= 14) {
            $stat['visceral_age'] = 'High Risk';
            $bg['visceral_age'] = 'bg-color9';
        } elseif ($visceral_fat >= 15) {
            $stat['visceral_age'] = 'High Risk';
            $bg['visceral_age'] = 'bg-color9';
        }

        return view('bmi', compact(
            'weight',
            'bmi',
            'body_fat',
            'water',
            'muscle_mass',
            'visceral_fat',
            'bone_mass',
            'metabolism',
            'body_age',
            'stat',
            'bg'
        ));
    }

    /**
     * Fetch today status if not exist.
     */
    public function food_status(Request $request)
    {
        $user_id = Auth::id();
        $current_weight = WeightTracking::where('user_id', $user_id)
            ->latest()
            ->value('current_value');
        $today = Carbon::now();
        $day = $today->dayOfWeek;
        $data = [];

        $meal_plan_id = UserMealPlan::where('user_id', $user_id)->where('active', 1)->value('meal_plan_id');

        $data = MealPlanItem::join('food_items as fi', 'fi.id', '=', 'meal_plan_items.food_item_id')
            ->where('meal_plan_items.day', $day)
            ->where('meal_plan_items.meal_plan_id', $meal_plan_id)
            ->select('meal_plan_items.*', 'fi.name as food_item_name', 'fi.serving_unit as serving_unit', 'fi.calories', 'fi.protein', 'fi.total_fat', 'fi.total_carbohydrates', 'fi.image', 'fi.type')
            ->get();

        if ($data == null) {
            UserDietPlan::dispatch($user_id);

            $data = MealPlanItem::join('food_items as fi', 'fi.id', '=', 'meal_plan_items.food_item_id')
                ->where('meal_plan_items.day', $day)
                ->where('meal_plan_items.meal_plan_id', $meal_plan_id)
                ->select('meal_plan_items.*', 'fi.name as food_item_name', 'fi.serving_unit as serving_unit', 'fi.calories', 'fi.protein', 'fi.total_fat', 'fi.total_carbohydrates', 'fi.image', 'fi.type')
                ->get();
        }

        // Fetch id an name of active meals.
        $meals = Meal::select('id', 'name')->where('active', 1)->get()->toArray();

        // Create an array for data
        $arr = [];

        foreach ($meals as $item) {
            $arr[$item['id']] = [
                'name' => $item['name'],
                'data' => [],
            ];
        }

        // Traverse data and put each iten in corresponding meal.
        foreach ($data as $item) {
            $arr[$item->meal_id]['data'][] = $item->toArray();
        }

        $arr2 = [];

        foreach ($arr as $x) {
            $calories = array_column($x['data'], 'calories');
            $x['total_calories'] = array_sum($calories);

            if ($x['total_calories'] >= 1000) {
                $x['total_calories'] = ($x['total_calories'] / 1000).'Kcal';
            } else {
                $x['total_calories'] = $x['total_calories'].' Cal';
            }

            $arr2[] = $x;
        }

        $data = $arr2;

        return view('food-status', compact(
            'current_weight',
            'data'
        ));
    }
}
