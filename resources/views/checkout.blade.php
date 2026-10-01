<?php
/* add this in ploi
STRIPE_SECRET_KEY=sk_test_123456
STRIPE_WEBHOOK_SECRET=whsec_ABCDEF
in stripe dashboard add https://flowstatedesks.com/webhook.php

SENDGRID_API_KEY=SG.xxxxxx
*/
require 'vendor/autoload.php';


\Stripe\Stripe::setApiKey(config('stripe.secret'));



// Map your products to Stripe Price IDs in stripe dashboard

$prices = [
    'pro'     => 'price_1U31CgQnlXfHzKD34f26yJ9v',
    'mini'    => 'price_1U31WMQnlXfHzKD34xJQdr5u',
    'sidecar' => 'price_1U31Y8QnlXfHzKD37qgJrffc'
];

$product = isset($_POST['product']) ? $_POST['product'] : null;

if (!isset($prices[$product])) {
    die("Invalid product.");
}

$session = \Stripe\Checkout\Session::create([
    'mode' => 'payment',

    // Force Stripe to collect customer + shipping
    'customer_creation' => 'always',

    // Force billing address (helps shipping appear)
    'billing_address_collection' => 'required',

    // Force shipping address
    'shipping_address_collection' => [
        'allowed_countries' => ['CA'],
    ],

    // Metadata for webhook
    'metadata' => [
        'product'  => $product,
        'price_id' => $prices[$product],
    ],

    'line_items' => [[
        'price' => $prices[$product],
        'quantity' => 1,
    ]],

    'success_url' => 'http://localhost/FlowDesk/success.html',
    'cancel_url'  => 'http://localhost/FlowDesk/cancel.html',
]);




header("Location: " . $session->url);
exit;
