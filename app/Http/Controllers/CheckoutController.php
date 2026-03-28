<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserMembership;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Razorpay\Api\Api;

class CheckoutController extends Controller
{
    // Open checkout page so that user can check and apply coupon if any.
    public function checkout(Request $request, $id)
    {
        $user = Auth::user();
        $membership = Membership::where('id', $id)->first();

        $data = [
            'username' => $user->username,
            'phone' => $user->phone,
            'email' => $user->email,
            'plan' => $membership->title.' '.$membership->month.' month',
            'plan_id' => $membership->id,
            'amount' => $membership->price,
        ];

        return view('checkout', compact('data'));
    }

    /**
     * We have plan id and coupon.
     * Check coupon
     * Create order
     */
    public function complete_order(Request $request)
    {
        $user = Auth::user();
        $plan = Membership::where('id', $request->plan_id)->first();

        // Add inactive user memebership record. Delete if previous exist.
        UserMembership::where('user_id', $user->id)->where('membership_id', $plan->id)->where('active', 0)->delete();

        $userMembership = new UserMembership;
        $userMembership->user_id = $user->id;
        $userMembership->membership_id = $plan->id;
        $userMembership->start_date = date('Y-m-d');
        $userMembership->end_date = Carbon::now()->addMonth($plan->month)->format('Y-m-d');
        $userMembership->active = 0;
        $userMembership->save();

        // Check coupon
        $coupon = $this->fetch_coupon($request->coupon, $plan->price);
        $amount = $plan->price;

        if ($coupon != null) {
            $amount = $coupon['amount'];
        }

        // Generate order id
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $order = $api->order->create([
            'amount' => $amount * 100,
            'currency' => 'INR',
            'receipt' => 'order_rcptid_'.time(),
            'payment_capture' => 1,
        ]);

        $orderId = $order->id;

        // Add inactive payment record. Delete if old record exist.
        Payment::where('user_id', $user->id)->where('user_membership_id', $userMembership->id)->delete();

        $payment = new Payment;
        $payment->user_membership_id = $userMembership->id;
        $payment->user_id = $user->id;
        $payment->price = $plan->price;
        $payment->status = 'P';
        $payment->order_id = $orderId;

        if ($coupon != null) {
            $payment->coupon_id = $coupon['coupon_id'];
            $payment->discount_amount = $coupon['discount'];
        } else {
            $payment->discount_amount = 0;
        }

        $payment->save();

        echo $orderId;

        exit;
    }

    public function complete_payment(Request $request, $id)
    {
        // Fetch logo
        $settings = Setting::first();
        $logo = asset('imgs/logo.png');

        // If custom logo is set in settings, check if it exists
        if ($settings && $settings->logo != '') {
            if (Storage::disk('public')->exists($settings->logo)) {
                $logo = asset(Storage::url($settings->logo));
            }
        }

        $payment = Payment::where('order_id', $id)->first();
        $amount = $payment->price;

        if ($payment->discount_amount > 0) {
            $amount = $payment->discount_amount;
        }

        $user = User::where('id', $payment->user_id)->first();

        $data = [
            'order_id' => $id,
            'amount' => $amount,
            'currency' => 'INR',
            'customer_name' => $user->username,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'logo' => $logo,
            'company_name' => 'Dietician',
        ];

        if ($request->filled('email')) {
            $data['customer_email'] = $request->email;
        }

        if ($request->filled('phone')) {
            $data['customer_phone'] = $request->phone;
        }

        return view('complete-payment', compact('data'));
    }

    public function apply_coupon(Request $request)
    {
        $dt = date('Y-m-d');
        $coupon = Coupon::where('code', $request->coupon)
            ->where('coupon_type', 'A')
            ->where('active', 1)
            ->where('start_date', '<=', $dt)
            ->where('end_date', '>=', $dt)
            ->first();

        if ($coupon == null) {
            return 0;
        } else {
            $amount = $request->amount;
            $discount_amount = 0;

            if ($coupon->discount_type == 'flat') {
                $discount_amount = $coupon->amount;
            } elseif ($coupon->discount_type == 'percent') {
                $discount_amount = ($amount / 100) * $coupon->amount;
            }

            return [
                'discount' => $discount_amount,
                'amount' => $amount - $discount_amount,
            ];
        }
    }

    public function fetch_coupon($coupon, $amount)
    {
        $dt = date('Y-m-d');
        $coupon = Coupon::where('code', $coupon)
            ->where('coupon_type', 'A')
            ->where('active', 1)
            ->where('start_date', '<=', $dt)
            ->where('end_date', '>=', $dt)
            ->first();

        if ($coupon == null) {
            return null;
        } else {
            $discount_amount = 0;

            if ($coupon->discount_type == 'flat') {
                $discount_amount = $coupon->amount;
            } elseif ($coupon->discount_type == 'percent') {
                $discount_amount = ($amount / 100) * $coupon->amount;
            }

            return [
                'coupon_id' => $coupon->id,
                'discount' => $discount_amount,
                'amount' => $amount - $discount_amount,
            ];
        }
    }

    public function process_payment_link(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $userId = Crypt::decryptString($request->user);
        $amount = Crypt::decryptString($request->amount);
        $plan_id = Crypt::decryptString($request->plan_id);

        $coupon_id = 0;
        $discount = 0;

        if ($request->filled('coupon_id')) {
            $coupon_id = Crypt::decryptString($request->coupon_id);
            $discount = Crypt::decryptString($request->discount);
        }

        $userDetails = User::where('id', $userId)->select('username', 'email', 'phone')->first();

        // Add inactive user memebership record.
        $plan = Membership::where('id', $plan_id)->first();

        // Delete if old record exist.
        UserMembership::where('user_id', $userId)->where('membership_id', $plan_id)->where('active', 0)->delete();

        $userMembership = new UserMembership;
        $userMembership->user_id = $userId;
        $userMembership->membership_id = $plan_id;
        $userMembership->start_date = date('Y-m-d');
        $userMembership->end_date = Carbon::now()->addMonth($plan->month)->format('Y-m-d');
        $userMembership->active = 0;
        $userMembership->save();

        // Generate order id
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $order = $api->order->create([
            'amount' => $amount * 100,
            'currency' => 'INR',
            'receipt' => 'order_rcptid_'.time(),
            'payment_capture' => 1,
        ]);

        $orderId = $order->id;

        // Add inactive payment record. Delete if old record exist.
        Payment::where('user_id', $userId)->where('user_membership_id', $userMembership->id)->delete();

        $payment = new Payment;
        $payment->user_membership_id = $userMembership->id;
        $payment->user_id = $userId;
        $payment->price = $plan->price;
        $payment->status = 'P';
        $payment->order_id = $orderId;

        if ($coupon_id == 0) {
            $payment->coupon_id = 0;
            $payment->discount_amount = 0;
        } else {
            $payment->coupon_id = $coupon_id;
            $payment->discount_amount = $discount;
        }

        $payment->save();

        $settings = Setting::first();
        $logo = asset('imgs/logo.png');

        // If custom logo is set in settings, check if it exists
        if ($settings && $settings->logo != '') {
            if (Storage::disk('public')->exists($settings->logo)) {
                $logo = asset(Storage::url($settings->logo));
            }
        }

        $data = [
            'order_id' => $orderId,
            'amount' => $amount * 100,
            'currency' => 'INR',
            'customer_name' => $userDetails->username,
            'customer_email' => $userDetails->email,
            'customer_phone' => $userDetails->phone,
            'plan' => $plan,
            'logo' => $logo,
            'company_name' => 'Dietician',
        ];

        return view('pay', compact('data'));
    }

    /**
     * Returns payment status against payment id captured, authorized, failed, or refunded
     */
    public function check_payment(Request $request)
    {
        $paymentId = $request->payment_id;

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $payment = $api->payment->fetch($paymentId);

            if ($payment['status'] == 'captured') {
                // Update payment attributes.
                $r = Payment::where('order_id', $payment['order_id'])->first();
                $r->status = 'S';
                $r->payment_method = $payment['method'];
                $r->transaction_id = $payment['id'];
                $r->response = $payment['status'];
                $r->save();

                // Active user plan.
                $uM = UserMembership::where('id', $r['user_membership_id'])->first();
                $uM->active = 1;
                $uM->save();

                echo 'done';
                exit;
            } else {
                echo 'false';
                exit;
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch payment details: '.$e->getMessage(),
            ], 500);
        }

        exit;
    }

    public function payment_success(Request $request)
    {
        $transactionId = null;

        if ($request->filled('transaction')) {
            $transactionId = $request->transaction;
        }

        return view('payment-success', compact('transactionId'));
    }

    public function payment_failure(Request $request)
    {
        return view('payment-failure');
    }
}
