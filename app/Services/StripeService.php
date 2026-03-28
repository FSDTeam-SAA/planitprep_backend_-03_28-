<?php

namespace App\Services;

use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;

use \Stripe\Stripe;
use \Stripe\Customer;

class StripeService
{
    
    public static function init()
    {
        $secret = config('services.stripe.secret');

        if (empty($secret)) {
            Log::error('Stripe secret key is missing in config.');
            throw new \Exception('Stripe secret key not configured.');
        }

        Stripe::setApiKey($secret);

        Log::info('Stripe initialized successfully.');
    }

    public static function getOrCreateCustomer($user)
    {
        if (!empty($user->stripe_customer_id)) {
            return $user->stripe_customer_id;
        }

        // Use email if available, otherwise fallback to phone or user ID
        $email = $user->email ?? ($user->phone ? $user->phone . '@example.com' : 'user_' . $user->id . '@example.com');
        $name = $user->name ?? $user->username ?? 'Unknown User';

        $customerData = [
            'name' => $name,
            'email' => $email,
        ];

        Log::info('Creating Stripe customer with data: ' . json_encode($customerData));

        try {
            // Use StripeClient with secret key
            $stripe = new StripeClient(config('services.stripe.secret'));

            $customer = $stripe->customers->create($customerData);

            $user->stripe_customer_id = $customer->id;
            $user->save();

            Log::info('Stripe customer created successfully: ' . $customer->id);

            return $customer->id;
        } catch (\Exception $e) {
            Log::error('Stripe customer creation failed: ' . $e->getMessage());
            throw new \Exception('Failed to create Stripe customer: ' . $e->getMessage());
        }
    }
}