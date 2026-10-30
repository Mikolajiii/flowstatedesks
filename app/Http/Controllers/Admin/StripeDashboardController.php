<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Payout;
use Stripe\BalanceTransaction;
use Throwable;

class StripeDashboardController extends Controller
{
    private const ORDER_EVENT_TYPES = [
        'checkout.session.completed',
        'v1.checkout.session.completed',
    ];

    private const TRANSACTION_EVENT_TYPES = [
        'payout.paid',
        'payout.created',
        'payout.failed',
        'payout.canceled',

        'charge.refunded',
        'v1.charge.refunded',

        'charge.updated',
        'v1.charge.updated',

        'charge.succeeded',
        'v1.charge.succeeded',
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

            $processed = $this->processedPayload($order);
            $obj = $this->stripeObject($order);

            $metadata = $obj['metadata'] ?? [];

            $order->product = $metadata['product'] ?? null;
            $order->description = $metadata['description'] ?? null;
            $order->price_id = $metadata['price_id'] ?? null;

            // Cart items
            $items = $processed['line_items'] ?? [];

            if (empty($items) && !empty($metadata['items'])) {

                $items = is_string($metadata['items'])
                    ? json_decode($metadata['items'], true)
                    : $metadata['items'];
            }

            $order->items = is_array($items) ? $items : [];

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

            // Amount in cents
            $order->amount = $obj['amount_total']
                ?? $obj['amount']
                ?? $processed['amount']
                ?? null;

            $order->currency = strtoupper(
                $obj['currency']
                ?? $processed['currency']
                ?? ''
            );

            $order->status = $obj['payment_status']
                ?? $obj['status']
                ?? $processed['status']
                ?? null;

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
        $transactions = $this->webhookCallsOfType(
            self::TRANSACTION_EVENT_TYPES
        )
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE);

        foreach ($transactions as $txn) {

            $processed = $this->processedPayload($txn);
            $obj = $this->stripeObject($txn);

            // Customer information
            $txn->buyer_name = $this->firstFilled(
                $obj['billing_details']['name'] ?? null,
                $obj['customer_details']['name'] ?? null,
                $obj['shipping']['name'] ?? null
            );

            $txn->buyer_email = $this->firstFilled(
                $obj['billing_details']['email'] ?? null,
                $obj['customer_details']['email'] ?? null,
                $obj['receipt_email'] ?? null
            );

            // Transaction status
            $txn->display_status = match ($obj['object'] ?? null) {

                'charge' => $this->chargeDisplayStatus($obj),

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

            $createdAfter = strtotime('-30 days');

            /*
             * Import charges.
             *
             * Pagination ensures we don't stop at the first 100.
             */

            $params = [
                'limit' => 100,
                'created' => [
                    'gte' => $createdAfter,
                ],
            ];

            do {

                $charges = Charge::all($params);

                foreach ($charges->data as $charge) {

                    $type = ($charge->amount_refunded ?? 0) > 0
                        ? 'charge.refunded'
                        : 'charge.succeeded';

                    $this->storeStripeEvent($type, $charge);
                }

                $lastCharge = end($charges->data);

                $hasMore = $charges->has_more && $lastCharge;

                if ($hasMore) {
                    $params['starting_after'] = $lastCharge->id;
                }

            } while ($hasMore);


            /*
             * Import payouts.
             */

            $params = [
                'limit' => 100,
                'created' => [
                    'gte' => $createdAfter,
                ],
            ];

            do {

                $payouts = Payout::all($params);

                foreach ($payouts->data as $payout) {

                    $type = match ($payout->status ?? null) {

                        'paid' => 'payout.paid',

                        'pending', 'in_transit' => 'payout.created',

                        'failed' => 'payout.failed',

                        'canceled' => 'payout.canceled',

                        default => 'payout.created',
                    };

                    $this->storeStripeEvent($type, $payout);
                }

                $lastPayout = end($payouts->data);

                $hasMore = $payouts->has_more && $lastPayout;

                if ($hasMore) {
                    $params['starting_after'] = $lastPayout->id;
                }

            } while ($hasMore);


        } catch (ApiErrorException $e) {

            Log::error('Stripe sync failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.stripe.transactions')
                ->with('error', 'Stripe sync failed: ' . $e->getMessage());

        } catch (Throwable $e) {

            Log::error('Stripe sync processing failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.stripe.transactions')
                ->with('error', 'Stripe sync processing failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.stripe.transactions')
            ->with('success', 'Stripe sync completed successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | STORE OR UPDATE STRIPE EVENT
    |--------------------------------------------------------------------------
    |
    | Important:
    | Existing records are updated rather than skipped.
    | This allows previously imported transactions to receive fee/net values.
    |
    */

    protected function storeStripeEvent(string $type, $object): void
    {
        $object = $object->toArray();

        $stripeId = $object['id'] ?? null;

        if (!$stripeId) {
            return;
        }

        $gross = null;
        $fee = null;
        $net = null;

        $cardBrand = null;
        $last4 = null;
        $receiptUrl = null;

        /*
         * Charge information
         */

        if (
            in_array($type, ['charge.succeeded', 'charge.refunded'], true)
            && ($object['object'] ?? null) === 'charge'
        ) {

            $gross = $object['amount'] ?? null;

            $receiptUrl = $object['receipt_url'] ?? null;

            $cardBrand = $object['payment_method_details']['card']['brand']
                ?? null;

            $last4 = $object['payment_method_details']['card']['last4']
                ?? null;

            /*
             * Retrieve actual Stripe processing fee and net amount.
             */

            $balanceTransactionId = $object['balance_transaction'] ?? null;

            if (is_array($balanceTransactionId)) {
                $balanceTransactionId = $balanceTransactionId['id'] ?? null;
            }

            if (!empty($balanceTransactionId)) {

                try {

                    $balanceTxn = BalanceTransaction::retrieve(
                        $balanceTransactionId
                    );

                    $fee = $balanceTxn->fee ?? null;
                    $net = $balanceTxn->net ?? null;

                } catch (Throwable $e) {

                    Log::warning('Unable to retrieve Stripe balance transaction', [
                        'charge_id' => $stripeId,
                        'balance_transaction_id' => $balanceTransactionId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }


        /*
         * Preserve Stripe's original creation date.
         */

        $createdAt = !empty($object['created'])
            ? Carbon::createFromTimestamp($object['created'])
            : now();


        /*
         * Save original Stripe object and event type.
         */

        $payload = [
            'type' => $type,
            'data' => [
                'object' => $object,
            ],
        ];

        $data = [
            'name' => $type,
            'gross' => $gross,
            'fee' => $fee,
            'net' => $net,
            'card_brand' => $cardBrand,
            'last4' => $last4,
            'receipt_url' => $receiptUrl,
            'payload' => json_encode($payload),
            'processed_payload' => json_encode([
                'object' => $object,
            ]),
            'headers' => json_encode([]),
            'updated_at' => now(),
        ];


        /*
         * Update existing records or insert new ones.
         */

        $existing = DB::table('webhook_calls')
            ->where('stripe_id', $stripeId)
            ->first();

        if ($existing) {

            DB::table('webhook_calls')
                ->where('id', $existing->id)
                ->update($data);

        } else {

            $data['stripe_id'] = $stripeId;
            $data['created_at'] = $createdAt;

            DB::table('webhook_calls')->insert($data);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CHARGE DISPLAY STATUS
    |--------------------------------------------------------------------------
    */

    private function chargeDisplayStatus(array $obj): string
    {
        $amount = $obj['amount'] ?? 0;
        $refunded = $obj['amount_refunded'] ?? 0;

        if ($refunded > 0 && $refunded >= $amount) {
            return 'refunded';
        }

        if ($refunded > 0) {
            return 'partially_refunded';
        }

        return $obj['status'] ?? 'unknown';
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
    */

    private function stripeObject(object $row): array
    {
        $processed = $this->processedPayload($row);

        // New processed webhook structure
        if (
            isset($processed['object'])
            && is_array($processed['object'])
        ) {
            return $processed['object'];
        }

        // Older processed structure containing original raw webhook
        if (
            isset($processed['raw'])
            && is_array($processed['raw'])
        ) {

            $raw = $processed['raw'];

            if (isset($raw['data']['object'])) {
                return $raw['data']['object'];
            }

            if (isset($raw['related_object'])) {
                return $raw['related_object'];
            }
        }

        // Older records where the full Stripe object was saved directly
        if (isset($processed['id'], $processed['object'])) {
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

        // Stripe Thin event
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
