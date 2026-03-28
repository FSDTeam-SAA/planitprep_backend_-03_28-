<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Testimonials';

        if ($request->ajax()) {
            $data = Testimonial::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.edit-testimonial', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Remove">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
                })
                ->addColumn('image', function ($row) {
                    $image = asset('admin/images/imgpreview-lg.jpg');

                    if ($row->image) {
                        if (Storage::exists($row->image)) {
                            $image = asset(Storage::url($row->image));
                        } else {
                            $image = asset('admin/images/defaultUser.png');
                        }
                    } else {
                        $image = asset('admin/images/defaultUser.png');
                    }

                    return "<img class='thumb-1' src='".$image."'>";
                })
                ->editColumn('name', function ($row) {
                    return Str::limit($row->name, 30);
                })
                ->editColumn('designation', function ($row) {
                    return Str::limit($row->designation, 30);
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
                ->rawColumns(['name', 'designation', 'image', 'action', 'description', 'active', 'created_at'])
                ->make(true);
        }

        return view('admin.testimonials.index', compact('title'));
    }

    public function create()
    {
        $action = 'create';

        return view('admin.testimonials.form', compact('action'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:testimonials,name,'.($request->id ?? 'null'),
            'designation' => 'required|string|max:250',
            'rating' => 'required',
            'review' => 'required',
            'image' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $testimonial = new Testimonial;

        if ($request->file('image') != null) {
            $image = $request->file('image');
            $uploadPath = 'public/uploads/testimonial';
            $testimonial->image = $image->store($uploadPath);
        }

        $testimonial->name = $request->name ? $request->name : '';
        $testimonial->designation = $request->designation ? $request->designation : '';
        $testimonial->rating = $request->rating ? $request->rating : 0;
        $testimonial->review = $request->review ? $request->review : '';
        $testimonial->active = $request->active ? 1 : 0;
        $testimonial->save();

        session()->flash('message', 'Testimonial added successfully.');

        return to_route('admin.testimonials');
    }

    public function edit(Testimonial $testimonial)
    {
        if ($testimonial->image != null) {
            if (Storage::exists($testimonial->image)) {
                $testimonial->image = asset(Storage::url($testimonial->image));
            }
        }

        $action = 'edit';

        return view('admin.testimonials.form', compact('action', 'testimonial'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:testimonials,name,'.($request->id ?? 'null'),
            'designation' => 'required|string|max:250',
            'rating' => 'required',
            'review' => 'required',
            'image' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $testimonial = Testimonial::where('id', $request->id)->first();

        if ($request->file('image') != null) {
            $oldFile = $testimonial->image;

            $image = $request->file('image');
            $uploadPath = 'public/uploads/testimonial';
            $testimonial->image = $image->store($uploadPath);

            Storage::delete($oldFile);
        }

        $testimonial->name = $request->name ? $request->name : '';
        $testimonial->designation = $request->designation ? $request->designation : '';
        $testimonial->rating = $request->rating ? $request->rating : 0;
        $testimonial->review = $request->review ? $request->review : '';
        $testimonial->active = $request->active ? 1 : 0;
        $testimonial->save();

        $request->session()->put('success', 'Testimonial details updated successfully.');

        return to_route('admin.testimonials');
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        Testimonial::where('id', $id)->update(['active' => $active]);

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

        $images = Testimonial::whereIn('id', $ids)->pluck('image');

        foreach ($images as $img) {
            Storage::delete($img);
        }

        Testimonial::whereIn('id', $ids)->delete();

        if (count($ids) > 1) {
            $msg = 'Testimonials deleted successfully';
        } else {
            $msg = 'Testimonial deleted successfully';
        }

        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }
}
