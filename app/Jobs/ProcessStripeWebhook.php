<?php

namespace App\Jobs;

use App\Services\SendGridService;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessStripeWebhook extends ProcessWebhookJob
{
    public function handle()
    {





        $webhook = $this->webhookCall;
        $payload = $webhook->payload;

        \Log::info("JOB PAYLOAD DEBUG", [
            'payload' => $payload
        ]);

        $eventType = $payload['type'] ?? null;
        $object    = $payload['data']['object'] ?? [];

        // 🔥 LOG: Webhook received
        \Log::channel('single')->info("🔥 Stripe Webhook Received", [
            'event_type' => $eventType,
            'payload_id' => $payload['id'] ?? null,
        ]);

        // Build a clean record
        $processed = [
            'event_id'      => $payload['id'] ?? null,
            'event_type'    => $eventType,
            'object_type'   => $object['object'] ?? null,
            'amount'        => $object['amount'] ?? null,
            'currency'      => $object['currency'] ?? null,
            'customer'      => $object['customer'] ?? null,
            'payment_intent'=> $object['payment_intent'] ?? null,
            'charge'        => $object['charge'] ?? null,
            'status'        => $object['status'] ?? null,
            'metadata'      => $object['metadata'] ?? [],
            'raw'           => $payload,
        ];

        // Save into DB
        $webhook->update([
            'processed_payload' => $processed,
            'exception' => null,
        ]);

        // 🔥 LOG: Payload saved
        \Log::channel('single')->info("💾 Webhook saved to DB", [
            'webhook_id' => $webhook->id,
        ]);

        // Handle events
        switch ($eventType) {

            case 'payment_intent.succeeded':
                \Log::info("⚡ payment_intent.succeeded fired");
                break;

            case 'checkout.session.completed':

                \Log::info("🎉 checkout.session.completed fired");

                $session = $object;

                $orderId = $session['id'] ?? null;
                $total   = $session['amount_total'] ?? null;

                // ⭐ Extract buyer info from Stripe JSON
                $customerDetails = $session['customer_details'] ?? [];

                $customerEmail = $customerDetails['email'] ?? null;
                $customerName  = $customerDetails['name'] ?? 'Flow State Customer';


                \Log::info("📧 Sending customer email...", [
                    'order_id' => $orderId,
                    'total' => $total,
                    'customer_email' => $customerEmail,
                    'customer_name' => $customerName,
                ]);

                // ⭐ SendGrid dynamic template email
                $result = app(SendGridService::class)->sendTemplate(
                    'WELCOME_MESSAGE',          // your template key
                    'mikadoneng@gmail.com',     // your email
                    'Mickey',                   // name
                    [
                        'message' => "You have a new order!",
                        'order_id' => $orderId,
                        'total'    => $total,
                    ]
                );

                \Log::info("📨 SendGrid result", [
                    'success' => $result,
                 
                ]);
                \Log::info("🟢 CHECKOUT BLOCK REACHED");

                break;



            default:
                \Log::warning("⚠️ Unhandled Stripe event type", [
                    'event_type' => $eventType,
                ]);
        }
    }
}
