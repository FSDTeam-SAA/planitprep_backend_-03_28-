<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StripeService;
use Stripe\Webhook;
use App\Models\StripePayment as Payment;
use App\Models\Subscription as UserSubscription;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        StripeService::init();

        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
        } catch (\Throwable $e) {
            Log::error('Stripe signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['status' => 'invalid'], 400);
        }

        try {

            switch ($event->type) {

                /* ==========================================================
                 | SUBSCRIPTION CREATED
                 ========================================================== */
                case 'customer.subscription.created':

                    $subscription = $event->data->object;

                    $userId = $subscription->metadata->user_id ?? null;

                    if (!$userId) {
                        Log::error('Missing user_id in subscription.created', [
                            'subscription_id' => $subscription->id
                        ]);
                        break;
                    }

                    UserSubscription::updateOrCreate(
                        ['stripe_subscription_id' => $subscription->id],
                        [
                            'user_id' => $userId,
                            'stripe_price_id' => $subscription->items->data[0]->price->id ?? null,
                            'status' => $subscription->status,
                            'is_subscribed' => 0,
                            'current_period_start' => $subscription->current_period_start
                                ? Carbon::createFromTimestamp($subscription->current_period_start)
                                : null,
                            'current_period_end' => $subscription->current_period_end
                                ? Carbon::createFromTimestamp($subscription->current_period_end)
                                : null,
                        ]
                    );

                    break;


                /* ==========================================================
                 |SUBSCRIPTION UPDATED (ACTIVATION + RENEWAL)
                 ========================================================== */
                case 'customer.subscription.updated':

                    $subscription = $event->data->object;

                    $userSub = UserSubscription::where(
                        'stripe_subscription_id',
                        $subscription->id
                    )->first();

                    if (!$userSub) {
                        Log::warning('Subscription not found in DB on update', [
                            'subscription_id' => $subscription->id
                        ]);
                        break;
                    }

                    $userSub->update([
                        'status' => $subscription->status,
                        'is_subscribed' => $subscription->status === 'active' ? 1 : 0,
                        'current_period_start' => $subscription->current_period_start
                            ? Carbon::createFromTimestamp($subscription->current_period_start)
                            : null,
                        'current_period_end' => $subscription->current_period_end
                            ? Carbon::createFromTimestamp($subscription->current_period_end)
                            : null,
                    ]);

                    break;


                /* ==========================================================
                 |  INVOICE PAYMENT SUCCEEDED (FIRST + RENEWAL)
                 ========================================================== */
                case 'invoice.payment_succeeded':

                     Log::warning('not come int his section name invoice.payment_succeeded', [
                            'invoice_id'
                        ]);

                    $invoice = $event->data->object;

                    if (!$invoice->subscription) {
                        Log::warning(' Not a subscription invoice', [
                            'invoice_id'
                        ]);
                        break; // Not a subscription invoice
                    }

                    $userSub = UserSubscription::where(
                        'stripe_subscription_id',
                        $invoice->subscription
                        
                    )->first();

                    if (!$userSub) {
                        Log::warning('Subscription not found for invoice', [
                            'invoice_id' => $invoice->id
                        ]);
                        break;
                    }
                    Log::info('User ID for payment insert', ['user_id' => $userSub->user_id]);

                    Payment::updateOrCreate(
                        ['stripe_invoice_id' => $invoice->id],
                        [
                            'user_id' => $userSub->user_id,
                            'stripe_payment_intent_id' => $invoice->payment_intent ?? null,
                            'amount' => $invoice->amount_paid / 100,
                            'currency' => $invoice->currency,
                            'status' => 'paid',
                            'type' => 'subscription',
                            'payment_method' => 'card',
                        ]
                    );

                    break;


                /* ==========================================================
                 | PAYMENT FAILED (RENEWAL FAILURE)
                 ========================================================== */
                case 'invoice.payment_failed':

                    $invoice = $event->data->object;

                    if (!$invoice->subscription) {
                        break;
                    }

                    $userSub = UserSubscription::where(
                        'stripe_subscription_id',
                        $invoice->subscription
                    )->first();

                    if ($userSub) {
                        $userSub->update([
                            'status' => 'past_due',
                            'is_subscribed' => 0,
                        ]);
                    }

                    break;


                /* ==========================================================
                 | SUBSCRIPTION CANCELED
                 ========================================================== */
                case 'customer.subscription.deleted':

                    UserSubscription::where(
                        'stripe_subscription_id',
                        $event->data->object->id
                    )->update([
                        'status' => 'canceled',
                        'is_subscribed' => 0,
                    ]);

                    break;
            }

        } catch (\Throwable $e) {

            Log::error('Stripe webhook processing failed', [
                'event_type' => $event->type ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}