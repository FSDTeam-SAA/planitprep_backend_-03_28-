<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
    try {

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Unauthorized - Api User is required'
            ], 401);
        }

        $request->validate([
            'amount' => 'required|integer|min:100',
        ]);

        StripeService::init();

        $customerId = StripeService::getOrCreateCustomer($user);

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $request->amount,
            'currency' => 'inr',
            'customer' => $customerId,
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'user_id' => $user->id,
                'type' => 'one_time',
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'client_secret' => $intent->client_secret
        ]);

    } catch (\Stripe\Exception\ApiErrorException $e) {

        return response()->json([
            'status' => 'error',
            'msg' => 'Stripe error',
            'error' => $e->getMessage()
        ], 500);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 'error',
            'msg' => 'Server error',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
