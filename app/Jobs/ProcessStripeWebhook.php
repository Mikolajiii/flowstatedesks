<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\StripeWebhooks\Models\StripeWebhookCall;
use App\Models\Order;

class ProcessStripeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public StripeWebhookCall $webhookCall;

    public function __construct(StripeWebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        $eventType = $this->webhookCall->payload['type'];
        $object    = $this->webhookCall->payload['data']['object'];

        switch ($eventType) {

            case 'checkout.session.completed':
                $session = $object;

                Order::create([
                    'stripe_session_id' => $session['id'],
                    'customer_email'    => $session['customer_details']['email'] ?? null,
                    'product_name'      => $session['metadata']['product'] ?? 'Unknown',
                    'amount_total'      => $session['amount_total'],
                    'payment_status'    => 'paid',
                    'payout_status'     => 'pending',
                    'raw_payload'       => $session,
                ]);
                break;

            case 'payment_intent.succeeded':
                // You can update an existing order here if needed
                break;

            default:
                // Ignore or log other events
                break;
        }
    }
}
