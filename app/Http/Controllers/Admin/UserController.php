<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLevel;
use App\Models\City;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\DietType;
use App\Models\FoodItem;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use App\Models\MedicalIssue;
use App\Models\State;
use App\Models\User;
use App\Models\UserActivityLevel;
use App\Models\UserMealPlan;
use App\Models\UserMedicalIssue;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use stdClass;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Users';
        if ($request->ajax()) {
            $data = User::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0 text-end">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Generate payment link">
                    <a href="javascript:void(0)" class="edit avtar avtar-xs btn-link-success btn-pc-default" onclick="membershipsModal('.$row->id.')"><i class="ti ti-link f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.users.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit Diet Plan">
                    <a href="'.route('admin.users.diet-plan', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-calendar f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit user membership">
                    <a href="'.route('admin.memberships.user-membership', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-package f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
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
                ->addColumn('membership', function ($row) {
                    return isset($row->user_membership) ? $row->user_membership->membership->title : '';
                })
                ->filterColumn('membership', function ($query, $keyword) {
                    $query->whereHas('user_membership.membership', function ($query) use ($keyword) {
                        $query->where('title', 'like', "%$keyword%");
                    });
                })
                ->editColumn('created_at', function ($row) {
                    return date('Y-m-d H:i:s A', strtotime($row->created_at));
                })
                ->rawColumns(['action', 'active', 'membership', 'created_at'])
                ->make(true);
        }

        return view('admin.users.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create User';
        $activityLevels = ActivityLevel::where('active', 1)->get();
        $medicalIssues = MedicalIssue::where('active', 1)->get();
        $dietTypes = DietType::where('active', 1)->get();
        $countries = Country::get();
        $states = new stdClass;
        $cities = new stdClass;
        $mealPlans = MealPlan::get();
        $userMealPlanId = 0;

        return view('admin.users.user', compact('action', 'title', 'countries', 'states', 'cities', 'activityLevels', 'medicalIssues', 'dietTypes', 'mealPlans', 'userMealPlanId'));
    }

    private function _form_validation($request)
    {
        $rules = [
            'username' => 'required|string|max:50|unique:users,username,'.($request->id ?? 'null'),
            'full_name' => 'required',
            'email' => 'required|string|max:50|unique:users,email,'.($request->id ?? 'null'),
            'country_id' => 'nullable',
            'state_id' => 'nullable',
            'city_id' => 'nullable',
            'meal_plan' => 'required',
        ];
        $messages = [
            'username.required' => 'Name is required',
            'full_name.required' => 'Full Name is required',
            'email.required' => 'Full Name is required',
        ];
        $this->validate($request, $rules, $messages);

        $postData = [
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone ? $request->phone : '',
            'age' => $request->age ? $request->age : 0,
            'gender' => $request->gender ? $request->gender : 'M',
            'height' => $request->height ? $request->height : 0,
            'weight' => $request->weight ? $request->weight : 0,
            'active' => isset($request->active) ? 1 : 0,
            'country_id' => isset($request->country_id) ? $request->country_id : 0,
            'state_id' => isset($request->state_id) ? $request->state_id : 0,
            'city_id' => isset($request->city_id) ? $request->city_id : 0,
        ];

        return $postData;
    }

    public function store(Request $request)
    {
        $data = $this->_form_validation($request);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['password'] = Hash::make('12345678');
        $id = User::insertGetId($data);

        $user_meal_plan = new UserMealPlan;
        $user_meal_plan->user_id = $id;
        $user_meal_plan->meal_plan_id = $request->meal_plan;
        $user_meal_plan->save();

        if ($request->hasFile('image')) {
            $path = 'public/users/'.$id;
            $file = $request->file('image');
            $imagename = time().'_'.$file->getClientOriginalName();
            $filenametostore = $request->file('image')->storeAs($path, $imagename);
            // Storage::setVisibility($filenametostore, 'public');
            // $postData['image'] = $imagename;
            User::where('id', $id)->update(['image' => $imagename]);
        }

        if (isset($request->actiivity_level)) {
            $this->updateActivityLevel($id, $request->actiivity_level);
        }

        if (isset($request->medical_issues)) {
            $this->updateMedicalIssues($id, $request->medical_issues);
        }

        if (isset($request->preferences)) {
            $this->updatePreferences($id, $request->preferences);
        }

        return redirect()->route('admin.users')->with('success', 'User saved successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit User';
        $countries = Country::limit(5)->get();
        $states = new stdClass;
        $cities = new stdClass;
        $record = User::with(['activity_levels', 'medical_issues'])->find($id);
        if ($record->country_id) {
            $countries = Country::where('id', $record->country_id)->get();
            $states = State::where('id', $record->state_id)->get();
            $cities = City::where('id', $record->city_id)->get();
        }
        $activityLevels = ActivityLevel::where('active', 1)->get();
        $medicalIssues = MedicalIssue::where('active', 1)->get();
        $dietTypes = DietType::where('active', 1)->get();
        $mealPlans = MealPlan::get();
        $userMealPlan = UserMealPlan::where('user_id', $record->id)->orderBy('id', 'desc')->first();

        $userMealPlanId = null;
        if ($userMealPlan) {
            $userMealPlanId = $userMealPlan->meal_plan_id;
        }

        return view('admin.users.user', compact('action', 'title', 'record', 'countries', 'states', 'cities', 'activityLevels', 'medicalIssues', 'dietTypes', 'mealPlans', 'userMealPlanId'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        User::where('id', $id)->update($data);
        if ($request->hasFile('image')) {
            $path = 'public/users/'.$id;
            $file = $request->file('image');
            $imagename = time().'_'.$file->getClientOriginalName();
            $filenametostore = $request->file('image')->storeAs($path, $imagename);
            // Storage::setVisibility($filenametostore, 'public');
            // $postData['image'] = $imagename;
            User::where('id', $id)->update(['image' => $imagename]);
        }
        if (isset($request->actiivity_level)) {
            $this->updateActivityLevel($id, $request->actiivity_level);
        }

        if (isset($request->medical_issues)) {
            $this->updateMedicalIssues($id, $request->medical_issues);
        }

        if (isset($request->preferences)) {
            $this->updatePreferences($id, $request->preferences);
        }

        $user_meal_plan = UserMealPlan::where('user_id', $id)->latest()->first();
        $user_meal_plan->meal_plan_id = $request->meal_plan;
        $user_meal_plan->save();

        return redirect()->route('admin.users')->with('success', 'Changes saved successfully.');
    }

    public function updateActivityLevel($id, $actiivity_level = [])
    {
        UserActivityLevel::where('user_id', $id)->delete();
        foreach ($actiivity_level as $activity) {
            $activityLevel = new UserActivityLevel;
            $activityLevel->user_id = $id;
            $activityLevel->activity_level_id = $activity;
            $activityLevel->save();
        }

        return true;
    }

    public function updateMedicalIssues($id, $issues = [])
    {
        UserMedicalIssue::where('user_id', $id)->delete();
        foreach ($issues as $issue) {
            $medicalIssue = new UserMedicalIssue;
            $medicalIssue->user_id = $id;
            $medicalIssue->medical_issue_id = $issue;
            $medicalIssue->save();
        }

        return true;
    }

    public function updatePreferences($id, $preferences = [])
    {
        UserPreference::where('user_id', $id)->delete();
        foreach ($preferences as $preference) {
            $userPreference = new UserPreference;
            $userPreference->user_id = $id;
            $userPreference->diet_type_id = $preference;
            $userPreference->save();
        }

        return true;
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        User::whereIn('id', $ids)->delete();
        if (count($ids) > 1) {
            $msg = 'Users deleted successfully';
        } else {
            $msg = 'User Level deleted successfully';
        }
        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        User::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }

    public function diet_plan(Request $request)
    {
        $title = 'Diet Plan';
        $user_id = $request->id;
        $full_name = User::where('id', $user_id)->value('full_name');

        // Fetch the latest meal plan for the user
        $mealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();
        $mealPlanId = null;

        if ($mealPlan) {
            $mealPlanId = $mealPlan->id;
        }

        return view('admin.users.diet-plan', compact('title', 'user_id', 'full_name', 'mealPlanId'));
    }

    public function fetch_meal(Request $request)
    {
        $day = $request->day;
        $user_id = $request->user_id;
        $detail = [];

        // Fetch the latest meal plan for the user
        $mealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();
        $mealPlanId = null;

        if ($mealPlan != null) {
            $mealPlanId = $mealPlan->meal_plan_id;
        }

        $data = [
            'meals_html' => '',
            'total_calories' => 0,
            'total_carbs' => 0,
            'total_fibers' => 0,
            'total_fats' => 0,
            'total_protein' => 0,
        ];

        if ($mealPlan) {
            $meal_plan_items = MealPlanItem::where('meal_plan_id', $mealPlan->meal_plan_id)
                ->join('meals', 'meals.id', '=', 'meal_plan_items.meal_id')
                ->where('day', $day)
                ->with('foodItem:id,name,type,image,serving_size,serving_unit,calories,total_carbohydrates,dietary_fiber,total_fat,protein')
                ->orderBy('meals.order', 'asc')
                ->get(['meal_plan_items.id', 'food_item_id', 'quantity', 'meal_id']);

            foreach ($meal_plan_items as $item) {
                $foodItem = $item->foodItem;

                $r_calories = round($foodItem->calories / $foodItem->serving_size * $item->quantity);
                $r_carbs = round($foodItem->total_carbohydrates / $foodItem->serving_size * $item->quantity);
                $r_fibers = round($foodItem->dietary_fiber / $foodItem->serving_size * $item->quantity);
                $r_fats = round($foodItem->total_fat / $foodItem->serving_size * $item->quantity);
                $r_protein = round($foodItem->protein / $foodItem->serving_size * $item->quantity);

                $detail[$item->meal_id]['data'][] = [
                    'meal_plan_item_id' => $item->id ? $item->id : 0,
                    'id' => $foodItem->id ? $foodItem->id : '',
                    'name' => $foodItem->name,
                    'type' => $foodItem->type,
                    'serving_size' => $foodItem->serving_size,
                    'serving_unit' => $foodItem->serving_unit,
                    'image' => $foodItem->image,
                    'calories' => $r_calories,
                    'carbs' => $r_carbs,
                    'fibers' => $r_fibers,
                    'fats' => $r_fats,
                    'protein' => $r_protein,
                    'qty' => (int) $item->quantity,
                ];

                $data['total_calories'] += $r_calories;
                $data['total_carbs'] += $r_carbs;
                $data['total_fibers'] += $r_fibers;
                $data['total_fats'] += $r_fats;
                $data['total_protein'] += $r_protein;
            }
        }

        $meals = Meal::select('id', 'name')->get()->toArray();
        $arr = [];

        foreach ($meals as $meal) {
            if (array_key_exists($meal['id'], $detail)) {
                $totalCalories = array_sum(array_column($detail[$meal['id']]['data'], 'calories'));

                $arr[] = [
                    'id' => $meal['id'],
                    'name' => $meal['name'],
                    'total_calories' => $totalCalories,
                    'data' => $detail[$meal['id']]['data'],
                ];
            } else {
                $totalCalories = 0;

                $arr[] = [
                    'id' => $meal['id'],
                    'name' => $meal['name'],
                    'total_calories' => $totalCalories,
                    'data' => [],
                ];
            }
        }

        $detail = $arr;
        $meals_html = '';

        if (count($detail) > 0) {
            foreach ($detail as $dt) {
                $mealId = $dt['id'];
                $x = $dt['data'];
                $item_html = '';

                if (count($x) > 0) {
                    foreach ($x as $item) {
                        $icon = '';

                        if ($item['type'] == 'VG') {
                            $icon = asset('admin/images/vg.png');
                        } elseif ($item['type'] == 'NV') {
                            $icon = asset('admin/images/nv.png');
                        }

                        $img = asset('admin/images/noimg.jpg');
                        $removeBtn = asset('admin/images/close.png');
                        $itemId = $item['id'];
                        $meal_plan_item_id = $item['meal_plan_item_id'];

                        if ($item['image']) {
                            if (Storage::exists('public/food-items/'.$item['image'])) {
                                $img = asset(Storage::url('public/food-items/'.$item['image']));
                            } else {
                                $img = asset('admin/images/noimg.jpg');
                            }
                        } else {
                            $img = asset('admin/images/noimg.jpg');
                        }

                        $s_size = round($item['serving_size']);

                        $protein = number_format($item['protein'], 1);
                        $carbs = number_format($item['carbs'], 1);
                        $fat = number_format($item['fats'], 1);

                        $item_html .= "
                            <div id=\"it{$itemId}\" class=\"row grab mb-3 mt-1\" draggable=\"true\" ondragstart=\"drag(event, {$mealId}, {$itemId}, {$mealPlanId})\">
                                <div class=\"d-flex justify-content-between\">
                                    <div>
                                        <div class=\"d-flex justify-content-between\">
                                            <div>
                                                <img src=\"{$img}\" class=\"food-item-img\">
                                            </div>
                                            <div class=\"ps-2 text-start\">
                                                <div>
                                                    <span class=\"fw-medium text-start\">{$item['name']} ({$item['qty']})</span>
                                                    <img src=\"{$icon}\">
                                                </div>
                                                {$s_size} | {$item['serving_unit']}
                                                <div class=\"d-flex justify-content-start\">
                                                    <span class=\"macro-span bg-green-100 modal-carb rounded\">P {$protein}g</span>&nbsp;&nbsp;&nbsp;
                                                    <span class=\"macro-span bg-orange-100 modal-carb rounded\">C {$carbs}g</span>&nbsp;&nbsp;&nbsp;
                                                    <span class=\"macro-span bg-blue-100 modal-carb rounded\">F {$fat}g</span>&nbsp;&nbsp;&nbsp;
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            <img class=\"removeItemBtn pe-2\" src=\"{$removeBtn}\" onclick=\"setRemoveItem({$mealPlanId}, {$day}, {$itemId},{$meal_plan_item_id})\" data-bs-toggle=\"modal\" data-bs-target=\"#removeModal\">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ";
                    }
                }

                $meals_html .= "
                    <div class=\"col-md-12 col-xl-4 pt-0\">
                        <div class=\"meals-div-container rounded\">
                            <div class=\"d-flex meal-heading-outer justify-content-between align-items-center border-bottom\">
                                <div>
                                    <strong class=\"f-16 align-content-center px-3 py-1 fw-medium\">{$dt['name']} <span class=\"text-grey-300\">({$dt['total_calories']} Kcals)</span></strong>
                                </div>
                                <div role=\"button\" class=\"plus-item pe-3\" data-bs-toggle=\"modal\" data-bs-target=\"#addModal\" title=\"Add Item\" onclick=\"setMealAndMealPlanId({$mealId}, {$mealPlanId})\">+</div>
                            </div>
                            <div class=\"p-1 pt-0\">
                                <div id=\"{$mealId}\" class=\"meals-div px-1 dropzone\" ondrop=\"drop(event, {$mealId})\" ondragover=\"allowDrop(event)\">
                                    {$item_html}
                                </div>
                            </div>
                        </div>
                    </div>
                ";
            }

            $data['meals_html'] = $meals_html;
        } else {
            $meals = Meal::select('id', 'name')->get()->toArray();
            $meals_html = '';
            $mealPlan = UserMealPlan::where('user_id', $user_id)->orderBy('id', 'desc')->first();
            $mealPlanId = $mealPlan->meal_plan_id;

            foreach ($meals as $meal) {
                $mealId = $meal['id'];
                $mealName = $meal['name'];

                $meals_html .= "
                    <div class=\"col-md-12 col-xl-4 pt-0\">
                        <div class=\"meals-div-container rounded\">
                            <div class=\"p-3 pt-0\">
                                <div class=\"d-flex justify-content-between align-items-center border-bottom\">
                                    <div>
                                        <strong class=\"f-16 bg-blue-500 align-content-center px-3 py-1 text-white fw-medium\">{$mealName}</strong>
                                    </div>
                                    <div role=\"button\" class=\"plus-item\" data-bs-toggle=\"modal\" data-bs-target=\"#addModal\" title=\"Add Item\" onclick=\"setMealAndMealPlanId({$mealId}, $mealPlanId)\">+</div>
                                </div>
                                <div id=\"{$mealId}\" class=\"meals-div px-0 dropzone\" ondrop=\"drop(event, {$mealId})\" ondragover=\"allowDrop(event)\">

                                </div>
                            </div>
                        </div>
                    </div>
                ";
            }

            $data['meals_html'] = $meals_html;

            $data['total_calories'] = 0;
            $data['total_carbs'] = 0;
            $data['total_fibers'] = 0;
            $data['total_fats'] = 0;
            $data['total_protein'] = 0;
        }

        echo json_encode($data);

        exit;
    }

    public function remove_food_item_from_meal_plan(Request $request)
    {
        $status = MealPlanItem::where('id', $request->meal_plan_item_id)
            // ->where('meal_plan_id', $request->meal)
            // ->where('day', $request->day)
            // ->where('food_item_id', $request->item)
            ->delete();

        return $status;
    }

    public function drag_food_item(Request $request)
    {
        // Get quantity and remove item source meal.
        $r = MealPlanItem::where('day', $request->day)
            ->where('meal_plan_id', $request->dragMealPlanId)
            ->where('food_item_id', $request->dragItemId)
            ->where('meal_id', $request->dragMealId)
            ->first();

        $qty = $r->quantity;
        $r->delete();

        // It item already exist in target meal them remove it first.
        $r = MealPlanItem::where('day', $request->day)
            ->where('meal_plan_id', $request->dragMealPlanId)
            ->where('food_item_id', $request->dragItemId)
            ->where('meal_id', $request->dropMealId)
            ->first();

        if ($r != null) {
            $r->delete();
        }

        // Add item to target meal.
        $r = new MealPlanItem;
        $r->day = $request->day;
        $r->meal_plan_id = $request->dragMealPlanId;
        $r->food_item_id = $request->dragItemId;
        $r->meal_id = $request->dropMealId;
        $r->quantity = $qty;
        $r->save();

        echo 'done';
        exit;
    }

    public function fetch_items(Request $request)
    {
        $key = '%'.$request->key.'%';

        $items = FoodItem::select('id', 'name', 'serving_size', 'serving_unit', 'image', 'total_carbohydrates', 'dietary_fiber', 'total_fat', 'protein')
            ->where('name', 'like', $key)
            ->get();

        $html = '<table class="table">';

        foreach ($items as $item) {
            $protein = round($item['protein']);
            $carbs = round($item['total_carbohydrates']);
            $fat = round($item['total_fat']);

            $serving_size = round($item['serving_size']);

            $img = asset('admin/images/noimg.jpg');

            if ($item['image']) {
                if (Storage::exists('public/food-items/'.$item['image'])) {
                    $img = asset(Storage::url('public/food-items/'.$item['image']));
                }
            }

            $html .= "<tr>
                        <td class=\"d-flex justify-content-between align-items-center cursor-pointer border-0 food-item-td\">
                            <div>
                                <div class=\"d-flex justify-content-between\">
                                    <div>
                                        <img src=\"{$img}\" class=\"img-fluid food-item-img\">
                                    </div>
                                    <div class=\"ps-2\">
                                        <span class=\"fw-medium\">{$item['name']}</span>
                                        <br>
                                        {$serving_size}{$item['serving_unit']}
                                        <div class=\"d-flex justify-content-start\">
                                            <span class=\"bg-gray-300 rounded modal-carb\">P {$protein}g</span>&nbsp;&nbsp;&nbsp;
                                            <span class=\"bg-gray-300 rounded modal-carb\">C {$carbs}g</span>&nbsp;&nbsp;&nbsp;
                                            <span class=\"bg-gray-300 rounded modal-carb\">F {$fat}g</span>&nbsp;&nbsp;&nbsp;
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type=\"number\" name=\"\" min=\"1\" class=\"form-control qty-input\" value=\"1\">
                        </td>
                        <td>
                            <button class=\"btn btn-primary btn-sm px-2 rounded-1 modal-item-add-btn\" onclick=\"addItem(this, {$item['id']})\">Add</button>
                        </td>
                    </tr>";
        }

        $html .= '</table>';

        echo $html;
        exit;
    }

    public function fetch_items_randomly(Request $request)
    {
        $items = FoodItem::select('id', 'name', 'serving_size', 'serving_unit', 'image', 'total_carbohydrates', 'dietary_fiber', 'total_fat', 'protein')
            ->latest()
            ->limit(6)
            ->get();

        $html = '<table class="table">';

        foreach ($items as $item) {
            $protein = round($item['protein']);
            $carbs = round($item['total_carbohydrates']);
            $fat = round($item['total_fat']);

            $serving_size = round($item['serving_size']);

            $img = asset('admin/images/noimg.jpg');

            if ($item['image']) {
                if (Storage::exists('public/food-items/'.$item['image'])) {
                    $img = asset(Storage::url('public/food-items/'.$item['image']));
                }
            }

            $html .= "<tr>
                        <td class=\"d-flex justify-content-between align-items-center cursor-pointer border-0 food-item-td\">
                            <div>
                                <div class=\"d-flex justify-content-between\">
                                    <div>
                                        <img src=\"{$img}\" class=\"img-fluid food-item-img\">
                                    </div>
                                    <div class=\"ps-2\">
                                        <span class=\"fw-medium\">{$item['name']}</span>
                                        <br>
                                        {$serving_size}{$item['serving_unit']}
                                        <div class=\"d-flex justify-content-start\">
                                            <span class=\"bg-gray-300 rounded modal-carb\">P {$protein}g</span>&nbsp;&nbsp;&nbsp;
                                            <span class=\"bg-gray-300 rounded modal-carb\">C {$carbs}g</span>&nbsp;&nbsp;&nbsp;
                                            <span class=\"bg-gray-300 rounded modal-carb\">F {$fat}g</span>&nbsp;&nbsp;&nbsp;
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type=\"number\" name=\"\" min=\"1\" class=\"form-control qty-input\" value=\"1\">
                        </td>
                        <td>
                            <button class=\"btn btn-primary btn-sm px-2 rounded-1 modal-item-add-btn\" onclick=\"addItem(this, {$item['id']})\">Add</button>
                        </td>
                    </tr>";
        }

        $html .= '</table>';

        echo $html;
        exit;
    }

    public function add_items_to_meal(Request $request)
    {
        $r = MealPlanItem::where('day', $request->day)
            ->where('meal_plan_id', $request->mealPlanId)
            ->where('food_item_id', $request->itemId)
            ->where('meal_id', $request->mealId)
            ->first();

        if ($r != null) {
            $r->delete();
        }

        $r = new MealPlanItem;
        $r->meal_plan_id = $request->mealPlanId;
        $r->food_item_id = $request->itemId;
        $r->quantity = $request->qty;
        $r->meal_id = $request->mealId;
        $r->day = $request->day;
        // $r->user_id = $request->userId;
        $r->save();

        echo 'done';
        exit;
    }

    public function generate_payment_link(Request $request)
    {
        $coupon = $request->coupon;
        $amount = $request->amount;
        $coupon_id = null;
        $discount = 0;

        if ($coupon) {
            $coupon = $this->fetch_coupon($coupon, $request->amount);

            if ($coupon) {
                $amount = $coupon['amount'];
                $coupon_id = $coupon['coupon_id'];
                $discount = $coupon['discount'];
            }
        }

        $encrypted_user_id = Crypt::encryptString($request->user_id);
        $encrypted_amount = Crypt::encryptString($amount);
        $encrypted_plan_id = Crypt::encryptString($request->plan_id);
        $encrypted_discount = Crypt::encryptString($discount);

        if ($coupon_id != null) {
            $encrypted_coupon_id = Crypt::encryptString($coupon_id);

            return URL::signedRoute('process-payment-link', [
                'user' => $encrypted_user_id,
                'amount' => $encrypted_amount,
                'plan_id' => $encrypted_plan_id,
                'coupon_id' => $encrypted_coupon_id,
                'discount' => $encrypted_discount,
            ]);
        } else {
            return URL::signedRoute('process-payment-link', [
                'user' => $encrypted_user_id,
                'amount' => $encrypted_amount,
                'plan_id' => $encrypted_plan_id,
                'discount' => $encrypted_discount,
            ]);
        }
    }

    public function fetch_coupon($coupon, $amount)
    {
        $dt = date('Y-m-d');
        $coupon = Coupon::where('code', $coupon)
            ->where('active', 1)
            ->where('start_date', '<=', $dt)
            ->where('end_date', '>=', $dt)
            ->first();

        if ($coupon == null) {
            return null;
        } else {
            $discount_amount = 0;

            if ($coupon->discount_type == 'flat') {
                $discount_amount = $coupon->amount;
            } elseif ($coupon->discount_type == 'percent') {
                $discount_amount = ($amount / 100) * $coupon->amount;
            }

            return [
                'coupon_id' => $coupon->id,
                'discount' => $discount_amount,
                'amount' => $amount - $discount_amount,
            ];
        }
    }
}
