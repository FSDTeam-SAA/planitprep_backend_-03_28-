<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserMembership;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class MembershipController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function index(Request $request)
    {
        $title = 'Membership';

        if ($request->ajax()) {
            $data = Membership::where('id', '<>', 0);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <ul class="list-inline me-auto mb-0 text-end">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Edit">
                    <a href="'.route('admin.memberships.edit', $row->id).'" class="edit avtar avtar-xs btn-link-success btn-pc-default"><i class="ti ti-edit-circle f-18"></i></a>
                    </li>
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Delete">
                    <a href="javascript:void(0)" class="deleteSelSingle delete avtar avtar-xs btn-link-danger btn-pc-default" data-val='.$row->id.'> <i class="ti ti-trash f-18"></i></a>
                    </li>';

                    return $actionBtn;
                })
                ->editColumn('title', function ($row) {
                    return Str::limit($row->title, 150);
                })
                ->editColumn('price', function ($row) {
                    return '&#x20B9;'.number_format($row->price, 2);
                })
                ->editColumn('month', function ($row) {
                    return $row->month;
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
                ->rawColumns(['title', 'price', 'month', 'action', 'active', 'created_at'])
                ->make(true);
        }

        return view('admin.memberships.index', compact('title'));
    }

    public function create()
    {
        $action = 'Create';
        $title = 'Create Membership';

        return view('admin.memberships.membership', compact('action', 'title'));
    }

    private function _form_validation($request)
    {
        if ($request->form_type == 'Edit') {
            $rules = [
                'title' => 'required|string|max:50|unique:memberships,name,'.($request->id ?? 'null'),
                'price' => 'required',
                'month' => 'required',
                'active' => 'nullable',
            ];
            $messages = [
                'title.required' => 'Title is required',
                'price.required' => 'Price is required',
                'month.required' => 'Month is required',
            ];

            $this->validate($request, $rules, $messages);
        } else {
            $rules = [
                'title' => 'required|unique:App\Models\Membership,title',
                'price' => 'required',
                'month' => 'required',
                'active' => 'nullable',
            ];
            $messages = [
                'title.required' => 'Title is required',
                'price.required' => 'Price is required',
                'month.required' => 'Month is required',
            ];

            $this->validate($request, $rules, $messages);
        }

        $postData = [
            'title' => $request->title,
            'price' => $request->price,
            'month' => $request->month,
            'active' => $request->active == null ? 0 : 1,
        ];

        return $postData;
    }

    public function store(Request $request)
    {
        $data = $this->_form_validation($request);
        $data['created_at'] = date('Y-m-d H:i:s');

        Membership::insert($data);

        return redirect()->route('admin.memberships')->with('success', 'Membership added successfully.');
    }

    public function edit($id)
    {
        $action = 'Edit';
        $title = 'Edit Membership';
        $record = Membership::find($id);

        return view('admin.memberships.membership', compact('action', 'title', 'record'));
    }

    public function update($id, Request $request)
    {
        $data = $this->_form_validation($request);
        $data['updated_at'] = date('Y-m-d H:i:s');
        Membership::where('id', $id)->update($data);

        return redirect()->route('admin.memberships')->with('success', 'Changes saved successfully.');
    }

    public function destroy(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);

        Membership::whereIn('id', $ids)->delete();

        if (count($ids) > 1) {
            $msg = 'Memberships deleted successfully';
        } else {
            $msg = 'Membership deleted successfully';
        }

        $request->session()->put('success', $msg);

        return response(['status' => true, 'msg' => $msg]);
    }

    public function updateActive(Request $request)
    {
        $id = $request->id;
        $active = ($request->value == 0) ? 1 : 0;
        Membership::where('id', $id)->update(['active' => $active]);
        $msg = 'Record has been deactivated';
        if ($active == 1) {
            $msg = 'Record has been activated';
        }

        return response()->json(['status' => true, 'msg' => $msg]);
    }

    public function user_membership($id)
    {
        $dt = Carbon::now()->format('Y-m-d');
        $user = User::select('id', 'username', 'full_name')->where('id', $id)->first();
        $userMembership = UserMembership::where('user_id', $id)->limit(1)->latest()->first();
        $memberships = Membership::get();
        $title = 'User Membership';

        return view('admin.memberships.user-membership', compact('title', 'user', 'userMembership', 'memberships'));
    }

    public function store_user_membership(Request $request)
    {
        $user_id = $request->id;
        $membership = Membership::where('id', $request->membership)->first();

        $r = new UserMembership;
        $r->user_id = $user_id;
        $r->membership_id = $membership->id;
        $r->start_date = Carbon::now()->format('Y-m-d');
        $r->end_date = Carbon::now()->addMonths($membership->month)->format('Y-m-d');
        $r->save();

        return redirect()->route('admin.users')->with('success', 'Membership details saved successfully.');
    }

    public function expired_memberships(Request $request)
    {
        $title = 'Expired Memberships';
        $dt = Carbon::now()->format('Y-m-d');

        if ($request->ajax()) {
            $data = UserMembership::where('end_date', '<', $dt)->orderBy('end_date', 'asc');

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->full_name;
                })
                ->editColumn('membership', function ($row) {
                    return $row->membership->title;
                })
                ->editColumn('price', function ($row) {
                    return '&#x20B9;'.number_format($row->membership->price, 2);
                })
                ->editColumn('start_date', function ($row) {
                    return $row->start_date;
                })
                ->editColumn('end_date', function ($row) {
                    return $row->end_date;
                })
                ->editColumn('month', function ($row) {
                    return $row->membership->month;
                })
                ->rawColumns(['title', 'price', 'month'])
                ->make(true);
        }

        return view('admin.memberships.expired-memberships', compact('title'));
    }

    public function expiring_memberships(Request $request)
    {
        $title = 'Expiring Memberships';
        $dt = Carbon::now()->format('Y-m-d');
        $dt2 = Carbon::now()->addDays(3)->format('Y-m-d');

        if ($request->ajax()) {
            $data = UserMembership::whereBetween('end_date', [$dt, $dt2])->orderBy('end_date', 'asc');

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->full_name;
                })
                ->editColumn('membership', function ($row) {
                    return $row->membership->title;
                })
                ->editColumn('price', function ($row) {
                    return '&#x20B9;'.number_format($row->membership->price, 2);
                })
                ->editColumn('start_date', function ($row) {
                    return $row->start_date;
                })
                ->editColumn('end_date', function ($row) {
                    return $row->end_date;
                })
                ->editColumn('month', function ($row) {
                    return $row->membership->month;
                })
                ->rawColumns(['title', 'price', 'month'])
                ->make(true);
        }

        return view('admin.memberships.expiring-memberships', compact('title'));
    }

    public function memberships(Request $request)
    {
        $memberships = Membership::where('active', 1)->get();
        $user_id = $request->user_id;

        // Fetch all coupons.
        $dt = date('Y-m-d');

        $allCoupons = Coupon::where('coupon_type', 'A')
            ->where('active', 1)
            ->where('start_date', '<=', $dt)
            ->where('end_date', '>=', $dt)
            ->select('coupons.*')
            ->get()->toArray();

        $userCoupons = Coupon::join('coupon_users', 'coupon_users.coupon_id', '=', 'coupons.id')
            ->where('coupon_users.user_id', $user_id)
            ->where('coupon_users.active', 1)
            ->where('coupon_users.expiry_date', '>=', $dt)
            ->where('coupons.active', 1)
            ->select('coupons.*')
            ->get()->toArray();

        $coupons = array_merge($allCoupons, $userCoupons);

        return view('admin.membership-list', compact('memberships', 'user_id', 'coupons'))->render();
    }
}
