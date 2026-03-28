<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Pages';

        if ($request->ajax()) {
            $data = Page::where('id', '>', 0);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-original-title="Details">
                    <a href="'.route('admin.pages.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-settings f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
                })
                ->editColumn('title', function ($row) {
                    return Str::limit($row->title, 50);
                })
                ->editColumn('alias', function ($row) {
                    return Str::limit($row->alias, 50);
                })
                ->addColumn('active', function ($row) {
                    $checked = '';
                    if ($row->active == 1) {
                        $checked = 'checked';
                    }

                    return '<div class="form-check form-switch custom-switch-v1 mb-2" data-bs-toggle="tooltip" data-bs-original-title="Activate / Deactivate">
                        <input type="checkbox" class="form-check-input input-success active_item" data-value="'.$row->active.'" data-id="'.$row->id.'" '.$checked.'>
                    </div>';
                })
                ->editColumn('created_at', function ($row) {
                    return date('Y-m-d H:i:s A', strtotime($row->created_at));
                })
                ->rawColumns(['title', 'image', 'alias', 'active', 'action', 'created_at'])
                ->make(true);
        }

        return view('admin.pages.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Page';
        $action = 'Create';

        return view('admin.pages.page', compact('title', 'action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191|unique:App\Models\Page,title',
            'alias' => 'nullable|string|max:191|unique:App\Models\Page,alias',
            'content' => 'required',
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string|max:191',
            'active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $page = new Page;

        if ($request->file('image') != null) {
            $image = $request->file('image');
            $uploadPath = 'uploads/page';
            $page->image = $image->store($uploadPath, 'public');
        }

        $page->title = $request->title;
        $page->alias = $request->alias ? $request->alias : Str::slug($request->title, '-');
        $page->content = $request->content;
        $page->meta_title = $request->meta_title ? $request->meta_title : '';
        $page->meta_description = $request->meta_description ? $request->meta_description : '';
        $page->active = $request->active == null ? 0 : 1;
        $page->save();

        session()->flash('message', 'Page saved successfully.');

        return to_route('admin.pages.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Page';
        $action = 'Edit';
        $page = Page::where('id', $id)->first();

        return view('admin.pages.page', compact('title', 'page', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191|unique:App\Models\Page,title,'.$id,
            'alias' => 'nullable|string|max:191|unique:App\Models\Page,alias,'.$id,
            'content' => 'required',
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string|max:191',
            'active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $page = Page::where('id', $id)->first();

        if ($request->file('image') != null) {
            if ($page->image != null) {
                Storage::delete($page->image);
            }

            $image = $request->file('image');
            $uploadPath = 'uploads/page';
            $page->image = $image->store($uploadPath, 'public');
        }

        $page->title = $request->title;
        $page->alias = $request->alias ? $request->alias : Str::slug($request->title, '-');
        $page->content = $request->content;
        $page->meta_title = $request->meta_title ? $request->meta_title : '';
        $page->meta_description = $request->meta_description ? $request->meta_description : '';
        $page->active = $request->active == null ? 0 : 1;
        $page->save();

        session()->flash('message', 'Page updated successfully.');

        return to_route('admin.pages.index');
    }

    /**
     * Enable disable record.
     */
    public function toggle(Request $request)
    {
        $page = Page::where('id', $request->id)->first();

        $page->active = $page->active ^ 1;
        $page->save();

        return response()->json(['status' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Page::where('id', $request->id)->delete();
        session()->flash('message', 'Page deleted successfully.');
        $msg = 'Page deleted successfully.';

        return response(['status' => true, 'msg' => $msg]);
    }
}
