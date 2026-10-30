<?php

/* namespace App\Jobs;

use Spatie\StripeWebhooks\ProcessStripeWebhookJob;

class ProcessStripeWebhook extends ProcessStripeWebhookJob
{
    public function handle()
    {
        \Log::info('CUSTOM STRIPE WEBHOOK FIRED', [
            'webhook_id' => $this->webhookCall->id,
            'payload' => $this->webhookCall->payload,
        ]);
    }
}
*/


namespace App\Jobs;

use App\Services\SendGridService;
use Spatie\StripeWebhooks\ProcessStripeWebhookJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
class ProcessStripeWebhook extends ProcessStripeWebhookJob
{
    public function handle()
    {
        $webhook = $this->webhookCall;
        $payload = $webhook->payload;
        Log::info('PROCESS STRIPE WEBHOOK JOB STARTED', [
            'id' => $this->webhookCall->id,
            'type' => $this->webhookCall->payload['type'] ?? null,
        ]);
        Log::info("JOB PAYLOAD DEBUG", [
            'payload' => $payload
        ]);

        $eventType = $payload['type'] ?? null;

// Classic Stripe event (sandbox)
        $object = $payload['data']['object'] ?? null;

// Stripe thin event (live)
        if (
            !$object &&
            ($payload['object'] ?? null) === 'v2.core.event'
        ) {
            $object = $payload['related_object'] ?? [];
        }

        Log::info('STRIPE OBJECT DEBUG', [
            'event_type' => $eventType,
            'object' => $object,
        ]);

        // 🔥 LOG: Webhook received
        Log::channel('single')->info("🔥 Stripe Webhook Received", [
            'event_type' => $eventType,
            'payload_id' => $payload['id'] ?? null,
        ]);

        // Build a clean record layout
        $processed = [
            'event_id' => $payload['id'] ?? null,
            'event_type' => $eventType,

            'object_type' => $object['object'] ?? null,

            'amount' => $object['amount_total']
                ?? $object['amount']
                    ?? null,

            'currency' => $object['currency'] ?? null,

            'customer' => $object['customer'] ?? null,

            'customer_details' => $object['customer_details'] ?? [],

            'payment_intent' => $object['payment_intent'] ?? null,

            'charge' => $object['charge'] ?? null,

            'status' => $object['payment_status']
                ?? $object['status']
                    ?? null,

            'metadata' => $object['metadata'] ?? [],

            'object' => $object,

            'raw' => $payload,
        ];

        $webhook->processed_payload = $processed;
        $webhook->exception = null;
        $webhook->save();

        // 🔥 LOG: Payload saved
        Log::channel('single')->info("💾 Webhook saved to DB", [
            'webhook_id' => $webhook->id,
        ]);

        // Process webhook application logic
        switch ($eventType) {

            case 'payment_intent.succeeded':
            case 'v1.payment_intent.succeeded':
                Log::info("⚡ payment_intent.succeeded fired");
                break;

            case 'checkout.session.completed':
            case 'v1.checkout.session.completed':

                Log::info('CHECKOUT CASE REACHED', [
                    'event_type' => $eventType,
                    'webhook_id' => $webhook->id,
                ]);

                if (($payload['object'] ?? null) === 'v2.core.event') {

                    Log::info('STRIPE CONFIG CHECK', [
                        'has_key' => !empty(config('services.stripe.secret')),
                    ]);

                    Stripe::setApiKey(config('services.stripe.secret'));

                    $sessionId = $payload['related_object']['id'] ?? null;

                    if (!$sessionId) {

                        Log::error('No checkout session ID found', [
                            'payload' => $payload,
                        ]);

                        break;
                    }

                    try {

                        Log::info('FETCHING CHECKOUT SESSION', [
                            'session_id' => $sessionId,
                        ]);

                        $session = Session::retrieve($sessionId);

                        $session = $session->toArray();

                        // Retrieve purchased products / line items
                        $lineItems = Session::allLineItems(
                            $sessionId,
                            ['limit' => 100]
                        );

                        $lineItems = $lineItems->toArray();

                        Log::info('STRIPE CHECKOUT LINE ITEMS', [
                            'session_id' => $sessionId,
                            'items' => $lineItems['data'] ?? [],
                        ]);

                        Log::info('CHECKOUT SESSION STRUCTURE', [
                            'keys' => array_keys($session),
                        ]);

                        Log::info('FULL STRIPE SESSION RETRIEVED', [
                            'session_id' => $session['id'] ?? null,
                            'email' => $session['customer_details']['email'] ?? null,
                        ]);

                        $object = $session;

                        Log::info('SAVING FULL SESSION TO DB', [
                            'session_id' => $session['id'] ?? null,
                        ]);

                        $processed = [
                            'event_id' => $payload['id'] ?? null,
                            'event_type' => $eventType,
                            'object_type' => 'checkout.session',

                            'amount' => $session['amount_total'] ?? null,
                            'currency' => $session['currency'] ?? null,

                            'customer' => $session['customer'] ?? null,
                            'customer_details' => $session['customer_details'] ?? [],

                            'payment_intent' => $session['payment_intent'] ?? null,

                            'status' => $session['payment_status'] ?? null,

                            'metadata' => $session['metadata'] ?? [],

                            // Full Checkout Session
                            'object' => $session,

                            // Purchased products
                            'line_items' => $lineItems['data'] ?? [],

                            // Original webhook
                            'raw' => $payload,
                        ];

                        $webhook->processed_payload = $processed;
                        $webhook->save();

                    } catch (\Throwable $e) {

                        Log::error('Failed retrieving Stripe session', [
                            'message' => $e->getMessage(),
                            'session_id' => $sessionId,
                            'line' => $e->getLine(),
                            'file' => $e->getFile(),
                        ]);

                        break;
                    }
                }

                $orderId = $object['id'] ?? null;

                $total = $object['amount_total']
                    ?? $object['amount']
                    ?? null;

                $customerDetails = $object['customer_details'] ?? [];

                $customerEmail = $customerDetails['email'] ?? null;

                $customerName = $customerDetails['name']
                    ?? 'Flow State Customer';

                $webhook->gross = $total;
                $webhook->fee = null;
                $webhook->net = null;

                $webhook->save();

                Log::info('Customer Info', [
                    'email' => $customerEmail,
                    'name' => $customerName,
                    'order_id' => $orderId,
                    'total' => $total,
                ]);

                break;




                break;

            case 'payout.created':
                Log::info("💰 payout.created — funds pending");

                // 🧠 FIX: Explicit assignment to bypass Mass Assignment block
                $webhook->payout_status = 'pending';
                $webhook->payout_at = null;
                $webhook->save();
                break;

            case 'payout.paid':
                Log::info("🏦 payout.paid — funds deposited to bank");

                $arrival = $object['arrival_date'] ?? null;

                // 🧠 FIX: Explicit assignment to bypass Mass Assignment block
                $webhook->payout_status = 'paid';
                $webhook->payout_at = $arrival ? Carbon::createFromTimestamp($arrival) : now();
                $webhook->save();
                break;

            case 'payout.failed':
                Log::warning("❌ payout.failed — payout failed");

                // 🧠 FIX: Explicit assignment to bypass Mass Assignment block
                $webhook->payout_status = 'failed';
                $webhook->payout_at = null;
                $webhook->save();
                break;
            case 'charge.succeeded':
            case 'v1.charge.succeeded':

                Log::info('💳 charge.succeeded fired');

                $charge = $object;

                $webhook->gross = $charge['amount'] ?? null;

                $webhook->card_brand =
                    $charge['payment_method_details']['card']['brand']
                    ?? null;

                $webhook->last4 =
                    $charge['payment_method_details']['card']['last4']
                    ?? null;

                $webhook->receipt_url =
                    $charge['receipt_url']
                    ?? null;

                $webhook->save();

                break;

            default:
                Log::warning("⚠️ Unhandled Stripe event type", [
                    'event_type' => $eventType,
                ]);
        }
    }
}
