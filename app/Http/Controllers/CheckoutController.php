<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Product;
class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('stripe.stripe.secret'));

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            abort(400, 'Cart is empty.');
        }

        $lineItems = [];
        $cartItems = [];

        foreach ($cart as $item) {

            $product = Product::findOrFail($item['id']);

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'cad',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $product->price,
                ],
                'quantity' => (int) $item['qty'],
            ];

            $cartItems[] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'qty'        => $item['qty'],
                'price'      => $product->price,
            ];
        }



        $session = Session::create([
            'mode' => 'payment',
            'customer_creation' => 'always',

            'billing_address_collection' => 'required',

            'shipping_address_collection' => [
                'allowed_countries' => ['CA'],
            ],

            'line_items' => $lineItems,

            'metadata' => [
                'items'       => json_encode($cartItems),
                'product'     => $cartItems[0]['name'] ?? 'Unknown',
                'product_id'  => $cartItems[0]['product_id'] ?? null,
            ],

            'success_url' => route('payment.success'),
            'cancel_url'  => route('payment.cancel'),
        ]);

        return redirect($session->url);
    }
}
