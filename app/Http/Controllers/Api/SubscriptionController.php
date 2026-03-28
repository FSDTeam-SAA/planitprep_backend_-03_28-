<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    public function create(Request $request)
    {
 
        $request->validate([
            'price_id' => 'required|string',
        ]);

        StripeService::init();

        $user = $request->user();

        // Create or get Stripe customer
        $customerId = StripeService::getOrCreateCustomer($user);

        // Create Checkout Session for SUBSCRIPTION
        $session = Session::create([
            'mode' => 'subscription',
            'customer' => $customerId,
        
            'line_items' => [[
                'price' => $request->price_id,
                'quantity' => 1,
            ]],
        
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ],
        
            // 'success_url' => 'https://planit-prep-web.vercel.app/payment-success.html?session_id={CHECKOUT_SESSION_ID}',
            'success_url' => 'https://planit-prep-web.vercel.app/payment',
            'cancel_url' => 'https://planit-prep-web.vercel.app/payment-cancel.html',
        ]);


        return response()->json([
            'checkout_url' => $session->url,
            'session_id'   => $session->id,
        ]);


        
    }

    public function createSubscription(Request $request)
    {
        try {

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 401);
            }

            $request->validate([
                'price_id' => 'required|string',
            ]);

            

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $customerId = \App\Services\StripeService::getOrCreateCustomer($user);
      
            // Create subscription (Flexible mode)
            $subscription = $stripe->subscriptions->create([
                'customer' => $customerId,
                'items' => [
                    ['price' => $request->price_id],
                ],
                'metadata' => [
                      'user_id' => $user->id, // <-- Attach user_id here
                  ],
                'billing_mode' => [
                    'type' => 'flexible',
                    'flexible' => [
                        'proration_discounts' => 'itemized',
                    ],
                ],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                ],
                'expand' => [
                    'latest_invoice.confirmation_secret',
                ],
            ]);


            $invoice = $subscription->latest_invoice;

            if (empty($invoice->confirmation_secret)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Confirmation secret not generated.',
                    'subscription_status' => $subscription->status,
                ], 400);
            }

         
            return response()->json([
                'status' => 'success',
                'client_secret' => $invoice->confirmation_secret->client_secret,
                'subscription_id' => $subscription->id,
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Stripe error',
                'error' => $e->getMessage(),
            ], 500);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
