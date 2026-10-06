<?php

namespace App\Services;

use Spatie\WebhookClient\Models\WebhookCall;

class StripeWebhookHandler
{
    public function handleWebhook(WebhookCall $webhookCall)
    {
        $payload = $webhookCall->payload;

        return [
            'event' => trim($payload['type'], '"'),
            'customer' => $payload['data']['object']['customer'] ?? null,
            'amount' => $payload['data']['object']['amount'] ?? null,
            'currency' => $payload['data']['object']['currency'] ?? null,
        ];
    }
}

