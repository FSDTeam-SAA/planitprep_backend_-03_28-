<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class CouponUserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }
    public function index(Request $request)
    {
        $title = 'Assigned users';
        $coupons = Coupon::where('active', 1)->get();
        $coupon_id = 0;
        if (isset($request->coupon_id)) {
            $coupon_id = $request->coupon_id;
        }
        if ($request->ajax()) {
            $data = CouponUser::select(DB::raw('coupon_users.*,c.code as code,u.full_name as firstName'))
                ->leftJoin('users as u', 'u.id', 'coupon_users.user_id')
                ->leftJoin('coupons as c', 'c.id', 'coupon_users.coupon_id');
            if ($coupon_id > 0) {
                $data = $data->where('coupon_users.coupon_id', $coupon_id);
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return "<input type='checkbox' class='form-check-input' data-id='".$row->id."'> ";
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0">
                   <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li></ul>';

                    return $actionBtn;
                })
                ->addColumn('code', function ($row) {
                    return $row->code;
                })
                ->addColumn('username', function ($row) {
                    return $row->firstName;
                })
                ->filterColumn('username', function ($query, $keyword) {
                    $query->where('full_name', 'like', "%{$keyword}%");
                })
                ->editColumn('expiry_date', function ($row) {
                    if ($row->expiry_date) {
                        return date('Y-m-d', strtotime($row->expiry_date));
                    }

                    return '-';
                })
                ->addColumn('active', function ($row) {
                    $checked = '';
                    if ($row->active == 1) {
                        $checked = 'checked';
                    }

                    return '<div class="form-check form-switch custom-switch-v1 mb-2">
                        <input type="checkbox" class="form-check-input input-success record_active" data-name="'.$row->title.'" data-value="'.$row->active.'" data-id="'.$row->id.'" '.$checked.'>
                    </div>';
                })
                ->editColumn('created_at', function ($row) {
                    return date('Y-m-d H:i:s A', strtotime($row->created_at));
                })
                ->rawColumns(['action', 'checkbox', 'code', 'username', 'active', 'created_at'])
                ->make(true);
        }

        return view('admin.coupon-users.index', compact('title', 'coupons', 'coupon_id'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Assign User';
        $coupons = Coupon::where('active', 1)->limit(5)->get();

        return view('admin.coupon-users.coupon-user', compact('action', 'title', 'coupons'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'coupon_id' => 'required',
            'assign_to' => 'required',
            'active' => 'nullable',
        ]);

        // Add coupon record.
        $assign_to = $request->assign_to;
        $coupon_id = $request->coupon_id;
        if ($assign_to == 'all') {
            $user_ids = User::where('active', 1)->get()->pluck('id')->toArray();
        } elseif ($assign_to == 'individual') {
            $user_ids = $request->user_ids;
            // $users = User::whereIn('id', $user_ids)->where('active', 1)->get();
        } elseif ($assign_to == 'lead') {
            $user_ids = User::where('customerType', 'lead')->where('active', 1)->get()->pluck('id')->toArray();
        } elseif ($assign_to == 'customer') {
            $user_ids = User::where('customerType', 'customer')->where('active', 1)->get()->pluck('id')->toArray();
        } elseif ($assign_to == 'trial') {
            $user_ids = User::where('customerType', 'trial')->where('active', 1)->get()->pluck('id')->toArray();
        }

        CouponUser::whereIn('user_id', $user_ids)->where('coupon_id', $coupon_id)->delete();
        $data = [];
        foreach ($user_ids as $id) {
            $data[] = [
                'user_id' => $id,
                'coupon_id' => $coupon_id,
                'expiry_date' => isset($request->expiry_date) ? date('Y-m-d', strtotime($request->expiry_date)) : null,
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        CouponUser::insert($data);

        return redirect()->route('admin.coupon-users.index')->with('success', 'Changes saved successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Assign User';
        $record = CouponUser::find($id);
        $coupons = Coupon::where('active', 1)->where('id', $record->coupon_id)->get();

        return view('admin.coupon-users.coupon-user', compact('action', 'title', 'record', 'coupons'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'code' => 'min:6|required|unique:App\Models\Coupon,code,'.$id,
            'title' => 'required|unique:App\Models\Coupon,title,'.$id,
            'discount_type' => 'required',
            'amount' => 'required',
            'limit' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'description' => 'nullable',
            'active' => 'nullable',
        ]);

        // Update coupon record.
        $coupon = Coupon::where('id', $id)->first();
        $coupon->title = $request->title;
        $coupon->code = $request->code;
        $coupon->discount_type = $request->discount_type;
        $coupon->amount = $request->amount;
        $coupon->limit = $request->limit;
        $coupon->start_date = $request->start_date;
        $coupon->end_date = $request->end_date;
        $coupon->description = $request->description;
        $coupon->active = $request->active == null ? '0' : '1';
        $coupon->type = $request->type;
        $coupon->save();

        return redirect()->route('admin.coupon-users.index')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        CouponUser::whereIn('id', $ids)->delete();
        if (count($ids) > 1) {
            $msg = 'Coupon Users deleted successfully';
        } else {
            $msg = 'Coupon User deleted successfully';
        }
        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        CouponUser::where('id', $id)->update(['active' => $active]);
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
}
