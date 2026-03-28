<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Services';

        if ($request->ajax()) {
            $data = Service::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.edit-service', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Remove">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
                })
                ->addColumn('image', function ($row) {
                    $image = asset('admin/images/imgpreview-lg.jpg');

                    if ($row->image != '') {
                        if (Storage::exists($row->image)) {
                            $image = asset(Storage::url($row->image));
                        }
                    }

                    return "<img class='thumb-1 bg-gray-200' src='".$image."'>";
                })
                ->editColumn('title', function ($row) {
                    return Str::limit($row->title, 50);
                })
                ->addColumn('active', function ($row) {
                    $checked = '';

                    if ($row->active == 1) {
                        $checked = 'checked';
                    }

                    return '<div class="form-check form-switch custom-switch-v1 mb-2">
                        <input type="checkbox" class="form-check-input input-success active_item" data-name="'.$row->title.'" data-value="'.$row->active.'" data-id="'.$row->id.'" '.$checked.'>
                    </div>';
                })
                ->editColumn('created_at', function ($row) {
                    return date('Y-m-d H:i:s a', strtotime($row->created_at));
                })
                ->editColumn('created_at', function ($row) {
                    return date('Y-m-d H:i:s A', strtotime($row->created_at));
                })
                ->rawColumns(['title', 'image', 'action', 'description', 'active', 'created_at', 'created_at'])
                ->make(true);
        }

        return view('admin.services.index', compact('title'));
    }

    public function create()
    {
        $action = 'create';

        return view('admin.services.form', compact('action'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191|unique:App\Models\Service,title',
            'alias' => 'nullable|string|max:191|unique:App\Models\Service,alias',
            'image' => 'required|file|max:10240|mimes:jpg,jpeg,png,webp',
            'short_description' => 'required|string|max:191',
            'description' => 'nullable',
            'rank' => 'nullable',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $service = new Service;

        if ($request->file('image') != null) {
            $image = $request->file('image');
            $uploadPath = 'public/uploads/service';
            $service->image = $image->store($uploadPath);
        }

        $service->title = $request->title ? $request->title : '';
        $service->alias = $request->alias ? $request->alias : Str::slug($request->title, '-');
        $service->short_description = $request->short_description ? $request->short_description : '';
        $service->description = $request->description ? $request->description : '';
        $service->rank = $request->rank ? $request->rank : 0;
        $service->active = $request->active ? 1 : 0;
        $service->save();

        $request->session()->put('success', 'Service added successfully.');

        return to_route('admin.services');
    }

    public function edit(Service $service)
    {
        if ($service->image != null) {
            if (Storage::exists($service->image)) {
                $service->image = asset(Storage::url($service->image));
            }
        }

        $action = 'edit';

        return view('admin.services.form', compact('action', 'service'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191|unique:App\Models\Service,title,'.$request->id,
            'alias' => 'nullable|unique:App\Models\Service,alias,'.$request->id,
            'image' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp',
            'short_description' => 'required|string|max:191',
            'description' => 'nullable',
            'rank' => 'nullable',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $service = Service::where('id', $request->id)->first();

        if ($request->file('image') != null) {
            $oldFile = $service->image;

            $image = $request->file('image');
            $uploadPath = 'public/uploads/service';
            $service->image = $image->store($uploadPath);

            Storage::delete($oldFile);
        }

        $service->title = $request->title ? $request->title : '';
        $service->alias = $request->alias ? $request->alias : Str::slug($request->title, '-');
        $service->short_description = $request->short_description ? $request->short_description : '';
        $service->description = $request->description ? $request->description : '';
        $service->rank = $request->rank ? $request->rank : 0;
        $service->active = $request->active ? 1 : 0;
        $service->save();

        $request->session()->put('success', 'Service details updated successfully.');

        return to_route('admin.services');
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        Service::where('id', $id)->update(['active' => $active]);

        $msg = 'Record has been deactivated';

        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        $images = Service::whereIn('id', $ids)->pluck('image');

        foreach ($images as $img) {
            Storage::delete($img);
        }

        Service::whereIn('id', $ids)->delete();

        if (count($ids) > 1) {
            $msg = 'Services deleted successfully';
        } else {
            $msg = 'Service deleted successfully';
        }

        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }
}
