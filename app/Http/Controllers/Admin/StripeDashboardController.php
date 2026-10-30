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

    public function orders()
    {
        $orders = $this->webhookCallsOfType(self::ORDER_EVENT_TYPES)
            ->orderBy('created_at', 'desc')
            ->paginate(self::PER_PAGE);

        foreach ($orders as $order) {

            $obj = $this->stripeObject($order);


            $metadata = $obj['metadata'] ?? [];

            // Product info
            $order->product = $metadata['product'] ?? null;
            $order->description = $metadata['description'] ?? null;
            $order->price_id = $metadata['price_id'] ?? null;

            // Cart items
            $items = isset($metadata['items'])
                ? json_decode($metadata['items'], true)
                : [];

            $order->items = is_array($items)
                ? $items
                : [];

            // Customer
            $order->buyer_name = $this->firstFilled(
                $obj['customer_details']['name'] ?? null,
                $obj['billing_details']['name'] ?? null,
                $obj['shipping']['name'] ?? null,
            );

            $order->buyer_email = $this->firstFilled(
                $obj['customer_details']['email'] ?? null,
                $obj['billing_details']['email'] ?? null,
                $obj['receipt_email'] ?? null,
            );

            // Amount
            $order->amount = $obj['amount_total']
                ?? $obj['amount']
                ?? null;

            // Currency
            $order->currency = strtoupper(
                $obj['currency'] ?? ''
            );

            // Status
            $order->status = $obj['payment_status']
                ?? $obj['status']
                ?? null;
        }

        return view('admin.stripe.orders.index', compact('orders'));
    }

    public function orderShow($id)
    {
        $hook = $this->findHook($id);

        return view('admin.stripe.orders.show', compact('hook'));
    }

    public function transactions()
    {
        $transactions = $this->webhookCallsOfType(self::TRANSACTION_EVENT_TYPES)
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE);

        foreach ($transactions as $txn) {
            $obj = $this->stripeObject($txn);

            $txn->buyer_name = $this->firstFilled(
                $obj['billing_details']['name'] ?? null,
                $obj['shipping']['name'] ?? null,
                $obj['customer_details']['name'] ?? null,
            );

            $txn->display_status = match ($obj['object'] ?? null) {

                'checkout.session' => $obj['payment_status'] ?? null,

                'charge' => $obj['status'] ?? null,

                'payment_intent' => $obj['status'] ?? null,

                'payout' => $obj['status'] ?? null,

                default => null,
            };


        }

        return view('admin.stripe.transactions.index', compact('transactions'));
    }

    public function transactionShow($id)
    {
        $hook = $this->findHook($id);

        $payload = json_decode($hook->payload, true);

        $hook->event_type = $payload['type'] ?? null;

        $hook->gross_amount = $hook->gross
            ? $hook->gross / 100
            : null;

        $hook->fee_amount = $hook->fee
            ? $hook->fee / 100
            : null;

        $hook->net_amount = $hook->net
            ? $hook->net / 100
            : null;

        return view('admin.stripe.transactions.show', compact('hook'));
    }

    public function sync()
    {
        Stripe::setApiKey(config('stripe.stripe.secret'));

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
            Log::error('Stripe sync failed', ['message' => $e->getMessage()]);

            return redirect()
                ->route('admin.stripe.transactions')
                ->with('error', 'Stripe sync failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.stripe.transactions')
            ->with('success', 'Stripe sync completed successfully.');
    }

    protected function storeStripeEvent(string $type, $object): void
    {
        if (DB::table('webhook_calls')
            ->where('stripe_id', $object->id)
            ->exists()) {
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
            'name'              => $type,
            'stripe_id'         => $object->id,

            'gross'             => $gross,
            'fee'               => $fee,
            'net'               => $net,

            'card_brand'        => $cardBrand,
            'last4'             => $last4,
            'receipt_url'       => $receiptUrl,

            'payload'           => json_encode([
                'type' => $type,
                'data' => [
                    'object' => $object,
                ],
            ]),

            'processed_payload' => json_encode([]),
            'headers'           => json_encode([]),

            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    /**
     * Base query for webhook calls whose payload "type" is one of the given event types.
     */
    private function webhookCallsOfType(array $types)
    {
        $placeholders = implode(', ', array_fill(0, count($types), '?'));

        return DB::table('webhook_calls')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) IN ($placeholders)", $types);
    }

    /**
     * Load a webhook call with its JSON columns decoded, or 404.
     */
    private function findHook($id): object
    {
        $hook = DB::table('webhook_calls')->where('id', $id)->first();

        abort_if(! $hook, 404);

        foreach (['payload', 'processed_payload', 'headers', 'attachments'] as $column) {
            $hook->{$column} = json_decode($hook->{$column} ?? 'null', true);
        }

        return $hook;
    }

    /**
     * The Stripe object (data.object) inside a webhook call's raw JSON payload.
     */
    private function stripeObject(object $row): array
    {
        $processed = json_decode(
            $row->processed_payload ?? 'null',
            true
        );

        if (!empty($processed)) {
            return $processed;
        }

        $payload = json_decode(
            $row->payload ?? 'null',
            true
        );

        return $payload['data']['object'] ?? [];
    }

    /**
     * Return the first non-empty value, or null.
     */
    private function firstFilled(mixed ...$values): mixed
    {
        foreach ($values as $value) {
            if (! empty($value)) {
                return $value;
            }
        }

        return null;
    }
}
