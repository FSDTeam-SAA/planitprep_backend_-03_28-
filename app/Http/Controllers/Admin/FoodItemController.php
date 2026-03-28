<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodGroup;
use App\Models\FoodItem;
use App\Models\ServingUnit;
use App\Models\SimilarFoodItem;
use Google\Auth\Cache\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FoodItemController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Food Items';
        if ($request->ajax()) {
            $data = FoodItem::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.food-items.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
                })
                ->editColumn('image', function ($row) {
                    if ($row->image != '') {
                        return "<img class='thumb-1' src='".asset(\Storage::url('food-items')).'/'.$row->image."' width=70>";
                    }

                    return '';
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
                ->rawColumns(['action', 'active', 'image', 'created_at'])
                ->make(true);
        }

        return view('admin.food-items.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create Food Item';
        $food_groups = FoodGroup::where('active', 1)->get();
        $similar_food_items = new \stdClass;
        $serving_units = ServingUnit::where('active', 1)->get();

        return view('admin.food-items.food-item', compact('action', 'title', 'food_groups', 'similar_food_items', 'serving_units'));
    }

    private function _form_validation($request)
    {
        $rules = [
            'name' => 'required|string|max:50|unique:food_items,name,'.($request->id ?? 'null'),
            'serving_size' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
        ];
        $messages = [
            'name.required' => 'Name is required',
        ];
        $this->validate($request, $rules, $messages);

        $postData = [
            'name' => $request->name,
            'food_group_id' => $request->food_group_id ? $request->food_group_id : 0,
            'serving_size' => $request->serving_size ? $request->serving_size : 0,
            'serving_unit' => $request->serving_unit ? $request->serving_unit : '',
            'calories' => $request->calories ? $request->calories : null,
            'total_fat' => $request->total_fat ? $request->total_fat : null,
            'saturated_fat' => $request->saturated_fat ? $request->saturated_fat : null,
            'trans_fat' => $request->trans_fat ? $request->trans_fat : null,
            'monounsaturated_fat' => $request->monounsaturated_fat ? $request->monounsaturated_fat : null,
            'polyunsaturated_fat' => $request->polyunsaturated_fat ? $request->polyunsaturated_fat : null,
            'cholesterol' => $request->cholesterol ? $request->cholesterol : null,
            'sodium' => $request->sodium ? $request->sodium : null,
            'total_carbohydrates' => $request->total_carbohydrates ? $request->total_carbohydrates : 0,
            'dietary_fiber' => $request->dietary_fiber ? $request->dietary_fiber : null,
            'sugars' => $request->sugars ? $request->sugars : null,
            'added_sugars' => $request->added_sugars ? $request->added_sugars : null,
            'protein' => $request->protein ? $request->protein : 0,
            'vitamin_a' => $request->vitamin_a ? $request->vitamin_a : null,
            'vitamin_c' => $request->vitamin_c ? $request->vitamin_c : null,
            'calcium' => $request->calcium ? $request->calcium : null,
            'iron' => $request->iron ? $request->iron : null,
            'potassium' => $request->potassium ? $request->potassium : null,
            'vitamin_d' => $request->vitamin_d ? $request->vitamin_d : null,
            'vitamin_b6' => $request->vitamin_b6 ? $request->vitamin_b6 : null,
            'vitamin_b12' => $request->vitamin_b12 ? $request->vitamin_b12 : null,
            'magnesium' => $request->magnesium ? $request->magnesium : null,
            'glycemic_index' => $request->glycemic_index ? $request->glycemic_index : null,
            'comments' => $request->comments ? $request->comments : '',
            'active' => isset($request->active) ? 1 : 0,
        ];
        if ($request->hasFile('image')) {
            $path = 'public/food-items';
            $file = $request->file('image');
            $imagename = time().'_'.$file->getClientOriginalName();
            $filenametostore = $request->file('image')->storeAs($path, $imagename);
            // Storage::setVisibility($filenametostore, 'public');
            $postData['image'] = $imagename;
        }

        return $postData;
    }

    public function store(Request $request)
    {
        $data = $this->_form_validation($request);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = FoodItem::insertGetId($data);

        if (isset($request->similar_item_ids)) {
            foreach ($request->similar_item_ids as $key => $similar_item_id) {
                $simItem = FoodItem::find($similar_item_id);

                $simliarItem = new SimilarFoodItem;
                $simliarItem->food_item_id = $id;
                $simliarItem->similar_item_id = $similar_item_id;
                $simliarItem->quantity = $simItem->serving_size;
                $simliarItem->save();
            }
        }

        return redirect()->route('admin.food-items')->with('success', 'Food item saved successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Food Item';
        $record = FoodItem::with(['similar_food_items', 'serving_unit'])->find($id);
        $food_groups = FoodGroup::where('active', 1)->get();
        $similar_ids = [];
        if (isset($record->similar_food_items)) {
            $similar_ids = $record->similar_food_items->pluck('similar_item_id')->toArray();
        }
        // $similar_food_items = FoodItem::whereIn('id', $similar_ids)->limit(5)->get();
        $similar_food_items = $record->similar_food_items;
        $serving_units = ServingUnit::where('active', 1)->get();

        // $similar_ids[] = $id;
        // $similar_ids = implode(',', $similar_ids);
        return view('admin.food-items.food-item', compact('action', 'title', 'record', 'food_groups', 'similar_food_items', 'serving_units'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        FoodItem::where('id', $id)->update($data);
        SimilarFoodItem::where('food_item_id', $id)->delete();
        if (isset($request->similar_item_ids)) {
            foreach ($request->similar_item_ids as $similar_item_id) {
                $simliarItem = new SimilarFoodItem;
                $simliarItem->food_item_id = $id;
                $simliarItem->similar_item_id = $similar_item_id;
                $simliarItem->save();
            }
        }

        return redirect()->route('admin.food-items')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        FoodItem::whereIn('id', $ids)->delete();
        if (count($ids) > 1) {
            $msg = 'Food Items deleted successfully';
        } else {
            $msg = 'Food Item deleted successfully';
        }
        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        FoodItem::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }

    // Get items
    public function getSimilarItems(Request $request)
    {
        $search = $request->search;
        if (isset($request->selected_ids)) {
            $selected_ids = $request->selected_ids;
        } else {
            $selected_ids = SimilarFoodItem::where('food_item_id', $request->item_id)->get()->pluck('similar_item_id')->toArray();
        }
        if ($search == '') {
            $items = new FoodItem;
        } else {
            $items = FoodItem::where('name', 'like', '%'.$search.'%')->where('active', '1');

            if (count($selected_ids) > 0) {
                $items = $items->whereNotIn('id', $selected_ids);
            }
            $items = $items->limit(5)->get();
        }

        $html = view('admin.food-items.items', compact('items'))->render();

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function addSimilarItem(Request $request)
    {
        $foodItem = FoodItem::find($request->id);
        if (isset($foodItem) && $request->item_id > 0) {
            $similar = new SimilarFoodItem;
            $similar->food_item_id = $request->item_id;
            $similar->similar_item_id = $request->id;
            $similar->quantity = $foodItem->serving_size;
            $similar->save();

            $item = SimilarFoodItem::with('food_item')->where('food_item_id', $request->item_id)
                ->where('similar_item_id', $request->id)->first();
        }

        // $item = $item->similar_food_items;
        if ($request->item_id == 0) {
            $foodItem = FoodItem::find($request->id);
            $item = new \stdClass;
            $item->id = $request->id;
            $item->quantity = $foodItem->serving_size;
            $item->food_item = $foodItem;
        }

        $html = view('admin.food-items.selected-items', compact('item'))->render();

        return response()->json(['status' => true, 'html' => $html]);
    }

    public function removeSimilarItem(Request $request)
    {
        // $foodItem = FoodItem::find($request->item_id);

        if ($request->item_id > 0) {
            SimilarFoodItem::where('food_item_id', $request->item_id)
                ->where('similar_item_id', $request->id)->delete();
        }
        // $similarItems = SimilarFoodItem::where('food_item_id', $request->item_id)->get()->pluck('similar_item_id')->toArray();
        // $items = SimilarFoodItem::with('food_item')
        //     ->where('food_item_id', $request->item_id)
        //     // ->where('similar_item_id',$request->id)
        //     ->get();
        $html = '';

        // foreach ($items as $item) {
        //     $view =  view('admin.food-items.selected-items', compact('item'))->render();
        //     $html .= $view;
        // }
        return response()->json(['status' => true, 'html' => $html]);
    }

    public function updateSimilarItemQty(Request $request)
    {
        SimilarFoodItem::where('id', $request->id)->update(['quantity' => $request->qty]);
    }
}
