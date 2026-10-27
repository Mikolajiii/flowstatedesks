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

class ProcessStripeWebhook extends ProcessStripeWebhookJob
{
    public function handle()
    {
        $webhook = $this->webhookCall;
        $payload = $webhook->payload;

        Log::info("JOB PAYLOAD DEBUG", [
            'payload' => $payload
        ]);

        $eventType = $payload['type'] ?? null;
        $object = $payload['data']['object'] ?? [];

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

            // 🧠 Fix: Fallback to checkout session properties if payment intent keys are missing
            'amount' => $object['amount'] ?? $object['amount_total'] ?? null,
            'currency' => $object['currency'] ?? null,
            'customer' => $object['customer'] ?? null,
            'payment_intent' => $object['payment_intent'] ?? null,
            'charge' => $object['charge'] ?? null,
            'status' => $object['status'] ?? $object['payment_status'] ?? null,

            'metadata' => $object['metadata'] ?? [],
            'raw' => $payload,
        ];

        // 🧠 FIX: Bypass Mass Assignment restrictions by updating properties directly
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
                Log::info("⚡ payment_intent.succeeded fired");
                break;

            case 'checkout.session.completed':
                Log::info("🎉 checkout.session.completed fired");

                $session = $object;
                $orderId = $session['id'] ?? null;
                $total = $session['amount_total'] ?? null;

                // Extract buyer info from Stripe JSON
                $customerDetails = $session['customer_details'] ?? [];
                $customerEmail = $customerDetails['email'] ?? null;
                $customerName = $customerDetails['name'] ?? 'Flow State Customer';

                Log::info("📧 Sending customer email...", [
                    'order_id' => $orderId,
                    'total' => $total,
                    'customer_email' => $customerEmail,
                    'customer_name' => $customerName,
                ]);

                // SendGrid dynamic template email
                $result = app(SendGridService::class)->sendTemplate(
                    'WELCOME_MESSAGE',
                    'michaelkondakow@gmail.com',
                    'Michael',
                    [
                        'message' => "You have a new order!",
                        'order_id' => $orderId,
                        'total' => $total,
                    ]
                );

                Log::info("📨 SendGrid result", [
                    'success' => $result,
                ]);

                Log::info("🟢 CHECKOUT BLOCK REACHED");
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

            default:
                Log::warning("⚠️ Unhandled Stripe event type", [
                    'event_type' => $eventType,
                ]);
        }
    }
}
