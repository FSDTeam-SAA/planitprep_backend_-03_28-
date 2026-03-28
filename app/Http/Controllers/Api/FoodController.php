<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Models\FoodItemLikeDislike;
use App\Models\FoodItemReplacement;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FoodController extends Controller
{
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

    public function likeDislike(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'food_item_id' => 'required',
                'type' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            }
            $user = Auth::user();
            $user_id = $user->id;
            $food_item_id = $request->food_item_id;
            $isRecord = FoodItemLikeDislike::where('food_item_id', $food_item_id)
                ->where('user_id', $user_id)
                ->first();

            if ($isRecord) {
                FoodItemLikeDislike::where('id', $isRecord->id)->delete();
            }

            $replace = ($request->replace) ? $request->replace : 0;

            $likeDislike = new FoodItemLikeDislike;
            $likeDislike->food_item_id = $food_item_id;
            $likeDislike->user_id = Auth::user()->id;
            $likeDislike->replace = $replace;
            $likeDislike->type = $request->type;
            $likeDislike->save();

            if ($replace == 1) {
                $itemReplace = new FoodItemReplacement;
                $itemReplace->food_item_id = $food_item_id;
                $itemReplace->user_id = Auth::user()->id;
                $itemReplace->save();

                try {
                    $foodItem = FoodItem::find($food_item_id);
                    $title = 'Food Item Replacement';
                    $description = $foodItem->name.' replacement with another food item';
                    $type = 'replace';
                    $param = ['id' => intval($user_id), 'type' => $type, 'food_item_id' => $food_item_id];
                    $small_file_path = '';
                    $user = User::find($user_id);
                    $user->notify(new UserNotification($title, $description, $small_file_path, $param));

                    $notify = new Notification;
                    $notify->notify_by = 0;
                    $notify->notify_to = $user_id;
                    $notify->title = $title;
                    $notify->message = $description;
                    $notify->type = $type;
                    $notify->save();

                    // sendNotification($title, $description, $param, $small_file_path);
                } catch (\Exception $e) {
                    dd($e);
                }
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        }
    }

    public function similarFoodItems(Request $request)
    {
        if (Auth::user()) {
            $validator = Validator::make($request->all(), [
                'food_item_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $this->errorStr($validator->errors()->all()),
                ]);
            }
            $foodItemId = $request->food_item_id;

            $targetItem = FoodItem::with('similar_food_items')->find($foodItemId);

            // Ensure the item exists
            if ($targetItem) {

                $url = asset(Storage::url('food-items'));
                // $foodItems = FoodItem::select(\DB::raw("*,concat('".$url."','/',image) as image"))->where("active", 1)
                //                 ->where('id', '!=', $foodItemId)
                //                 ->whereBetween('calories', [$targetItem->calories - 10, $targetItem->calories + 10]) //10
                //                 ->whereBetween('protein', [$targetItem->protein - 2, $targetItem->protein + 2]) //2
                //                 ->whereBetween('calcium', [$targetItem->total_carbohydrates - 2, $targetItem->total_carbohydrates + 2])
                //                 ->whereBetween('dietary_fiber', [$targetItem->dietary_fiber - 2, $targetItem->dietary_fiber + 2]);
                if (isset($targetItem->similar_food_items) && $targetItem->similar_food_items->count() > 0) {
                    $ids = $targetItem->similar_food_items->pluck('similar_item_id')->toArray();
                    $foodItems = FoodItem::select(\DB::raw("*,concat('".$url."','/',image) as image"))->where('active', 1)
                        ->where('id', '!=', $foodItemId)
                        ->whereIn('id', $ids);
                    if (isset($request->search) && $request->search != '') {
                        $foodItems = $foodItems->where(\DB::raw('LOWER(name)'), 'like', "%{$request->search}%");
                    }
                    $foodItems = $foodItems->limit(10)->get();

                    return response()->json(['status' => true, 'data' => $foodItems]);
                } else {
                    return response()->json(['status' => false, 'msg' => 'No Item Found!']);
                }
            } else {
                return response()->json(['status' => false, 'msg' => 'Invalid Id!']);
            }
        } else {
            return response()->json(['status' => false, 'msg' => 'Unauthorized User!']);
        }
    }
}
