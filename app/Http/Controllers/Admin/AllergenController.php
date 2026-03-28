<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class AllergenController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Allergens';
        if ($request->ajax()) {
            $data = Allergen::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.allergens.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
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
                ->editColumn('created_at', function ($row) {
                    return date('Y-m-d H:i:s A', strtotime($row->created_at));
                })
                ->rawColumns(['action', 'active', 'created_at'])
                ->make(true);
        }

        return view('admin.allergens.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create Allergen';

        return view('admin.allergens.allergen', compact('action', 'title'));
    }

    private function _form_validation($request)
    {
        $rules = [
            'name' => 'required|string|max:50|unique:allergens,name,'.($request->id ?? 'null'),
        ];
        $messages = [
            'name.required' => 'Name is required',
        ];
        $this->validate($request, $rules, $messages);

        $postData = [
            'name' => $request->name,
            'active' => isset($request->active) ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            $path = 'public/allergens';
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
        Allergen::insert($data);

        return redirect()->route('admin.allergens')->with('success', 'Allergen saved successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Allergen';
        $record = Allergen::find($id);

        if ($record->image != '') {
            if (! Storage::disk('public')->exists('allergens/'.$record->image)) {
                $record->image = '';
            }
        } else {
            $record->image = '';
        }

        return view('admin.allergens.allergen', compact('action', 'title', 'record'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        Allergen::where('id', $id)->update($data);

        return redirect()->route('admin.allergens')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        Allergen::whereIn('id', $ids)->delete();
        if (count($ids) > 1) {
            $msg = 'Allergens deleted successfully';
        } else {
            $msg = 'Allergen Level deleted successfully';
        }
        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        Allergen::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }
}
