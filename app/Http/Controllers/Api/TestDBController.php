<?php

namespace App\Http\Controllers\Api;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Models\Payment;
use App\Models\subscription as Userrequestscription;

class TestDBController extends Controller{
    public function test(Request $request){

    try{
        Payment::create([
            'user_id' => $request->userId,
            'stripe_payment_intent_id' => $request->id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status' => $request->status,
            'type' => 'requestscription',
            'payment_method' => $request->payment_method,
                    ]);

            Userrequestscription::create([
                'user_id' => $request->userId,
                'stripe_subscription_id' => $request->id,
                'stripe_price_id' => $request->id,
                'status' => $request->status,
                //'current_period_start' => Carbon::createFromTimestamp($request->current_period_start),
                //'current_period_end'   => Carbon::createFromTimestamp($request->current_period_end),
                    ]);
    }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['status' => 'ok']);
    }
}