<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiResponse;
use App\Models\Recipe;
use App\Models\FavouriteMeal;
use Carbon\Carbon;

class FavouriteMealController extends Controller
{
public function addFavourite(Request $request)
{
    // Validate Request
    $request->validate([
        'user_id'   => 'required|integer',
        'date'      => 'required|date',
        'type'      => 'required|string',
        'meal_type' => 'required|string',
        'prep_type' => 'required|string',
    ]);

    $formattedDate = \Carbon\Carbon::parse($request->date)->format('Y-m-d');

    // Normalize meal_type (Breakfast → BREAKFAST)
    $mealType = strtoupper(str_replace(' ', '_', trim($request->meal_type)));

    /*
    |--------------------------------------------------------------------------
    |  Find Recipe
    |--------------------------------------------------------------------------
    */

    $recipe = Recipe::where('userid', $request->user_id)
        ->where('date', $formattedDate)
        ->where('type', $request->type)
        ->where('meal_type', $mealType)
        ->where('preparation_type', $request->prep_type)
        ->where('is_active', 1)
        ->where('isDeleted', 0)
        ->first();

    if (!$recipe) {
        return response()->json([
            'status'  => false,
            'message' => 'Recipe not found'
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    |  Update isFavorate
    |--------------------------------------------------------------------------
    */

    $recipe->update([
        'isFavorate' => 1,
        'modifyDate' => now()
    ]);

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'status'  => true,
        'message' => 'Recipe marked as favourite successfully',
        'data'    => $recipe
    ]);
}


    // Soft Delete Function
    public function deleteFavourite(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $favourite = Recipe::where('id', $request->id)->first();

        if (!$favourite) {
            return response()->json([
                'status'  => false,
                'message' => 'Favourite meal not found'
            ], 404);
        }

        $favourite->update([
            'isFavorate' => 0
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Favourite meal deleted successfully'
        ]);
    }



    //get all favourite meals 
public function getFavouriteMeals(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer',
    ]);

    /*
    |--------------------------------------------------------------------------
    |  Fetch Favourite Recipes
    |--------------------------------------------------------------------------
    */

    $favourites = Recipe::where('userid', $request->user_id)
        ->where('isFavorate', 1)
        ->where('isDeleted', 0)
        ->orderBy('modifyDate', 'desc')
        ->get();

    if ($favourites->isEmpty()) {
        return response()->json([
            'status'  => true,
            'message' => 'No favourite meals found for this user',
            'user_id' => $request->user_id,
            'count'   => 0,
            'data'    => []
        ]);
    }

    return response()->json([
        'status'  => true,
        'message' => 'Favourite meals fetched successfully',
        'user_id' => $request->user_id,
        'count'   => $favourites->count(),
        'data'    => $favourites
    ]);
}




public function removeFavourite(Request $request)
{
    $request->validate([
        'user_id'   => 'required|integer',
        'date'      => 'required|date',
        'type'      => 'required|string',
        'meal_type' => 'required|string',
        'prep_type' => 'required|string',
    ]);

    $formattedDate = \Carbon\Carbon::parse($request->date)->format('Y-m-d');

    // Normalize meal type
    $mealType = strtoupper(str_replace(' ', '_', trim($request->meal_type)));

    /*
    |--------------------------------------------------------------------------
    |  Find Recipe
    |--------------------------------------------------------------------------
    */

    $recipe = Recipe::where('userid', $request->user_id)
        ->where('date', $formattedDate)
        ->where('type', $request->type)
        ->where('meal_type', $mealType)
        ->where('preparation_type', $request->prep_type)
        ->where('is_active', 1)
        ->where('isDeleted', 0)
        ->first();

    if (!$recipe) {
        return response()->json([
            'status'  => false,
            'message' => 'Recipe not found'
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    |  Remove Favourite (Set 0)
    |--------------------------------------------------------------------------
    */

    $recipe->update([
        'isFavorate' => 0,
        'modifyDate' => now()
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Recipe removed from favourite successfully'
    ]);
}

//check is favourite or not
public function checkIsFavourite(Request $request)
{
    $request->validate([
        'user_id'   => 'required|integer',
        'date'      => 'required|date',
        'type'      => 'required|string',
        'prep_type' => 'required|string',
    ]);

    $formattedDate = \Carbon\Carbon::parse($request->date)->format('Y-m-d');

    /*
    |--------------------------------------------------------------------------
    |  Get All Active + Favourite Recipes
    |--------------------------------------------------------------------------
    */

    $recipes = Recipe::where('userid', $request->user_id)
        ->where('date', $formattedDate)
        ->where('type', $request->type)
        ->where('preparation_type', $request->prep_type)
        ->where('is_active', 1)
        ->where('isFavorate', 1)
        ->where('isDeleted', 0)
        ->get();

    if ($recipes->isEmpty()) {
        return response()->json([
            'status'          => true,
            'favourite_types' => ""
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    |  Map Meal Types To Enum
    |--------------------------------------------------------------------------
    */

    $enumValues = [];

    foreach ($recipes as $recipe) {

        $mealName = strtolower($recipe->meal_type);

        if (str_contains($mealName, 'lunch')) {
            $enumValues[] = 1;
        } elseif (str_contains($mealName, 'breakfast')) {
            $enumValues[] = 2;
        } elseif (str_contains($mealName, 'dinner')) {
            $enumValues[] = 3;
        } elseif (str_contains($mealName, 'snack')) {
            $enumValues[] = 4;
        }
    }

    // Remove duplicates & reindex
    $enumValues = array_values(array_unique($enumValues));

    // Convert to comma-separated string
    $favouriteTypes = implode(',', $enumValues);

    return response()->json([
        'status'          => true,
        'user_id'         => $request->user_id,
        'type'            => $request->type,
        'favourite_types' => $favouriteTypes
    ]);
}



public function checkSM_IsFavourite(Request $request)
{
    $request->validate([
        'user_id'   => 'required|integer',
        'date'      => 'required|date',
        'type'      => 'required|string',
        'prep_type' => 'required|string',
    ]);

    $formattedDate = \Carbon\Carbon::parse($request->date)->format('Y-m-d');

    /*
    |--------------------------------------------------------------------------
    |  Get All Active + Favourite Recipes
    |--------------------------------------------------------------------------
    */

    $recipes = Recipe::where('userid', $request->user_id)
        ->where('date', $formattedDate)
        ->where('type', $request->type)
        ->where('preparation_type', $request->prep_type)
        ->where('is_active', 1)
        ->where('isDeleted', 0)
        ->first();

    if (!$recipes) {
        return response()->json([
            'status'          => true,
            'favourite_types' => ""
        ]);
    }



    return response()->json([
        'status'          => true,
        'user_id'         => $request->user_id,
        'type'            => $request->type,
        'is_favourite'    => $recipes->isFavorate ?? 0
    ]);
}


    // public function updateIsFavourite(Request $request)
    // {
    //     // Validate request
    //     $request->validate([
    //         'user_id' => 'required|integer',
    //         'type'    => 'required|string',
    //         'date'    => 'required|string',
    //     ]);

    //     $formattedDate = Carbon::parse($request->date)->format('Y-m-d');

    //     // Find AI response
    //     $aiResponse = AiResponse::where('user_id', $request->user_id)
    //         ->where('type', $request->type)
    //         ->where('response_date', $formattedDate)
    //         ->first();

    //     if (!$aiResponse) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'AI response not found',
    //             'is_favourite' => 0
    //         ], 404);
    //     }

    //     // Update is_favourite to 0
    //     $aiResponse->is_favourite = 0;
    //     $aiResponse->save();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Favourite removed successfully',
    //         'user_id' => $request->user_id,
    //         'type' => $request->type,
    //         'is_favourite' => 0
    //     ]);
    // }




// Helper function to filter meals by type from response_json
// function filterMealByType($jsonData, string $mealType): array
// {
//     if (is_string($jsonData)) {
//         $data = json_decode($jsonData, true);
//     } else {
//         $data = $jsonData;
//     }

//     if (!$data || !isset($data['data']['diet_plan'])) {
//         return [
//             'status' => 'error',
//             'message' => 'Invalid JSON structure'
//         ];
//     }

//     $mealType = ucfirst(strtolower($mealType));
//     $filteredMeals = [];

//     foreach ($data['data']['diet_plan'] as $dayPlan) {

//         if (!isset($dayPlan['meals'][$mealType])) {
//             continue;
//         }

//         $mealDetails = $dayPlan['meals'][$mealType];

//         $filteredMeals[] = [
//             'day' => $dayPlan['day'] ?? null,
//             'meal_type' => $mealType,
//             'recipe' => $mealDetails
//         ];
//     }

//     if (empty($filteredMeals)) {
//         return [
//             'status' => 'error',
//             'message' => 'Meal type not found'
//         ];
//     }

//     return [
//         'status' => 'success',
//         'data' => $filteredMeals
//     ];
// }

}