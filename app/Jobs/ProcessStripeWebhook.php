<?php

namespace App\Jobs;

use Spatie\StripeWebhooks\ProcessStripeWebhookJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Charge;
use Stripe\PaymentIntent;

class ProcessStripeWebhook extends ProcessStripeWebhookJob
{
    public function handle()
    {
        $webhook = $this->webhookCall;
        $payload = $webhook->payload;

        $eventType = $payload['type'] ?? null;
        $isThinEvent = ($payload['object'] ?? null) === 'v2.core.event';

        /*
        |--------------------------------------------------------------------------
        | 1. Log the complete incoming Stripe event
        |--------------------------------------------------------------------------
        */

        Log::info('STRIPE WEBHOOK - COMPLETE RAW JSON', [
            'webhook_id' => $webhook->id,
            'json' => json_encode(
                $payload,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            ),
        ]);

        Log::info('PROCESS STRIPE WEBHOOK JOB STARTED', [
            'webhook_id' => $webhook->id,
            'event_id' => $payload['id'] ?? null,
            'event_type' => $eventType,
            'event_format' => $isThinEvent ? 'thin' : 'snapshot',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Get the Stripe object
        |--------------------------------------------------------------------------
        |
        | Snapshot:
        |   $payload['data']['object']
        |
        | Thin:
        |   Retrieve the object using related_object.id
        |
        */

        $object = $payload['data']['object'] ?? null;

        if ($isThinEvent) {

            $relatedObject = $payload['related_object'] ?? [];

            $resourceId = $relatedObject['id'] ?? null;
            $resourceType = $relatedObject['type'] ?? null;

            if (!$resourceId) {
                Log::error('STRIPE THIN EVENT - MISSING RESOURCE ID', [
                    'payload' => $payload,
                ]);

                throw new \RuntimeException(
                    'Stripe thin event is missing related_object.id'
                );
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            Log::info('STRIPE THIN EVENT - RETRIEVING RESOURCE', [
                'resource_id' => $resourceId,
                'resource_type' => $resourceType,
            ]);

            try {

                switch ($resourceType) {

                    case 'checkout.session':
                        $object = Session::retrieve($resourceId)->toArray();
                        break;

                    case 'charge':
                        $object = Charge::retrieve($resourceId)->toArray();
                        break;

                    case 'payment_intent':
                        $object = PaymentIntent::retrieve($resourceId)->toArray();
                        break;

                    default:
                        $object = $relatedObject;

                        Log::warning('UNSUPPORTED THIN EVENT RESOURCE', [
                            'resource_type' => $resourceType,
                            'resource_id' => $resourceId,
                        ]);
                }

            } catch (\Throwable $e) {

                Log::error('STRIPE RESOURCE RETRIEVAL FAILED', [
                    'resource_id' => $resourceId,
                    'resource_type' => $resourceType,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                // Allow Laravel's queue to retry the job.
                throw $e;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Log the complete retrieved Stripe object
        |--------------------------------------------------------------------------
        */

        Log::info('STRIPE COMPLETE OBJECT JSON', [
            'event_type' => $eventType,
            'json' => json_encode(
                $object,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. Build the initial processed payload
        |--------------------------------------------------------------------------
        */

        $processed = [
            'event_id' => $payload['id'] ?? null,
            'event_type' => $eventType,
            'event_format' => $isThinEvent ? 'thin' : 'snapshot',

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

        /*
        |--------------------------------------------------------------------------
        | 5. Process event types
        |--------------------------------------------------------------------------
        */

        switch ($eventType) {

            /*
            |--------------------------------------------------------------------------
            | PAYMENT INTENT
            |--------------------------------------------------------------------------
            */

            case 'payment_intent.succeeded':
            case 'v1.payment_intent.succeeded':

                Log::info('PAYMENT INTENT SUCCEEDED', [
                    'object' => $object,
                ]);

                break;


            /*
            |--------------------------------------------------------------------------
            | CHECKOUT SESSION COMPLETED
            |--------------------------------------------------------------------------
            */

            case 'checkout.session.completed':
            case 'v1.checkout.session.completed':

                Log::info('CHECKOUT SESSION COMPLETED', [
                    'event_type' => $eventType,
                    'event_format' => $isThinEvent ? 'thin' : 'snapshot',
                ]);

                $sessionId = $object['id'] ?? null;

                if (!$sessionId) {
                    throw new \RuntimeException(
                        'Checkout Session ID is missing'
                    );
                }

                /*
                | Retrieve purchased products for BOTH event formats.
                */

                try {

                    Stripe::setApiKey(config('services.stripe.secret'));

                    $lineItems = Session::allLineItems(
                        $sessionId,
                        ['limit' => 100]
                    )->toArray();

                } catch (\Throwable $e) {

                    Log::error('CHECKOUT LINE ITEMS RETRIEVAL FAILED', [
                        'session_id' => $sessionId,
                        'message' => $e->getMessage(),
                    ]);

                    throw $e;
                }

                /*
                | Build complete Checkout Session record.
                */

                $processed = [
                    'event_id' => $payload['id'] ?? null,
                    'event_type' => $eventType,
                    'event_format' => $isThinEvent ? 'thin' : 'snapshot',

                    'object_type' => 'checkout.session',

                    'amount' => $object['amount_total'] ?? null,

                    'currency' => $object['currency'] ?? null,

                    'customer' => $object['customer'] ?? null,

                    'customer_details' => $object['customer_details'] ?? [],

                    'payment_intent' => $object['payment_intent'] ?? null,

                    'status' => $object['payment_status'] ?? null,

                    'metadata' => $object['metadata'] ?? [],

                    // Complete Checkout Session
                    'object' => $object,

                    // Purchased products
                    'line_items' => $lineItems['data'] ?? [],

                    // Original Stripe webhook JSON
                    'raw' => $payload,
                ];

                /*
                | Log the COMPLETE processed JSON.
                */

                Log::info('STRIPE CHECKOUT - FULL JSON DUMP', [
                    'json' => json_encode(
                        $processed,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
                    ),
                ]);

                /*
                | Save complete information to database.
                */

                $webhook->processed_payload = $processed;

                $webhook->gross = $object['amount_total'] ?? null;
                $webhook->fee = null;
                $webhook->net = null;

                $webhook->exception = null;

                $webhook->save();

                /*
                | Customer information.
                */

                $customerDetails = $object['customer_details'] ?? [];

                Log::info('STRIPE CHECKOUT CUSTOMER INFORMATION', [
                    'session_id' => $sessionId,
                    'email' => $customerDetails['email'] ?? null,
                    'name' => $customerDetails['name'] ?? null,
                    'total' => $object['amount_total'] ?? null,
                    'currency' => $object['currency'] ?? null,
                    'line_items' => $lineItems['data'] ?? [],
                ]);

                break;


            /*
            |--------------------------------------------------------------------------
            | PAYOUT EVENTS
            |--------------------------------------------------------------------------
            */

            case 'payout.created':

                Log::info('PAYOUT CREATED');

                $webhook->payout_status = 'pending';
                $webhook->payout_at = null;
                $webhook->save();

                break;


            case 'payout.paid':

                Log::info('PAYOUT PAID');

                $arrival = $object['arrival_date'] ?? null;

                $webhook->payout_status = 'paid';

                $webhook->payout_at = $arrival
                    ? Carbon::createFromTimestamp($arrival)
                    : now();

                $webhook->save();

                break;


            case 'payout.failed':

                Log::warning('PAYOUT FAILED');

                $webhook->payout_status = 'failed';
                $webhook->payout_at = null;
                $webhook->save();

                break;


            /*
            |--------------------------------------------------------------------------
            | CHARGE SUCCEEDED
            |--------------------------------------------------------------------------
            */

            case 'charge.succeeded':
            case 'v1.charge.succeeded':

                Log::info('CHARGE SUCCEEDED', [
                    'charge' => $object,
                ]);

                $webhook->gross = $object['amount'] ?? null;

                $webhook->card_brand =
                    $object['payment_method_details']['card']['brand']
                    ?? null;

                $webhook->last4 =
                    $object['payment_method_details']['card']['last4']
                    ?? null;

                $webhook->receipt_url =
                    $object['receipt_url']
                    ?? null;

                $webhook->save();

                break;


            /*
            |--------------------------------------------------------------------------
            | REFUNDS
            |--------------------------------------------------------------------------
            */

            case 'charge.refunded':
            case 'v1.charge.refunded':

                Log::info('CHARGE REFUNDED', [
                    'charge' => $object,
                ]);

                break;


            /*
            |--------------------------------------------------------------------------
            | DISPUTES
            |--------------------------------------------------------------------------
            */

            case 'charge.dispute.created':
            case 'v1.charge.dispute.created':

                Log::warning('CHARGE DISPUTE CREATED', [
                    'object' => $object,
                ]);

                break;


            case 'charge.dispute.closed':
            case 'v1.charge.dispute.closed':

                Log::info('CHARGE DISPUTE CLOSED', [
                    'object' => $object,
                ]);

                break;


            /*
            |--------------------------------------------------------------------------
            | UNHANDLED EVENTS
            |--------------------------------------------------------------------------
            */

            default:

                Log::warning('UNHANDLED STRIPE EVENT', [
                    'event_type' => $eventType,
                    'event_format' => $isThinEvent ? 'thin' : 'snapshot',
                    'object' => $object,
                ]);

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Confirm job completed
        |--------------------------------------------------------------------------
        */

        Log::info('STRIPE WEBHOOK JOB COMPLETED', [
            'webhook_id' => $webhook->id,
            'event_type' => $eventType,
        ]);
    }
}
