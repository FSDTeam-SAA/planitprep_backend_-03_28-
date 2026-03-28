<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class FeatureController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Features';

        if ($request->ajax()) {
            $data = Feature::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0 text-end">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.features.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Delete">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
                })
                ->editColumn('title', function ($row) {
                    return Str::limit($row->title, 300);
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
                ->rawColumns(['title', 'action', 'active', 'created_at'])
                ->make(true);
        }

        return view('admin.features.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create Feature';

        return view('admin.features.feature', compact('action', 'title'));
    }

    private function _form_validation($request)
    {
        if ($request->form_type == 'Edit') {
            $rules = [
                'title' => 'required|unique:App\Models\Feature,title, '.$request->id,
                'active' => 'nullable',
            ];
            $messages = [
                'title.required' => 'Title is required',
            ];

            $this->validate($request, $rules, $messages);
        } else {
            $rules = [
                'title' => 'required|unique:App\Models\Feature,title',
                'active' => 'nullable',
            ];
            $messages = [
                'title.required' => 'Title is required',
            ];

            $this->validate($request, $rules, $messages);
        }

        $postData = [
            'title' => $request->title,
            'active' => $request->active == null ? 0 : 1,
        ];

        return $postData;
    }

    public function store(Request $request)
    {
        $data = $this->_form_validation($request);
        $data['created_at'] = date('Y-m-d H:i:s');

        Feature::insert($data);

        return redirect()->route('admin.features')->with('success', 'Feature added successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Feature';
        $record = Feature::find($id);

        return view('admin.features.feature', compact('action', 'title', 'record'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        Feature::where('id', $id)->update($data);

        return redirect()->route('admin.features')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        Feature::whereIn('id', $ids)->delete();

        if (count($ids) > 1) {
            $msg = 'Features deleted successfully';
        } else {
            $msg = 'Feature deleted successfully';
        }

        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        Feature::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }
}
