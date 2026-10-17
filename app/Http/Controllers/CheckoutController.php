<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('stripe.stripe.secret'));

        // Get cart
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            abort(400, 'Cart is empty.');
        }

        // Build Stripe line items
        $lineItems = [];
        $cartItems = [];   // 👈 snapshot for metadata

        foreach ($cart as $key => $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'cad',
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['price'], // cents
                ],
                'quantity' => $item['qty'],
            ];

            // 👇 capture cart item details for later
            $cartItems[] = [
                'key'   => $key,
                'name'  => $item['name'],
                'qty'   => $item['qty'],
                'price' => $item['price'], // cents
            ];
        }

        // First item (for legacy fields)
        $firstItem = $cart[array_key_first($cart)];

        $session = Session::create([
            'mode' => 'payment',
            'customer_creation' => 'always',
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['CA'],
            ],
            'line_items' => $lineItems,

            // ⭐ Full cart snapshot in metadata
            'metadata' => [
                'items'      => json_encode($cartItems),          // 👈 all items
                'product'    => $firstItem['name'] ?? 'Unknown', // legacy single product
                'description'=> $firstItem['description'] ?? null,
                'price_id'   => $firstItem['price_id'] ?? null,
            ],

            'success_url' => route('payment.success'),
            'cancel_url'  => route('payment.cancel'),
        ]);

        return redirect($session->url);
    }
}
