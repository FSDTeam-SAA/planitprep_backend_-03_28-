<?php

namespace App\Jobs;

use App\Models\FoodItem;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use App\Models\User;
use App\Models\UserMealPlan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Support\AnthropicMessageClient;

class UserDayDietPlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user_id;

    public $day;

    public $messages;

    // public $tries = 4;
    // public $timeout = 99999999999999999;
    // public $numprocs = 8;
    /**
     * Create a new job instance.
     */
    public function __construct($user_id, $day, $messages)
    {
        $this->user_id = $user_id;
        $this->day = $day;
        $this->messages = $messages;
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '18048M');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userId = $this->user_id;
        $day = $this->day;
        $messages = $this->messages;

        $dietPlanFinalData = [];
        $dayWiseDietPlanData = [];

        // $prompt = " prompt=Suggest me a weekly diet plan to achieve my goals in 3 months. Provide me day " . $day . " for this weekly diet plan. Please provide a valid json in response.";

        $prompt = 'prompt=Suggest Day '.$day.' of a weekly diet plan for a 3-month goal. Respond with valid JSON.';
        // dd($messages);

        $response = $this->getDayWiseDietPlanFromChatGPT($messages.$prompt);
        $jsonString = $response->getContent();
        // \Log::info('aaaaaaaaaaa');
        \Log::info($jsonString);
        if (isset($jsonString)) {
            $responseInArray = json_decode($jsonString, true);
            // dd($jsonString);
            if (isset($responseInArray['status']) && $responseInArray['status'] == 'success') {

                $dayWiseDietPlanData[] = $responseInArray['data']['diet_chart'];

            }
            // }
            if (count($dayWiseDietPlanData) > 0) {
                $dietPlanFinalData = ['data' => ['weekly_diet_chart' => $dayWiseDietPlanData], 'user_id' => $userId];
                // dd($dietPlanFinalData);
                $this->saveUserDietPlan($dietPlanFinalData);
                // return response()->json($dietPlanFinalData);
            }
            // else {
            // if ($day < 7) {
            //     UserDietPlan::dispatch($userId);
            //     // $this->getWeeklyDietPlanFromChatGPT($request);
            //     //   }
            // }
        }
    }

    public function getDayWiseDietPlanFromChatGPT($messages)
    {
        // $expectedStructure = ["diet_chart"=>["day"=>"Day 1","meals"=>[["meal_time"=>"Breakfast","food_items"=>[["name"=>"Oats","quantity"=>"1 cup","data"=>["cal"=>150,"fat"=>3.00,"sat"=>0.5,"trs"=>0.0,"mno"=>0.2,"ply"=>0.1,"chl"=>0.00,"sod"=>2.00,"car"=>27.00,"fbr"=>3.00,"sgr"=>1.00,"ads"=>0.00,"pro"=>5.00,"a"=>0.00,"c"=>0.00,"ca"=>20.00,"irn"=>1.5,"pot"=>150.00,"d"=>0.00,"b6"=>0.2,"b12"=>0.0,"mag"=>40.00,"gix"=>55.00,"com"=>"Rich in complex carbs and dietary fiber","typ"=>"VE"]],["name"=>"Skimmed Milk","quantity"=>"1 cup","data"=>["cal"=>90,"fat"=>0.00,"sat"=>0.0,"trs"=>0.0,"mno"=>0.0,"ply"=>0.0,"chl"=>5.00,"sod"=>100.00,"car"=>12.00,"fbr"=>0.00,"sgr"=>12.00,"ads"=>0.00,"pro"=>8.00,"a"=>500.00,"c"=>0.00,"ca"=>300.00,"irn"=>0.0,"pot"=>380.00,"d"=>2.00,"b6"=>0.1,"b12"=>1.0,"mag"=>30.00,"gix"=>32.00,"com"=>"High in calcium and low in fat","typ"=>"VG"]],["name"=>"Almonds","quantity"=>"10 pieces","data"=>["cal"=>70,"fat"=>6.00,"sat"=>0.5,"trs"=>0.0,"mno"=>3.5,"ply"=>1.0,"chl"=>0.00,"sod"=>0.00,"car"=>2.00,"fbr"=>1.00,"sgr"=>0.5,"ads"=>0.00,"pro"=>3.00,"a"=>0.00,"c"=>0.00,"ca"=>30.00,"irn"=>0.5,"pot"=>100.00,"d"=>0.00,"b6"=>0.1,"b12"=>0.0,"mag"=>60.00,"gix"=>15.00,"com"=>"Rich in healthy fats and fiber","typ"=>"VG"]],["name"=>"Boiled Eggs","quantity"=>"2","data"=>["cal"=>140,"fat"=>10.00,"sat"=>3.0,"trs"=>0.0,"mno"=>4.0,"ply"=>1.0,"chl"=>210.00,"sod"=>70.00,"car"=>1.00,"fbr"=>0.00,"sgr"=>0.5,"ads"=>0.00,"pro"=>12.00,"a"=>270.00,"c"=>0.00,"ca"=>50.00,"irn"=>1.0,"pot"=>60.00,"d"=>1.00,"b6"=>0.1,"b12"=>1.0,"mag"=>10.00,"gix"=>0.00,"com"=>"Excellent source of protein","typ"=>"EG"]]]],["meal_time"=>"Morning Snack","food_items"=>[["name"=>"Apple","quantity"=>"1 medium","data"=>["cal"=>95,"fat"=>0.3,"sat"=>0.1,"trs"=>0.0,"mno"=>0.0,"ply"=>0.0,"chl"=>0.0,"sod"=>1.0,"car"=>25.0,"fbr"=>4.0,"sgr"=>19.0,"ads"=>0.0,"pro"=>0.5,"a"=>54.0,"c"=>8.4,"ca"=>6.0,"irn"=>0.1,"pot"=>195.0,"d"=>0.0,"b6"=>0.1,"b12"=>0.0,"mag"=>5.0,"gix"=>36.0,"com"=>"Good source of fiber and natural sugars","typ"=>"VE"]],["name"=>"Whey Protein","quantity"=>"1 scoop","data"=>["cal"=>120,"fat"=>1.0,"sat"=>0.5,"trs"=>0.0,"mno"=>0.2,"ply"=>0.1,"chl"=>30.0,"sod"=>50.0,"car"=>3.0,"fbr"=>0.0,"sgr"=>1.5,"ads"=>0.0,"pro"=>24.0,"a"=>0.0,"c"=>0.0,"ca"=>150.0,"irn"=>0.5,"pot"=>120.0,"d"=>0.0,"b6"=>0.5,"b12"=>0.4,"mag"=>20.0,"gix"=>15.0,"com"=>"High in protein and low in fats","typ"=>"VG"]]]],["meal_time"=>"Lunch","food_items"=>[["name"=>"Grilled Chicken","quantity"=>"150g","data"=>["cal"=>300,"fat"=>16.0,"sat"=>4.0,"trs"=>0.0,"mno"=>5.0,"ply"=>3.0,"chl"=>90.0,"sod"=>80.0,"car"=>0.0,"fbr"=>0.0,"sgr"=>0.0,"ads"=>0.0,"pro"=>35.0,"a"=>0.0,"c"=>0.0,"ca"=>20.0,"irn"=>1.5,"pot"=>230.0,"d"=>0.0,"b6"=>0.7,"b12"=>1.0,"mag"=>30.0,"gix"=>0.0,"com"=>"Rich in protein","typ"=>"NV"]],["name"=>"Brown Rice","quantity"=>"1 cup","data"=>["cal"=>215,"fat"=>2.0,"sat"=>0.5,"trs"=>0.0,"mno"=>0.5,"ply"=>0.5,"chl"=>0.0,"sod"=>10.0,"car"=>45.0,"fbr"=>8.0,"sgr"=>0.5,"ads"=>0.0,"pro"=>5.0,"a"=>0.0,"c"=>0.0,"ca"=>10.0,"irn"=>0.8,"pot"=>150.0,"d"=>0.0,"b6"=>0.2,"b12"=>0.0,"mag"=>85.0,"gix"=>50.0,"com"=>"Complex carbs source","typ"=>"VE"]],["name"=>"Steamed Vegetables","quantity"=>"1 cup","data"=>["cal"=>50,"fat"=>0.0,"sat"=>0.0,"trs"=>0.0,"mno"=>0.0,"ply"=>0.0,"chl"=>0.0,"sod"=>40.0,"car"=>10.0,"fbr"=>3.0,"sgr"=>2.0,"ads"=>0.0,"pro"=>2.0,"a"=>500.0,"c"=>10.0,"ca"=>20.0,"irn"=>0.5,"pot"=>180.0,"d"=>0.0,"b6"=>0.1,"b12"=>0.0,"mag"=>10.0,"gix"=>15.0,"com"=>"Low calorie, high in fiber","typ"=>"VG"]]]],["meal_time"=>"Evening Snack","food_items"=>[["name"=>"Paneer Tikka","quantity"=>"100g","data"=>["cal"=>300,"fat"=>22.0,"sat"=>12.0,"trs"=>0.0,"mno"=>5.0,"ply"=>2.0,"chl"=>40.0,"sod"=>60.0,"car"=>8.0,"fbr"=>0.0,"sgr"=>2.0,"ads"=>0.0,"pro"=>15.0,"a"=>100.0,"c"=>0.0,"ca"=>300.0,"irn"=>0.8,"pot"=>100.0,"d"=>0.0,"b6"=>0.3,"b12"=>0.5,"mag"=>40.0,"gix"=>30.0,"com"=>"Good protein source, moderate fat","typ"=>"VG"]]]],["meal_time"=>"Dinner","food_items"=>[["name"=>"Fish Curry","quantity"=>"150g","data"=>["cal"=>250,"fat"=>12.0,"sat"=>3.0,"trs"=>0.0,"mno"=>5.0,"ply"=>2.0,"chl"=>60.0,"sod"=>150.0,"car"=>5.0,"fbr"=>1.0,"sgr"=>1.0,"ads"=>0.0,"pro"=>28.0,"a"=>200.0,"c"=>0.0,"ca"=>30.0,"irn"=>2.0,"pot"=>250.0,"d"=>0.0,"b6"=>0.5,"b12"=>2.5,"mag"=>30.0,"gix"=>0.0,"com"=>"Rich in protein and omega-3","typ"=>"NV"]],["name"=>"Quinoa","quantity"=>"1 cup","data"=>["cal"=>222,"fat"=>4.0,"sat"=>0.4,"trs"=>0.0,"mno"=>1.5,"ply"=>0.5,"chl"=>0.0,"sod"=>13.0,"car"=>39.0,"fbr"=>5.0,"sgr"=>0.0,"ads"=>0.0,"pro"=>4.0,"a"=>0.0,"c"=>0.0,"ca"=>17.0,"irn"=>1.5,"pot"=>172.0,"d"=>0.0,"b6"=>0.1,"b12"=>0.0,"mag"=>59.0,"gix"=>53.0,"com"=>"Complete protein, low GI","typ"=>"V"]]]]]]];
        $expectedStructure = ['diet_chart' => ['day' => 'Day 1', 'meals' => [['meal_time' => 'Breakfast', 'food_items' => [['n' => 'Oats', 'q' => '1 cup', 'data' => ['cal' => 150, 'fat' => 3, 'sat' => 0.5, 'trs' => 0, 'mno' => 0.2, 'ply' => 0.1, 'chl' => 0, 'sod' => 2, 'car' => 27, 'fbr' => 3, 'sgr' => 1, 'ads' => 0, 'pro' => 5, 'a' => 0, 'c' => 0, 'ca' => 20, 'irn' => 1.5, 'pot' => 150, 'd' => 0, 'b6' => 0.2, 'b12' => 0, 'mag' => 40, 'gix' => 55, 'com' => 'Rich in complex carbs and dietary fiber', 'typ' => 'VE']], ['n' => 'Skimmed Milk', 'q' => '1 cup', 'data' => ['cal' => 90, 'fat' => 0, 'sat' => 0, 'trs' => 0, 'mno' => 0, 'ply' => 0, 'chl' => 5, 'sod' => 100, 'car' => 12, 'fbr' => 0, 'sgr' => 12, 'ads' => 0, 'pro' => 8, 'a' => 500, 'c' => 0, 'ca' => 300, 'irn' => 0, 'pot' => 380, 'd' => 2, 'b6' => 0.1, 'b12' => 1, 'mag' => 30, 'gix' => 32, 'com' => 'High in calcium and low in fat', 'typ' => 'VG']], ['n' => 'Almonds', 'q' => '10 pieces', 'data' => ['cal' => 70, 'fat' => 6, 'sat' => 0.5, 'trs' => 0, 'mno' => 3.5, 'ply' => 1, 'chl' => 0, 'sod' => 0, 'car' => 2, 'fbr' => 1, 'sgr' => 0.5, 'ads' => 0, 'pro' => 3, 'a' => 0, 'c' => 0, 'ca' => 30, 'irn' => 0.5, 'pot' => 100, 'd' => 0, 'b6' => 0.1, 'b12' => 0, 'mag' => 60, 'gix' => 15, 'com' => 'Rich in healthy fats and fiber', 'typ' => 'VG']], ['n' => 'Boiled Eggs', 'q' => '2', 'data' => ['cal' => 140, 'fat' => 10, 'sat' => 3, 'trs' => 0, 'mno' => 4, 'ply' => 1, 'chl' => 210, 'sod' => 70, 'car' => 1, 'fbr' => 0, 'sgr' => 0.5, 'ads' => 0, 'pro' => 12, 'a' => 270, 'c' => 0, 'ca' => 50, 'irn' => 1, 'pot' => 60, 'd' => 1, 'b6' => 0.1, 'b12' => 1, 'mag' => 10, 'gix' => 0, 'com' => 'Excellent source of protein', 'typ' => 'EG']]]], ['meal_time' => 'Morning Snack', 'food_items' => [['n' => 'Apple', 'q' => '1 medium', 'data' => ['cal' => 95, 'fat' => 0.3, 'sat' => 0.1, 'trs' => 0, 'mno' => 0, 'ply' => 0, 'chl' => 0, 'sod' => 1, 'car' => 25, 'fbr' => 4, 'sgr' => 19, 'ads' => 0, 'pro' => 0.5, 'a' => 54, 'c' => 8.4, 'ca' => 6, 'irn' => 0.1, 'pot' => 195, 'd' => 0, 'b6' => 0.1, 'b12' => 0, 'mag' => 5, 'gix' => 36, 'com' => 'Good source of fiber and natural sugars', 'typ' => 'VE']], ['n' => 'Whey Protein', 'q' => '1 scoop', 'data' => ['cal' => 120, 'fat' => 1, 'sat' => 0.5, 'trs' => 0, 'mno' => 0.2, 'ply' => 0.1, 'chl' => 30, 'sod' => 50, 'car' => 3, 'fbr' => 0, 'sgr' => 1.5, 'ads' => 0, 'pro' => 24, 'a' => 0, 'c' => 0, 'ca' => 150, 'irn' => 0.5, 'pot' => 120, 'd' => 0, 'b6' => 0.5, 'b12' => 0.4, 'mag' => 20, 'gix' => 15, 'com' => 'High in protein and low in fats', 'typ' => 'VG']]]], ['meal_time' => 'Lunch', 'food_items' => [['n' => 'Grilled Chicken', 'q' => '150g', 'data' => ['cal' => 300, 'fat' => 16, 'sat' => 4, 'trs' => 0, 'mno' => 5, 'ply' => 3, 'chl' => 90, 'sod' => 80, 'car' => 0, 'fbr' => 0, 'sgr' => 0, 'ads' => 0, 'pro' => 35, 'a' => 0, 'c' => 0, 'ca' => 20, 'irn' => 1.5, 'pot' => 230, 'd' => 0, 'b6' => 0.7, 'b12' => 1, 'mag' => 30, 'gix' => 0, 'com' => 'Rich in protein', 'typ' => 'NV']], ['n' => 'Brown Rice', 'q' => '1 cup', 'data' => ['cal' => 215, 'fat' => 2, 'sat' => 0.5, 'trs' => 0, 'mno' => 0.5, 'ply' => 0.5, 'chl' => 0, 'sod' => 10, 'car' => 45, 'fbr' => 8, 'sgr' => 0.5, 'ads' => 0, 'pro' => 5, 'a' => 0, 'c' => 0, 'ca' => 10, 'irn' => 0.8, 'pot' => 150, 'd' => 0, 'b6' => 0.2, 'b12' => 0, 'mag' => 85, 'gix' => 50, 'com' => 'Complex carbs source', 'typ' => 'VE']], ['n' => 'Steamed Vegetables', 'q' => '1 cup', 'data' => ['cal' => 50, 'fat' => 0, 'sat' => 0, 'trs' => 0, 'mno' => 0, 'ply' => 0, 'chl' => 0, 'sod' => 40, 'car' => 10, 'fbr' => 3, 'sgr' => 2, 'ads' => 0, 'pro' => 2, 'a' => 500, 'c' => 10, 'ca' => 20, 'irn' => 0.5, 'pot' => 180, 'd' => 0, 'b6' => 0.1, 'b12' => 0, 'mag' => 10, 'gix' => 15, 'com' => 'Low calorie, high in fiber', 'typ' => 'VG']]]], ['meal_time' => 'Evening Snack', 'food_items' => [['n' => 'Paneer Tikka', 'q' => '100g', 'data' => ['cal' => 300, 'fat' => 22, 'sat' => 12, 'trs' => 0, 'mno' => 5, 'ply' => 2, 'chl' => 40, 'sod' => 60, 'car' => 8, 'fbr' => 0, 'sgr' => 2, 'ads' => 0, 'pro' => 15, 'a' => 100, 'c' => 0, 'ca' => 300, 'irn' => 0.8, 'pot' => 100, 'd' => 0, 'b6' => 0.3, 'b12' => 0.5, 'mag' => 40, 'gix' => 30, 'com' => 'Good protein source, moderate fat', 'typ' => 'VG']]]], ['meal_time' => 'Dinner', 'food_items' => [['n' => 'Fish Curry', 'q' => '150g', 'data' => ['cal' => 250, 'fat' => 12, 'sat' => 3, 'trs' => 0, 'mno' => 5, 'ply' => 2, 'chl' => 60, 'sod' => 150, 'car' => 5, 'fbr' => 1, 'sgr' => 1, 'ads' => 0, 'pro' => 28, 'a' => 200, 'c' => 0, 'ca' => 30, 'irn' => 2, 'pot' => 250, 'd' => 0, 'b6' => 0.5, 'b12' => 2.5, 'mag' => 30, 'gix' => 0, 'com' => 'Rich in protein and omega-3', 'typ' => 'NV']], ['n' => 'Quinoa', 'q' => '1 cup', 'data' => ['cal' => 222, 'fat' => 4, 'sat' => 0.4, 'trs' => 0, 'mno' => 1.5, 'ply' => 0.5, 'chl' => 0, 'sod' => 13, 'car' => 39, 'fbr' => 5, 'sgr' => 0, 'ads' => 0, 'pro' => 4, 'a' => 0, 'c' => 0, 'ca' => 17, 'irn' => 1.5, 'pot' => 172, 'd' => 0, 'b6' => 0.1, 'b12' => 0, 'mag' => 59, 'gix' => 53, 'com' => 'Complete protein, low GI', 'typ' => 'V']]]]]]];
        // Define your desired structured response
        $desiredResponseStructure = [
            'status' => 'success',
            'message' => 'Response retrieved successfully',
            'data' => $expectedStructure,
            'timestamp' => now()->toDateTimeString(),
        ];

        // Create a system message to guide the model
        // $systemMessage = "Please generate a complete 1-day diet chart in the following JSON format:\n" . json_encode($desiredResponseStructure, JSON_PRETTY_PRINT);
        $systemMessage = "Generate a 1-day diet chart in this JSON format (omit values that are 0):\n".json_encode($desiredResponseStructure, JSON_PRETTY_PRINT);
        // Prepare the user input message
        $userInputMessage = $messages;
        \Log::info('userInputMessage');
        \Log::info($userInputMessage);
        // dd($userInputMessage);
        // dd($userInputMessage);
        // Previous OpenAI request kept commented for reference.
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer '.$apiKey,
        // ])->timeout(600)->post('https://api.openai.com/v1/chat/completions', [...]);

        $response = AnthropicMessageClient::send($systemMessage, $userInputMessage, 2500);
        // dd($response);
        // Handle the Claude API response
        if (($response['status'] ?? 'error') === 'success') {
            $content = $response['content'];
            // dd($content);
            // Attempt to decode the content to ensure it's in the correct format
            $decodedContent = json_decode($content, true);
            // dd($decodedContent);
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
                // dd($decodedContent);
                // Populate the data if the response is valid
                $desiredResponseStructure['data'] = $decodedContent['data'] ?? [];
            }

            // dd($desiredResponseStructure);
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

    public function saveUserDietPlan($request)
    {

        $user_id = $request['user_id'];
        $user = User::find($user_id);
        if (isset($request['data']['weekly_diet_chart'])) {
            if (isset($user->goal)) {
                $userMealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();
                if ($userMealPlan) {
                    $mealPlanId = $userMealPlan->meal_plan_id;
                    $planDay = MealPlanItem::where('meal_plan_id', $userMealPlan->meal_plan_id)->groupBy('day')->pluck('day')->count();
                    if ($planDay == 7) {
                        $mealPlanId = MealPlan::insertGetId(['name' => $user->goal->name, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]);
                        UserMealPlan::insertGetId(['user_id' => $user_id, 'meal_plan_id' => $mealPlanId, 'active' => 1, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]);
                    }
                } else {
                    $mealPlanId = MealPlan::insertGetId(['name' => $user->goal->name, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]);
                    UserMealPlan::insertGetId(['user_id' => $user_id, 'meal_plan_id' => $mealPlanId, 'active' => 1, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]);
                }

                if ($mealPlanId > 0) {
                    foreach ($request['data']['weekly_diet_chart'] as $key1 => $value) {

                        $day = (int) preg_replace('/[^0-9]/', '', $value['day']);
                        if ($day > 0) {
                            if (isset($value['meals']) && count($value['meals']) > 0) {
                                foreach ($value['meals'] as $key2 => $meal) {
                                    $mealSql = Meal::select('id')->where(DB::raw('LOWER(name)'), strtolower($meal['meal_time']))->first();
                                    if ($mealSql) {
                                        if (isset($meal['food_items']) && count($meal['food_items']) > 0) {
                                            foreach ($meal['food_items'] as $key3 => $fItem) {
                                                $foodItemsData = [];
                                                $foodItemsData['name'] = $fItem['n'];

                                                if (strpos($fItem['q'], '/') !== false) {
                                                    [$num, $den] = explode('/', $value);
                                                    if (is_numeric($num) && is_numeric($den) && (float) $den != 0) {
                                                        $serving_size = (float) $num / (float) $den;
                                                        $foodItemsData['serving_size'] = $serving_size;
                                                    }
                                                } else {
                                                    $foodItemsData['serving_size'] = (int) preg_replace('/[^0-9]/', '', $fItem['q']);
                                                }

                                                $foodItemsData['serving_unit'] = preg_replace('/[^a-zA-Z]/', '', $fItem['q']);
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
                                                    $foodItemsData['created_at'] = date('Y-m-d h:i:s');
                                                    $foodItemsData['updated_at'] = date('Y-m-d h:i:s');
                                                }

                                                $foodItemsSql = FoodItem::select('id')->where(DB::raw('LOWER(name)'), strtolower($fItem['n']))->first();
                                                if (! $foodItemsSql) {
                                                    $foodItemId = FoodItem::insertGetId($foodItemsData);
                                                } else {
                                                    $foodItemId = $foodItemsSql->id;
                                                }
                                                $mealPlanItemData = [];
                                                $mealPlanItemData['meal_id'] = $mealSql->id;
                                                $mealPlanItemData['meal_plan_id'] = $mealPlanId;
                                                $mealPlanItemData['food_item_id'] = $foodItemId;
                                                $mealPlanItemData['quantity'] = (int) preg_replace('/[^0-9]/', '', $fItem['q']);
                                                $mealPlanItemData['day'] = $day;
                                                $mealPlanItemData['created_at'] = date('Y-m-d h:i:s');
                                                $mealPlanItemData['updated_at'] = date('Y-m-d h:i:s');

                                                MealPlanItem::insertGetId($mealPlanItemData);
                                                \Log::info('inserted');
                                            }
                                        }
                                        // else {
                                        //     return response()->json([
                                        //         'status' => false,
                                        //         'msg' => 'Food items are missing!',
                                        //     ]);
                                        // }
                                    }
                                }
                            }
                            // else {
                            //     return response()->json([
                            //         'status' => false,
                            //         'msg' => 'Meals are missing!',
                            //     ]);
                            // }
                        }
                        // else {
                        //     return response()->json([
                        //         'status' => false,
                        //         'msg' => 'Invalid days!',
                        //     ]);
                        // }
                    }
                    // return response()->json([
                    //     'status' => true,
                    //     'msg' => 'Weekly diet plan added successfully!',
                    // ]);
                }
                // else {
                //     return response()->json([
                //         'status' => true,
                //         'msg' => 'No meal plan found!',
                //     ]);
                // }
            }
            // else {
            //     return response()->json([
            //         'status' => false,
            //         'msg' => 'Your goal is not set!',
            //     ]);
            // }
        }
        // else {
        //     return response()->json([
        //         'status' => false,
        //         'msg' => 'Weekly diet chart should be of 7 days!',
        //     ]);
        // }
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
}
