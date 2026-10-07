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

        $prices = [
            'pro'     => 'price_1U31CgQnlXfHzKD34f26yJ9v',
            'mini'    => 'price_1U31WMQnlXfHzKD34xJQdr5u',
            'sidecar' => 'price_1U31Y8QnlXfHzKD37qgJrffc'
        ];
        $descriptions = [
            'pro'     => 'FlowState Pro Desk – Full size workstation',
            'mini'    => 'FlowState Mini Desk – Compact workspace',
            'sidecar' => 'FlowState Sidecar – Add‑on extension surface'
        ];


        $product = $request->product;

        if (!isset($prices[$product])) {
            abort(400, 'Invalid product.');
        }

        $session = Session::create([
            'mode' => 'payment',
            'customer_creation' => 'always',
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['CA'],
            ],
            'metadata' => [
                'product'      => $product,
                'price_id'     => $prices[$product],
                'description'  => $descriptions[$product],
            ],

            'line_items' => [[
                'price' => $prices[$product],
                'quantity' => 1,
            ]],
            'success_url' => route('payment.success'),
            'cancel_url'  => route('payment.cancel'),
        ]);

        return redirect($session->url);
    }
}
