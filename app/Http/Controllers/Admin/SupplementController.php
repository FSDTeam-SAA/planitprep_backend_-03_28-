<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplement;
use App\Models\SupplementType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SupplementController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Supplements';
        if ($request->ajax()) {
            $data = Supplement::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.supplements.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
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

        return view('admin.supplements.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create Supplement';
        $types = SupplementType::where('active', 1)->get();

        return view('admin.supplements.supplement', compact('action', 'title', 'types'));
    }

    private function _form_validation($request)
    {
        $rules = [
            'name' => 'required|string|max:50|unique:supplements,name,'.($request->id ?? 'null'),
            'supplement_type_id' => 'required',
            'dosage' => 'required',
        ];
        $messages = [
            'name.required' => 'Name is required',
            'supplement_type_id.required' => 'Supplement Type is required',
            'dosage.required' => 'Dosage is required',
        ];
        $this->validate($request, $rules, $messages);

        $postData = [
            'name' => $request->name,
            'supplement_type_id' => $request->supplement_type_id,
            'dosage' => $request->dosage,
            'benefits' => $request->benefits ? $request->benefits : '',
            'side_effects' => $request->side_effects ? $request->side_effects : '',
            'active' => isset($request->active) ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            $path = 'public/supplements';
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
        Supplement::insert($data);

        return redirect()->route('admin.supplements')->with('success', 'Supplement saved successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Supplement';
        $record = Supplement::find($id);
        $types = SupplementType::where('active', 1)->get();

        return view('admin.supplements.supplement', compact('action', 'title', 'record', 'types'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        Supplement::where('id', $id)->update($data);

        return redirect()->route('admin.supplements')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        SupplementType::whereIn('id', $ids)->delete();
        if (count($ids) > 1) {
            $msg = 'Supplements deleted successfully';
        } else {
            $msg = 'Supplement deleted successfully';
        }
        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        Supplement::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }
}
