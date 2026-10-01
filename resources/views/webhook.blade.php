<?php

const SENDGRID_API_KEY = 'SENDGRID_API_KEY';
require 'vendor/autoload.php';
use App\Services\SendGridService;



\Stripe\Stripe::setApiKey(config('STRIPE_KEY'));
$endpoint_secret = \Stripe\Stripe::setApiKey(config('STRIPE_WEBHOOK_SECRET'));

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;

// Prevent warnings when accessing webhook manually
if (!$sig_header) {
    http_response_code(400);
    echo "Missing Stripe signature";
    exit();
}

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        $endpoint_secret
    );
} catch (\Exception $e) {
    http_response_code(400);
    exit();
}

if ($event->type === 'checkout.session.completed') {

    $session = $event->data->object;

    // Extract shipping info safely
    $shipping = $session->collected_information->shipping_details ?? null;
    $address  = $shipping->address ?? null;
// Fallback for older Stripe shipping format
    if (!$shipping && isset($session->shipping)) {
        $shipping = $session->shipping;
        $address  = $shipping->address ?? null;
    }

    $orderData = [
        'email'          => $session->customer_details->email ?? 'unknown',

        // Correct shipping fields
        'name'           => $shipping->name ?? 'unknown',
        'address_line1'  => $address->line1 ?? 'unknown',
        'address_line2'  => $address->line2 ?? '',
        'city'           => $address->city ?? 'unknown',
        'state'          => $address->state ?? 'unknown',
        'postal_code'    => $address->postal_code ?? 'unknown',
        'country'        => $address->country ?? 'unknown',

        // Product metadata
        'product'        => $session->metadata->product ?? 'unknown',
        'price_id'       => $session->metadata->price_id ?? 'unknown',

        // Payment info
        'amount'         => ($session->amount_total ?? 0) / 100,
        'session_id'     => $session->id,
        'payment_intent' => $session->payment_intent ?? 'unknown',
// NEW: Stripe checkout payment status
        'payment_status' => $session->payment_status ?? 'unknown',
        'timestamp'      => date('Y-m-d H:i:s')
    ];


    // Log order
    file_put_contents(__DIR__ . "/orders.log", json_encode($orderData) . PHP_EOL, FILE_APPEND);

    // Email notification
    $sendgrid = app(SendGridService::class);

    $success = $sendgrid->sendTemplate('PAYMENT_SUCCESS', "mikolajkondakow@gmail.com", "Flow State Desks",
        [
            'message' => "Payment completed. Session ID: {$session->id}",
            'bcc'     => "michaelkondakow@gmail.com",
        ]
    );

    return $success ? "Email sent successfully." : "SendGrid failed.";
}


