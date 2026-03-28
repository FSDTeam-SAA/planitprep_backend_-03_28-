<?php


namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Recipe;
use App\Models\BatchMealConfig;
use App\Models\BatchMealInventory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Support\AnthropicMessageClient;

class BatchMealController extends Controller
{

public function generateBatchMeal(Request $request)
{
    $userId    = $request->user_id;
    $isRefresh = $request->is_refresh ?? 0;

    $user = User::find($userId);
    if (!$user) {
        return response()->json(['status'=>false,'msg'=>'User not found'],404);
    }

    $config = BatchMealConfig::where('user_id',$userId)
                ->where('is_active',1)
                ->first();

    if (!$config) {
        return response()->json(['status'=>false,'msg'=>'Batch config not found'],404);
    }

    $today = Carbon::today();
    $totalCalories = $user->daily_calorie_intake ?? 0;

    if ($totalCalories <= 0) {
        return response()->json(['status'=>false,'msg'=>'Daily calorie intake missing'],400);
    }

    // -------------------------
    // Check cooking day
    // -------------------------
    $daysArray = explode(',', $config->days_of_cook);
    $todayNumber = Carbon::now()->dayOfWeekIso;
    $isCookingDay = in_array($todayNumber, $daysArray);

    // -------------------------
    // Check existing active meals for today
    // -------------------------
    $existingRecipes = Recipe::where('userid',$userId)
        ->where('meal_type','batch')
        ->where('preparation_type','BATCH_MEAL')
        ->whereDate('date',$today)
        ->where('is_active',1)
        ->get();

    // ==============================
    // DECISION LOGIC
    // ==============================

    if ($isRefresh == 0 && $existingRecipes->count() > 0) {

        // Return existing data
        $responseMeals = [];

        foreach ($existingRecipes as $recipe) {

            $inventory = BatchMealInventory::where('recipe_id',$recipe->id)
                ->where('is_active',1)
                ->first();

            $responseMeals[] = [
                'id' => $recipe->id,
                'recipeName' => $recipe->recipeName,
                'prepTime' => $recipe->prepTime,
                'calories' => $recipe->calories,
                'isVegetarian' => $recipe->isVegetarian,
                'recipePoints' => $recipe->recipePoints,
                'grocery_list' => $recipe->grocery_list,true,
                'total_portions' => $inventory->total_portions ?? 0,
                'used_portions' => $inventory->used_portions ?? 0,
                'remaining_portions' => $inventory->remaining_portions ?? 0,
                'calories_per_portion' => $inventory->calories_per_portion ?? 0,
            ];
        }

        return response()->json([
            'status'=>true,
            'message'=>'Batch meal fetched successfully',
            'data'=>$responseMeals
        ]);
    }

    // ==============================
    // GENERATE NEW MEALS
    // ==============================

    DB::beginTransaction();

    try {

        // deactivate old meals
        Recipe::where('userid',$userId)
            ->where('meal_type','batch')
            ->where('preparation_type','BATCH_MEAL')
            ->whereDate('date',$today)
            ->update(['is_active'=>0]);

        BatchMealInventory::where('user_id',$userId)
            ->where('is_active',1)
            ->update(['is_active'=>0]);

        // calculate days
        sort($daysArray);
        $nextCookDay = null;

        foreach ($daysArray as $day) {
            if ($day >= $todayNumber) {
                $nextCookDay = $day;
                break;
            }
        }

        if (!$nextCookDay) {
            $nextCookDay = $daysArray[0];
        }

        $dayDifference = ($nextCookDay - $todayNumber);
        if ($dayDifference < 0) $dayDifference += 7;

        $totalDays = $dayDifference + 1;

        //calculation of calories per portions
        $caloriesPerPortion = round($totalCalories / $request->number_of_meals);

        $recipeData = $this->generateRecipeFromGPT($user,$caloriesPerPortion,$totalDays,$request->number_of_meals);

        if (!$recipeData) {
            throw new \Exception("Recipe generation failed");
        }

        $responseMeals = [];

        foreach ($recipeData['meals'] as $meal) {

            $recipe = Recipe::create([
                'userid'        => $userId,
                'date'          => $today,
                'meal_type'     => 'batch',
                'preparation_type'     => 'BATCH_MEAL',
                'type'          => 'ai',
                'version'       => 1,
                'is_active'     => 1,
                'recipeName'    => $meal['recipeName'],
                'prepTime'      => $meal['prepTime'],
                'calories'      => $meal['calories'],
                'isVegetarian'  => $meal['isVegetarian'],
                'recipePoints'  => $meal['recipePoints'],
                'grocery_list'  => $meal['grocery_list'],
                'createDate'    => now(),
                'modifyDate'    => now()
            ]);

            $inventory = BatchMealInventory::create([
                'user_id'             => $userId,
                'recipe_id'           => $recipe->id,
                'total_portions'      => $totalDays,
                'used_portions'       => 0,
                'remaining_portions'  => $totalDays,
                'calories_per_portion'=> $caloriesPerPortion,
                'is_active'           => 1
            ]);

            $responseMeals[] = [
                'id' => $recipe->id,
                'recipeName' => $recipe->recipeName,
                'prepTime' => $recipe->prepTime,
                'calories' => $recipe->calories,
                'isVegetarian' => $recipe->isVegetarian,
                'recipePoints' => $recipe->recipePoints,
                'grocery_list' => $meal['grocery_list'],
                'total_portions' => $inventory->total_portions,
                'used_portions' => $inventory->used_portions,
                'remaining_portions' => $inventory->remaining_portions,
                'calories_per_portion' => $inventory->calories_per_portion,
            ];
        }

        DB::commit();

        return response()->json([
            'status'=>true,
            'message'=>'Batch meal generated successfully',
            'data'=>$responseMeals
        ]);

    } catch (\Exception $e) {

        DB::rollBack();
        return response()->json([
            'status'=>false,
            'msg'=>$e->getMessage()
        ],500);
    }
}

    // ======================================
    // GPT CALL FUNCTION
    // ======================================
private function generateRecipeFromGPT($user, $caloriesPerPortion, $totalDays, $mealsNumber)
{
    $systemMessage = "
You are a professional nutritionist and chef.

User will cook today and eat same food for {$totalDays} days.

Generate EXACTLY {$mealsNumber} completely different meals.

Each meal:
- {$caloriesPerPortion} kcal per portion
- Divided into {$totalDays} equal portions
- Max 60 min cooking time
- prepTime format: '45 min'
- calories format: '{$caloriesPerPortion} kcal'
- recipePoints must use '\\n' for line breaks
- Include detailed cooking steps
- Include how to divide into {$totalDays} portions

IMPORTANT:
- Return ONLY JSON
- NO outer object
- NO explanation text
- NO combined grocery list
- Each meal must contain its OWN grocery_list

Return EXACT format:

{
  \"meals\": [
    {
      \"recipeName\": string,
      \"prepTime\": string,
      \"calories\": string,
      \"isVegetarian\": boolean,
      \"recipePoints\": string,
      \"grocery_list\": [
        {
          \"name\": string,
          \"amount\": string,
          \"isVegetarian\": boolean
        }
      ]
    }
  ]
}
";

    try {
        // Previous OpenAI request kept commented for reference.
        // $response = Http::timeout(120)
        //     ->withHeaders([
        //         'Authorization' => 'Bearer ' . $apiKey,
        //         'Content-Type'  => 'application/json'
        //     ])
        //     ->post('https://api.openai.com/v1/chat/completions', [...]);

        $response = AnthropicMessageClient::send(
            $systemMessage,
            'Generate the batch meals now.',
            2500,
            [],
            ['temperature' => 0.3]
        );

        if (($response['status'] ?? 'error') !== 'success') {
            return null;
        }

        $content = $response['content'] ?? null;

        if (!$content) {
            return null;
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            return null;
        }

        // ===============================
        // STRICT VALIDATION
        // ===============================

        if (!isset($data['meals']) || !is_array($data['meals'])) {
            return null;
        }

        if (count($data['meals']) !== $mealsNumber) {
            return null;
        }

        foreach ($data['meals'] as $meal) {

            if (
                !isset($meal['recipeName']) ||
                !isset($meal['prepTime']) ||
                !isset($meal['calories']) ||
                !isset($meal['isVegetarian']) ||
                !isset($meal['recipePoints']) ||
                !isset($meal['grocery_list']) ||
                !is_array($meal['grocery_list'])
            ) {
                return null;
            }
        }

        return $data;

    } catch (\Exception $e) {
        return null;
    }
}

//======================================
//consume batch meal from inventory table
//======================================
public function consumePortion(Request $request)
{
    $request->validate([
        'user_id'   => 'required|integer',
        'recipe_id' => 'required|integer'
    ]);

    $userId   = $request->user_id;
    $recipeId = $request->recipe_id;

    // Get inventory for specific recipe
    $inventory = BatchMealInventory::where('user_id', $userId)
        ->where('recipe_id', $recipeId)
        ->where('is_active', 1)
        ->first();

    if (!$inventory) {
        return response()->json([
            'status' => false,
            'msg' => 'Active inventory not found for this recipe'
        ], 404);
    }

    if ($inventory->remaining_portions <= 0) {
        return response()->json([
            'status' => false,
            'msg' => 'No remaining portions'
        ], 400);
    }

    // Update portions
    $inventory->used_portions += 1;
    $inventory->remaining_portions -= 1;

    // If finished, deactivate
    if ($inventory->remaining_portions == 0) {
        $inventory->is_active = 0;
    }

    $inventory->save();

    return response()->json([
        'status' => true,
        'message' => 'Portion consumed successfully',
        'data' => [
            'recipe_id' => $recipeId,
            'used_portions' => $inventory->used_portions,
            'remaining_portions' => $inventory->remaining_portions,
            'is_active' => $inventory->is_active
        ]
    ]);
}




//============================================================================
//this help to check that is active recepies present for batch meal or not
//============================================================================
private function checkAndTriggerNewBatch($userId)
{
    $config = BatchMealConfig::where('user_id', $userId)
        ->where('is_active', 1)
        ->first();

    if (!$config) {
        return;
    }

    $todayDate   = \Carbon\Carbon::today();
    $todayNumber = \Carbon\Carbon::now()->dayOfWeekIso; // 1-7

    $daysArray = explode(',', $config->days_of_cook);

    // If today is NOT cooking day → stop
    if (!in_array($todayNumber, $daysArray)) {
        return;
    }

    // Check if active batch recipes already exist for today
    $alreadyGenerated = Recipe::where('userid', $userId)
        ->where('meal_type', 'batch')
        ->where('prep_type', 'BATCH_MEAL')
        ->whereDate('date', $todayDate)
        ->where('is_active', 1)
        ->exists();

    if ($alreadyGenerated) {
        return;
    }

    // Call generator directly (no fake Request)
    $this->generateBatchMeal(
        new \Illuminate\Http\Request([
            'user_id' => $userId,
            'is_refresh' => 0
        ])
    );
}
}
