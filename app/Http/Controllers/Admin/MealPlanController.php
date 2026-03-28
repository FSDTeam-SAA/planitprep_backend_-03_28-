<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class MealPlanController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Meal Plans';

        if ($request->ajax()) {
            $data = MealPlan::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0 text-end">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.meal-plans.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Meals">
                    <a href="'.route('admin.meal-plans.meals', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-file f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Delete">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
                })
                ->editColumn('description', function ($row) {
                    return Str::limit($row->description, 150);
                })
                ->addColumn('active', function ($row) {
                    $checked = '';
                    if ($row->active == 1) {
                        $checked = 'checked';
                    }

                    return '<div class="form-check form-switch custom-switch-v1 mb-2">
                        <input type="checkbox" class="form-check-input input-success active" data-name="'.$row->title.'" data-value="'.$row->active.'" data-id="'.$row->id.'" '.$checked.'>
                    </div>';
                })
                ->editColumn('created_at', function ($row) {
                    return date('Y-m-d H:i:s A', strtotime($row->created_at));
                })
                ->rawColumns(['description', 'action', 'active', 'created_at'])
                ->make(true);
        }

        return view('admin.meal-plans.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create Meal Plan';

        return view('admin.meal-plans.meal-plan', compact('action', 'title'));
    }

    private function _form_validation($request)
    {
        $rules = [
            'name' => 'required|string|max:50|unique:meal_plans,name,'.($request->id ?? 'null'),
            'description' => 'nullable',
        ];
        $messages = [
            'name.required' => 'Name is required',
        ];
        $this->validate($request, $rules, $messages);

        $postData = [
            'name' => $request->name,
            'description' => $request->description,
            'active' => isset($request->active) ? 1 : 0,
        ];

        return $postData;
    }

    public function store(Request $request)
    {
        $data = $this->_form_validation($request);
        $data['created_at'] = date('Y-m-d H:i:s');

        MealPlan::insert($data);

        return redirect()->route('admin.meal-plans')->with('success', 'Meal plan saved successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Meal Plan';
        $record = MealPlan::find($id);

        return view('admin.meal-plans.meal-plan', compact('action', 'title', 'record'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        MealPlan::where('id', $id)->update($data);

        return redirect()->route('admin.meal-plans')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        MealPlan::whereIn('id', $ids)->delete();

        if (count($ids) > 1) {
            $msg = 'Meal plans deleted successfully';
        } else {
            $msg = 'Meal plan deleted successfully';
        }

        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        MealPlan::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }

    // Edit meals in a meal plan.
    public function meals($id)
    {
        $title = 'Meals';
        $meals = Meal::get();
        $mealIds = Meal::pluck('id');
        $mealPlanId = $id;
        $foodItems = [];

        return view('admin.meal-plans.meals', compact('title', 'meals', 'foodItems', 'mealPlanId', 'mealIds'));
    }

    public function updateFoodItems(Request $request)
    {
        $mealPlanId = $request->mealPlanId;
        $mealId = $request->mealId;
        $day = $request->day;
        $foodItemId = $request->foodItemId;
        $foodItemQty = $request->foodItemQty;

        if ($request->action == 'add') {
            $r = new MealPlanItem;
            $r->meal_plan_id = $mealPlanId;
            $r->meal_id = $mealId;
            $r->day = $day;
            $r->food_item_id = $foodItemId;
            $r->quantity = $foodItemQty;

            $r->save();
        } elseif ($request->action == 'remove') {
            MealPlanItem::where('meal_plan_id', $mealPlanId)
                ->where('meal_id', $mealId)
                ->where('day', $day)
                ->where('food_item_id', $foodItemId)
                ->delete();
        }

        exit;
    }

    public function updateFoodItemQty(Request $request)
    {
        $r = MealPlanItem::where('meal_plan_id', $request->mealPlanId)
            ->where('meal_id', $request->mealId)
            ->where('day', $request->day)
            ->where('food_item_id', $request->foodItemId)
            ->first();

        $r->quantity = $request->qty;
        $r->save();

        exit;
    }

    public function remove_item_from_meal_plan(Request $request)
    {
        MealPlanItem::where('meal_id', $request->mealId)
            ->where('meal_plan_id', $request->mealPlan)
            ->where('day', $request->day)
            ->where('food_item_id', $request->item)
            ->delete();

        echo 'done';
        exit;
    }

    public function fetch_meals(Request $request)
    {
        $meals = Meal::get();
        $mealIds = Meal::pluck('id');
        $mealPlanId = $request->mealPlanId;
        $foodItems = [];
        $removeBtn = asset('admin/images/close.png');
        $html = '';

        for ($i = 1; $i <= 7; $i++) {
            foreach ($meals as $meal) {
                $meal_plan_items = MealPlanItem::where('meal_plan_id', $mealPlanId)
                    ->where('meal_id', $meal->id)
                    ->where('day', $i)
                    ->select(['food_item_id', 'quantity'])
                    ->get()
                    ->toArray();
                $food_item_quantities = [];

                foreach ($meal_plan_items as $item) {
                    $food_item_quantities[$item['food_item_id']] = $item['quantity'];
                }

                $food_item_ids = array_keys($food_item_quantities);
                $b = FoodItem::whereIn('id', $food_item_ids)->pluck('id')->toArray();
                $arr1 = FoodItem::whereIn('id', $b)->get();

                foreach ($arr1 as $x) {
                    $x['quantity'] = $food_item_quantities[$x['id']];

                    $img = asset('admin/images/noimg.jpg');

                    if ($x['image']) {
                        if (Storage::exists('public/food-items/'.$x['image'])) {
                            $img = asset(Storage::url('public/food-items/'.$x['image']));
                        }
                    }

                    $x['image'] = $img;
                }

                $foodItems[$i][$meal->id] = $arr1;
            }
        }

        $html = view('admin.meal-plans.render', compact('meals', 'mealPlanId', 'foodItems'))->render();

        $data = [
            'html' => $html,
            'total_calories' => 0,
            'total_carbs' => 0,
            'total_fibers' => 0,
            'total_fats' => 0,
            'total_protein' => 0,
        ];

        $meal_plan_items = MealPlanItem::where('meal_plan_id', $request->mealPlanId)
            ->join('meals', 'meals.id', '=', 'meal_plan_items.meal_id')
            ->where('day', $request->day)
            ->with('foodItem:id,name,type,image,serving_size,serving_unit,calories,total_carbohydrates,dietary_fiber,total_fat,protein')
            ->orderBy('meals.order', 'asc')
            ->get(['food_item_id', 'quantity', 'meal_id']);

        $total_calories = 0;
        $total_carbs = 0;
        $total_fibers = 0;
        $total_fats = 0;
        $total_protein = 0;

        foreach ($meal_plan_items as $item) {
            $foodItem = $item->foodItem;

            $the_qty = (int) $item->quantity;

            $total_calories += $foodItem->calories / $foodItem->serving_size * $the_qty;
            $total_carbs += $foodItem->total_carbohydrates / $foodItem->serving_size * $the_qty;
            $total_fibers += $foodItem->dietary_fiber / $foodItem->serving_size * $the_qty;
            $total_fats += $foodItem->total_fat / $foodItem->serving_size * $the_qty;
            $total_protein += $foodItem->protein / $foodItem->serving_size * $the_qty;
        }

        $data['total_calories'] = number_format($total_calories);
        $data['total_carbs'] = number_format($total_carbs);
        $data['total_fibers'] = number_format($total_fibers);
        $data['total_fats'] = number_format($total_fats);
        $data['total_protein'] = number_format($total_protein);

        echo json_encode($data);
        exit;
    }
}
