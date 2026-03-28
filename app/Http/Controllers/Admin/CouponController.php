<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class CouponController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Coupons';
        if ($request->ajax()) {
            $data = Coupon::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return "<input type='checkbox' class='form-check-input' data-id='".$row->id."'> ";
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.coupons.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Users">
                    <a href="'.route('admin.coupon-users.index', ['coupon_id' => $row->id]).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-users f-18"></i></a>
                    </li>

                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li></ul>';

                    return $actionBtn;
                })

                ->editColumn('code', function ($row) {
                    $actionBtn = $row->code;

                    return $actionBtn;
                })
                ->editColumn('coupon_type', function ($row) {
                    return $row->coupon_type == 'A' ? 'Admin' : 'User';
                })
                ->editColumn('type', function ($row) {
                    return $row->type == 'A' ? 'Admin' : 'User';
                })
                ->editColumn('start_date', function ($row) {
                    if ($row->start_date) {
                        return date('Y-m-d', strtotime($row->start_date));
                    } else {
                        return '-';
                    }
                })
                ->editColumn('end_date', function ($row) {
                    if ($row->end_date) {
                        return date('Y-m-d', strtotime($row->end_date));
                    } else {
                        return '-';
                    }
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
                ->rawColumns(['action', 'checkbox', 'code', 'coupon_type', 'type', 'active', 'start_date', 'end_date', 'created_at'])
                ->make(true);
        }

        return view('admin.coupons.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create Coupon';

        return view('admin.coupons.coupon', compact('action', 'title'));
    }

    private function _form_validation($request)
    {
        $rules = [
            'code' => 'min:6|required|string|max:50|unique:coupons,code,'.($request->id ?? 'null'),
            'title' => 'required|string|max:50|unique:coupons,title,'.($request->id ?? 'null'),
            'discount_type' => 'required',
            'amount' => 'required',
            'limit' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'description' => 'nullable',
            'active' => 'nullable',
        ];
        $messages = [
            'code.required' => 'Code is required',
        ];
        $this->validate($request, $rules, $messages);

        $postData = [
            'code' => $request->code,
            'title' => $request->title,
            'discount_type' => $request->discount_type,
            'amount' => $request->amount,
            'limit' => $request->limit,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'active' => $request->active == null ? '0' : '1',
            'type' => $request->type,
        ];

        return $postData;
    }

    public function store(Request $request)
    {
        $data = $this->_form_validation($request);
        $data['created_at'] = date('Y-m-d H:i:s');
        Coupon::insert($data);

        return redirect()->route('admin.coupons')->with('success', 'Coupon saved successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Coupon';
        $record = Coupon::find($id);

        return view('admin.coupons.coupon', compact('action', 'title', 'record'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        Coupon::where('id', $id)->update($data);

        return redirect()->route('admin.coupons')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        Coupon::whereIn('id', $ids)->delete();
        if (count($ids) > 1) {
            $msg = 'Coupons deleted successfully';
        } else {
            $msg = 'Coupon deleted successfully';
        }
        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        Coupon::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }

    public function generateRandomCoupon()
    {
        $code = Str::random(6);

        return $code;
    }

    public function getCoupon($id)
    {
        $coupon = Coupon::find($id);

        return json_encode($coupon);
    }
}
