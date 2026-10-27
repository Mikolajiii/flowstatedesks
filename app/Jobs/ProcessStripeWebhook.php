<?php

namespace App\Jobs;

use Spatie\StripeWebhooks\ProcessStripeWebhookJob;

class ProcessStripeWebhook extends ProcessStripeWebhookJob
{
    public function handle()
    {
        \Log::info('CUSTOM STRIPE WEBHOOK FIRED', [
            'payload' => $this->webhookCall->payload,
        ]);
    }
}
