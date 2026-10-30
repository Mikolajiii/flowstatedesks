<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeDashboardController extends Controller
{
    private const ORDER_EVENT_TYPES = [
        'checkout.session.completed',
        'v1.checkout.session.completed',
    ];

    private const TRANSACTION_EVENT_TYPES = [
        'payout.paid',
        'payout.created',
        'balance.available',

        'charge.refunded',
        'v1.charge.refunded',

        'charge.updated',
        'v1.charge.updated',

        'charge.succeeded',
        'v1.charge.succeeded',

        'checkout.session.completed',
        'v1.checkout.session.completed',
    ];

    private const PER_PAGE = 25;


    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

    public function orders()
    {
        $orders = $this->webhookCallsOfType(self::ORDER_EVENT_TYPES)
            ->orderBy('created_at', 'desc')
            ->paginate(self::PER_PAGE);

        foreach ($orders as $order) {

            // Get the processed webhook structure
            $processed = $this->processedPayload($order);

            // Get the actual Stripe Checkout Session
            $obj = $this->stripeObject($order);

            $metadata = $obj['metadata'] ?? [];

            // Product information
            $order->product = $metadata['product'] ?? null;

            $order->description = $metadata['description'] ?? null;

            $order->price_id = $metadata['price_id'] ?? null;


            // Cart items - retrieved from Stripe Checkout
            $items = $processed['line_items'] ?? [];

            // Fallback for older webhook records
            if (empty($items) && !empty($metadata['items'])) {

                $items = is_string($metadata['items'])
                    ? json_decode($metadata['items'], true)
                    : $metadata['items'];
            }

            $order->items = is_array($items)
                ? $items
                : [];


            // Customer information
            $customerDetails = $obj['customer_details'] ?? [];

            $order->buyer_name = $this->firstFilled(
                $customerDetails['name'] ?? null,
                $obj['billing_details']['name'] ?? null,
                $obj['shipping']['name'] ?? null
            );

            $order->buyer_email = $this->firstFilled(
                $customerDetails['email'] ?? null,
                $obj['customer_email'] ?? null,
                $obj['billing_details']['email'] ?? null,
                $obj['receipt_email'] ?? null
            );


            // Amount (Stripe stores amounts in cents)
            $order->amount = $obj['amount_total']
                ?? $obj['amount']
                ?? $processed['amount']
                ?? null;


            // Currency
            $order->currency = strtoupper(
                $obj['currency']
                ?? $processed['currency']
                ?? ''
            );


            // Payment status
            $order->status = $obj['payment_status']
                ?? $obj['status']
                ?? $processed['status']
                ?? null;


            // Additional useful identifiers
            $order->stripe_session_id = $obj['id'] ?? null;

            $order->payment_intent = $obj['payment_intent'] ?? null;
        }

        return view('admin.stripe.orders.index', compact('orders'));
    }


    /*
    |--------------------------------------------------------------------------
    | ORDER DETAILS
    |--------------------------------------------------------------------------
    */

    public function orderShow($id)
    {
        $hook = $this->findHook($id);

        return view('admin.stripe.orders.show', compact('hook'));
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSACTIONS
    |--------------------------------------------------------------------------
    */

    public function transactions()
    {
        $transactions = $this->webhookCallsOfType(self::TRANSACTION_EVENT_TYPES)
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE);

        foreach ($transactions as $txn) {

            $processed = $this->processedPayload($txn);

            $obj = $this->stripeObject($txn);


            // Customer information
            $txn->buyer_name = $this->firstFilled(
                $obj['billing_details']['name'] ?? null,
                $obj['shipping']['name'] ?? null,
                $obj['customer_details']['name'] ?? null
            );

            $txn->buyer_email = $this->firstFilled(
                $obj['billing_details']['email'] ?? null,
                $obj['receipt_email'] ?? null,
                $obj['customer_details']['email'] ?? null
            );


            // Transaction status
            $txn->display_status = match ($obj['object'] ?? null) {

                'checkout.session' => $obj['payment_status'] ?? null,

                'charge' => $obj['status'] ?? null,

                'payment_intent' => $obj['status'] ?? null,

                'payout' => $obj['status'] ?? null,

                default => $processed['status'] ?? null,
            };


            // Amount and currency
            $txn->display_amount = $obj['amount_total']
                ?? $obj['amount']
                ?? $processed['amount']
                ?? null;

            $txn->display_currency = strtoupper(
                $obj['currency']
                ?? $processed['currency']
                ?? ''
            );
        }

        return view('admin.stripe.transactions.index', compact('transactions'));
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION DETAILS
    |--------------------------------------------------------------------------
    */

    public function transactionShow($id)
    {
        $hook = $this->findHook($id);

        $payload = $hook->payload ?? [];

        $hook->event_type = $payload['type'] ?? null;

        $hook->gross_amount = $hook->gross !== null
            ? $hook->gross / 100
            : null;

        $hook->fee_amount = $hook->fee !== null
            ? $hook->fee / 100
            : null;

        $hook->net_amount = $hook->net !== null
            ? $hook->net / 100
            : null;

        return view('admin.stripe.transactions.show', compact('hook'));
    }


    /*
    |--------------------------------------------------------------------------
    | STRIPE SYNC
    |--------------------------------------------------------------------------
    */

    public function sync()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {

            foreach (\Stripe\Charge::all(['limit' => 20])->data as $charge) {

                $this->storeStripeEvent('charge.succeeded', $charge);
            }


            foreach (\Stripe\Payout::all(['limit' => 20])->data as $payout) {

                $this->storeStripeEvent('payout.paid', $payout);
            }


            foreach (\Stripe\Refund::all(['limit' => 20])->data as $refund) {

                $this->storeStripeEvent('charge.refunded', $refund);
            }

        } catch (ApiErrorException $e) {

            Log::error('Stripe sync failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.stripe.transactions')
                ->with('error', 'Stripe sync failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.stripe.transactions')
            ->with('success', 'Stripe sync completed successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | STORE STRIPE EVENTS
    |--------------------------------------------------------------------------
    */

    protected function storeStripeEvent(string $type, $object): void
    {
        if (
            DB::table('webhook_calls')
                ->where('stripe_id', $object->id)
                ->exists()
        ) {
            return;
        }

        $gross = null;
        $fee = null;
        $net = null;

        $cardBrand = null;
        $last4 = null;
        $receiptUrl = null;


        if (
            $type === 'charge.succeeded'
            && ($object->object ?? null) === 'charge'
        ) {

            $gross = $object->amount ?? null;

            $receiptUrl = $object->receipt_url ?? null;

            $cardBrand = $object->payment_method_details->card->brand ?? null;

            $last4 = $object->payment_method_details->card->last4 ?? null;


            if (!empty($object->balance_transaction)) {

                try {

                    $balanceTxn = \Stripe\BalanceTransaction::retrieve(
                        $object->balance_transaction
                    );

                    $fee = $balanceTxn->fee ?? null;

                    $net = $balanceTxn->net ?? null;

                } catch (\Exception $e) {

                    Log::warning('Unable to retrieve balance transaction', [
                        'charge_id' => $object->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }


        DB::table('webhook_calls')->insert([

            'name' => $type,

            'stripe_id' => $object->id,

            'gross' => $gross,
            'fee' => $fee,
            'net' => $net,

            'card_brand' => $cardBrand,
            'last4' => $last4,
            'receipt_url' => $receiptUrl,

            'payload' => json_encode([
                'type' => $type,
                'data' => [
                    'object' => $object,
                ],
            ]),

            'processed_payload' => json_encode([]),

            'headers' => json_encode([]),

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | WEBHOOK QUERY
    |--------------------------------------------------------------------------
    */

    private function webhookCallsOfType(array $types)
    {
        $placeholders = implode(', ', array_fill(0, count($types), '?'));

        return DB::table('webhook_calls')
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) IN ($placeholders)",
                $types
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESSED PAYLOAD
    |--------------------------------------------------------------------------
    |
    | Handles JSON strings and already-decoded arrays.
    |
    */

    private function processedPayload(object $row): array
    {
        $processed = $row->processed_payload ?? null;

        if (is_string($processed)) {
            $processed = json_decode($processed, true);
        }

        return is_array($processed) ? $processed : [];
    }


    /*
    |--------------------------------------------------------------------------
    | FIND WEBHOOK
    |--------------------------------------------------------------------------
    */

    private function findHook($id): object
    {
        $hook = DB::table('webhook_calls')
            ->where('id', $id)
            ->first();

        abort_if(!$hook, 404);

        foreach (['payload', 'processed_payload', 'headers', 'attachments'] as $column) {

            $value = $hook->{$column} ?? null;

            if (is_string($value)) {
                $hook->{$column} = json_decode($value, true);
            }
        }

        return $hook;
    }


    /*
    |--------------------------------------------------------------------------
    | GET ACTUAL STRIPE OBJECT
    |--------------------------------------------------------------------------
    |
    | New structure:
    | processed_payload['object']
    |
    | Older structure:
    | processed_payload directly contains the session/object.
    |
    | Original webhook fallback:
    | payload['data']['object']
    |
    */

    private function stripeObject(object $row): array
    {
        $processed = $this->processedPayload($row);


        // New processed webhook structure
        if (isset($processed['object']) && is_array($processed['object'])) {

            return $processed['object'];
        }


        // Older processed structure containing the original raw webhook
        if (isset($processed['raw']) && is_array($processed['raw'])) {

            $raw = $processed['raw'];

            if (isset($raw['data']['object'])) {
                return $raw['data']['object'];
            }

            if (isset($raw['related_object'])) {
                return $raw['related_object'];
            }
        }


        // Older records where the full Checkout Session was saved directly
        if (isset($processed['id']) && isset($processed['object'])) {

            return $processed;
        }


        // Original webhook payload
        $payload = $row->payload ?? [];

        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }

        if (!is_array($payload)) {
            return [];
        }


        // Classic Stripe event
        if (isset($payload['data']['object'])) {

            return $payload['data']['object'];
        }


        // Stripe thin event
        if (isset($payload['related_object'])) {

            return $payload['related_object'];
        }


        return [];
    }


    /*
    |--------------------------------------------------------------------------
    | FIRST FILLED VALUE
    |--------------------------------------------------------------------------
    */

    private function firstFilled(mixed ...$values): mixed
    {
        foreach ($values as $value) {

            if ($value !== null && $value !== '') {

                return $value;
            }
        }

        return null;
    }
}
