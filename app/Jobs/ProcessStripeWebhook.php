<?php

namespace App\Jobs;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessStripeWebhook extends ProcessWebhookJob
{
    public function handle()
    {
        $webhook = $this->webhookCall;
        $payload = $webhook->payload;

        $eventType = $payload['type'] ?? null;
        $object    = $payload['data']['object'] ?? [];

        // Log event type
        \Log::info("Processing Stripe webhook: {$eventType}");

        // Build a clean, admin-friendly record
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
            'raw'           => $payload, // keep full JSON too
        ];

        // Save everything into your webhook table
        $webhook->update([
            'processed_payload' => $processed,
            'exception' => null,
        ]);

        // Handle specific Stripe events
        switch ($eventType) {
            case 'payment_intent.succeeded':
                // your logic here
                break;

            case 'charge.succeeded':
                // your logic here
                break;

            case 'checkout.session.completed':
                // your logic here
                break;
        }
    }
}
