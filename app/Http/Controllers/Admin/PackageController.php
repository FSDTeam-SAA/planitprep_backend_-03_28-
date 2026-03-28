<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PackageController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Packages';

        if ($request->ajax()) {
            $data = Package::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0 text-end">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.packages.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Delete">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
                })
                ->editColumn('description', function ($row) {
                    return Str::limit($row->description, 150);
                })
                ->editColumn('price', function ($row) {
                    return number_format($row->price, 2);
                })
                ->editColumn('discounted_price', function ($row) {
                    return number_format($row->discounted_price, 2);
                })
                ->editColumn('image', function ($row) {
                    $img = asset('admin/images/noimg.jpg');

                    if ($row->image != '') {
                        if (Storage::exists('public/packages/'.$row->image)) {
                            $img = Storage::url('public/packages/'.$row->image);
                        }
                    }

                    return "<img class='photo-thumb img-fluid rounded rounded-circle' src='".$img."'>";
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
                ->rawColumns(['image', 'price', 'discounted_price', 'description', 'action', 'active', 'created_at'])
                ->make(true);
        }

        return view('admin.packages.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create Package';

        return view('admin.packages.package', compact('action', 'title'));
    }

    private function _form_validation($request)
    {
        if ($request->form_type == 'Edit') {
            $rules = [
                'title' => 'required|string|max:255|unique:App\Models\Package,title, '.$request->id,
                'price' => 'required|numeric',
                'discounted_price' => 'nullable',
                'description' => 'nullable',
            ];
            $messages = [
                'title.required' => 'Title is required',
                'title.string' => 'Title must be a string',
                'price.required' => 'Price is required',
                'price.numeric' => 'Invalid price',
            ];

            $this->validate($request, $rules, $messages);
        } else {
            $rules = [
                'title' => 'required|string|max:255|unique:App\Models\Package,title',
                'price' => 'required|numeric',
                'discounted_price' => 'nullable',
                'description' => 'nullable',
            ];
            $messages = [
                'title.required' => 'Title is required',
                'title.string' => 'Title must be a string',
                'price.required' => 'Price is required',
                'price.numeric' => 'Invalid price',
            ];

            $this->validate($request, $rules, $messages);
        }

        $postData = [
            'title' => $request->title,
            'price' => $request->price,
            'discounted_price' => $request->discounted_price ? $request->discounted_price : 0,
            'description' => $request->description,
            'active' => isset($request->active) ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            $path = 'public/packages';
            $file = $request->file('image');
            $imagename = time().'_'.$file->getClientOriginalName();
            $request->file('image')->storeAs($path, $imagename);
            $postData['image'] = $imagename;
        }

        return $postData;
    }

    public function store(Request $request)
    {
        $data = $this->_form_validation($request);
        $data['created_at'] = date('Y-m-d H:i:s');

        Package::insert($data);

        return redirect()->route('admin.packages')->with('success', 'Package added successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Package';
        $record = Package::find($id);

        $image = $record->image;
        $image = explode('/', $image);
        $image = $image[count($image) - 1];

        if ($image != '') {
            if (! Storage::disk('public')->exists('packages/'.$image)) {
                $image = '';
            }
        } else {
            $image = '';
        }

        $record->image = $image;

        return view('admin.packages.package', compact('action', 'title', 'record'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        Package::where('id', $id)->update($data);

        return redirect()->route('admin.packages')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        Package::whereIn('id', $ids)->delete();

        if (count($ids) > 1) {
            $msg = 'Packages deleted successfully';
        } else {
            $msg = 'Package deleted successfully';
        }

        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        Package::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }
}
