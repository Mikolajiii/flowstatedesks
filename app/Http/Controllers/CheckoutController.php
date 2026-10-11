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
        }

        // Create checkout session
        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'customer_creation' => 'always',
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['CA'],
            ],
            'line_items' => $lineItems,
            'success_url' => route('payment.success'),
            'cancel_url'  => route('payment.cancel'),
        ]);

        return redirect($session->url);
    }


}
