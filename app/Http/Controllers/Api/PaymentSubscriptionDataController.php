<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription as UserSubscription;

class PaymentSubscriptionDataController extends Controller
{
    /**
     * Get current user payments & subscriptions
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load([
            'payments',
            'subscriptions'
        ]);

        $data = [
            'user' => [
                'id'        => $user->id,
                'username'  => $user->username,
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'phone'     => $user->phone,
            ],

            'subscriptions' => $user->subscriptions->map(function ($sub) {
                return [
                    'id'                     => $sub->id,
                    'is_subscribed'          => $sub->is_subscribed,
                    'stripe_subscription_id' => $sub->stripe_subscription_id,
                    'stripe_price_id'        => $sub->stripe_price_id,
                    'status'                 => $sub->status,
                    'cancel_at_period_end'   => $sub->cancel_at_period_end,
                    'current_period_start'   => optional($sub->current_period_start)?->toDateTimeString(),
                    'current_period_end'     => optional($sub->current_period_end)?->toDateTimeString(),
                    'canceled_at'            => optional($sub->canceled_at)?->toDateTimeString(),
                    'created_at'             => $sub->created_at->toDateTimeString(),
                ];
            }),

            'payments' => $user->payments->map(function ($pay) {
                return [
                    'id'                        => $pay->id,
                    'stripe_invoice_id'         => $pay->stripe_invoice_id,
                    'stripe_payment_intent_id' => $pay->stripe_payment_intent_id,
                    'amount'                    => $pay->amount,
                    'currency'                  => $pay->currency,
                    'status'                    => $pay->status,
                    'type'                      => $pay->type,
                    'payment_method'            => $pay->payment_method,
                    'created_at'                => $pay->created_at->toDateTimeString(),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Check if the user is subscribed for a given price ID
     */
    public function checkSubscription(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string',
        ]);

        
        $userId = $request->user_id;
        
        $priceId = $request->price_id;

        $subscription = UserSubscription::where('user_id', $userId)
            ->where('stripe_price_id', $priceId)
            ->first();

        $isSubscribed = $subscription ? $subscription->is_subscribed : 0;

        return response()->json([
            'success' => true,
            'is_subscribed' => $isSubscribed
        ]);
    }
}
