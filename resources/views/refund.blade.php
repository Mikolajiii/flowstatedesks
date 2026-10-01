<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

// Get POST data
$pi = $_POST['payment_intent'];
$amount = $_POST['amount']; // optional

// Build refund data
$data = ['payment_intent' => $pi];

if (!empty($amount)) {
    // Convert dollars → cents
    $data['amount'] = intval($amount * 100);
}

// Process refund
$refund = \Stripe\Refund::create($data);

// ------------------------------
// LOG THE REFUND
// ------------------------------
$logFile = 'refunds.log';

$entry = [
    'payment_intent' => $pi,
    'amount' => !empty($amount) ? intval($amount * 100) : $refund->amount,
    'timestamp' => date('Y-m-d H:i:s')
];

file_put_contents($logFile, json_encode($entry) . PHP_EOL, FILE_APPEND);

// ------------------------------
// CONFIRMATION
// ------------------------------
echo "<h2>Refund processed successfully.</h2>";
echo "<p>Payment Intent: $pi</p>";
echo "<p>Amount Refunded: $" . number_format($entry['amount'] / 100, 2) . "</p>";
echo "<p><a href='orders.php?key=" . getenv('orders_secret') . "'>Back to Orders</a></p>";
