<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StripeDashboardController extends Controller
{
    public function orders()
    {
        $orders = DB::table('webhook_calls')
            ->where(function ($q) {
                $q->whereRaw("JSON_EXTRACT(payload, '$.type') LIKE '%checkout.session.completed%'");

            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Attach metadata + buyer info
        foreach ($orders as $order) {

            $payload = json_decode($order->payload, true);
            $obj = $payload['data']['object'] ?? [];

            // Metadata (legacy single-product fields)
            $order->product        = $obj['metadata']['product']        ?? null;
            $order->description    = $obj['metadata']['description']    ?? null;
            $order->price_id       = $obj['metadata']['price_id']       ?? null;

            // ⭐ NEW: Full cart items
            $itemsJson = $obj['metadata']['items'] ?? null;
            $order->items = $itemsJson ? json_decode($itemsJson, true) : [];

            // Buyer name (checkout.session.completed)
            $order->buyer_name     = $obj['customer_details']['name']   ?? null;
            $order->buyer_email    = $obj['customer_details']['email']  ?? null;

            // Fallbacks for charge.succeeded
            if (!$order->buyer_name && !empty($obj['billing_details']['name'])) {
                $order->buyer_name = $obj['billing_details']['name'];
            }

            if (!$order->buyer_email && !empty($obj['billing_details']['email'])) {
                $order->buyer_email = $obj['billing_details']['email'];
            }

            if (!$order->buyer_name && !empty($obj['shipping']['name'])) {
                $order->buyer_name = $obj['shipping']['name'];
            }

            // Logging (unchanged)
            \Log::info('TYPE DEBUG', [
                'raw_type' => $payload['type'] ?? null,
            ]);

            \Log::info('METADATA DEBUG', [
                'id'       => $order->id,
                'metadata' => $obj['metadata'] ?? null,
            ]);

            \Log::info('PRODUCT FIELD', [
                'id'      => $order->id,
                'product' => $obj['metadata']['product'] ?? null,
            ]);
        }


        return view('admin.stripe.orders.index', compact('orders'));
    }



    public function orderShow($id)
    {
        $hook = DB::table('webhook_calls')->where('id', $id)->first();
        if (! $hook) abort(404);

        $hook->payload = json_decode($hook->payload, true);
        $hook->processed_payload = json_decode($hook->processed_payload, true);
        $hook->headers = json_decode($hook->headers, true);
        $hook->attachments = json_decode($hook->attachments, true);

        return view('admin.stripe.orders.show', compact('hook'));
    }



    public function transactions()
    {
        $transactions = DB::table('webhook_calls')
            ->where(function ($q) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) = 'payout.paid'")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) = 'payout.created'")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) = 'balance.available'")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) = 'charge.refunded'")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) = 'charge.updated'")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) = 'charge.succeeded'")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(payload, '$.type')) = 'checkout.session.completed'");
            })
            ->orderBy('id', 'desc')
            ->paginate(25);

        foreach ($transactions as $txn) {

            $payload = json_decode($txn->payload, true);
            $obj = $payload['data']['object'] ?? [];

            $name = null;

            // charge.succeeded → billing_details.name
            if (!empty($obj['billing_details']['name'])) {
                $name = $obj['billing_details']['name'];
            }

            // fallback → shipping.name
            if (!$name && !empty($obj['shipping']['name'])) {
                $name = $obj['shipping']['name'];
            }

            // checkout.session.completed → customer_details.name
            if (!$name && !empty($obj['customer_details']['name'])) {
                $name = $obj['customer_details']['name'];
            }

            $txn->buyer_name = $name;
        }

        return view('admin.stripe.transactions.index', compact('transactions'));
    }



    public function transactionShow($id)
    {
        $hook = DB::table('webhook_calls')->where('id', $id)->first();
        if (! $hook) abort(404);

        $hook->payload = json_decode($hook->payload, true);
        $hook->processed_payload = json_decode($hook->processed_payload, true);
        $hook->headers = json_decode($hook->headers, true);
        $hook->attachments = json_decode($hook->attachments, true);

        return view('admin.stripe.transactions.show', compact('hook'));
    }
    public function sync()
    {
        \Stripe\Stripe::setApiKey(config('stripe.stripe.secret'));

        // Fetch latest charges
        $charges = \Stripe\Charge::all(['limit' => 20]);

        foreach ($charges->data as $charge) {
            $this->storeStripeEvent('charge.succeeded', $charge);
        }

        // Fetch latest payouts
        $payouts = \Stripe\Payout::all(['limit' => 20]);

        foreach ($payouts->data as $payout) {
            $this->storeStripeEvent('payout.paid', $payout);
        }

        // Fetch latest refunds
        $refunds = \Stripe\Refund::all(['limit' => 20]);

        foreach ($refunds->data as $refund) {
            $this->storeStripeEvent('charge.refunded', $refund);
        }

        return redirect()
            ->route('admin.stripe.transactions')
            ->with('success', 'Stripe sync completed successfully.');
    }

    protected function storeStripeEvent(string $type, $object)
    {
        $id = $object->id;

        // Avoid duplicates
        $exists = DB::table('webhook_calls')
            ->where('stripe_id', $id)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('webhook_calls')->insert([
            'name' => $type,
            'stripe_id' => $id,
            'payload' => json_encode([
                'type' => $type,
                'data' => ['object' => $object],
            ]),
            'processed_payload' => json_encode([]),
            'headers' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


}
