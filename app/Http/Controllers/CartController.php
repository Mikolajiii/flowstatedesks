<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    protected array $products = [
        'pro' => [
            'name'  => 'FlowState Pro',
            'price' => 149900,
            'image' => '/product_photos/flowstate_pro/1_FLOWPRO_EDITED.webp',
        ],
        'mini' => [
            'name'  => 'FlowState Mini',
            'price' => 109900,
            'image' => '/product_photos/flowstate_mini/1_FLOWMINI_EDITED.webp',
        ],
        'sidecar' => [
            'name'  => 'FlowState Sidecar',
            'price' => 45000,
            'image' => '/product_photos/flowstate_sidecar/1_RACK_EDITED.webp',
        ],
    ];

    public function add(Request $request)
    {
        $product = $request->product;
        $qty     = max(1, (int) $request->qty);

        if (!isset($this->products[$product])) {
            abort(400, 'Invalid product.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product])) {
            $cart[$product]['qty'] += $qty;
        } else {
            $cart[$product] = [
                'name'  => $this->products[$product]['name'],
                'price' => $this->products[$product]['price'],
                'qty'   => $qty,
                'image' => $this->products[$product]['image'],
            ];
        }

        session()->put('cart', $cart);

        // ⭐ If AJAX request → return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'cart' => $cart,
                'status' => 'updated'
            ]);
        }

        // ⭐ Fallback for non-AJAX
        return redirect()->back()
            ->with('cart_open', true)
            ->with('cart_updated', true);
    }

    public function update(Request $request)
    {
        $cart = session()->get('cart', []);

        foreach ($request->qty ?? [] as $key => $newQty) {
            if (!isset($cart[$key])) {
                continue;
            }

            $newQty = max(0, (int) $newQty);

            if ($newQty === 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['qty'] = $newQty;
            }
        }

        // If cart is now empty
        if (empty($cart)) {
            session()->forget('cart');

            // ⭐ AJAX response
            if ($request->expectsJson()) {
                return response()->json([
                    'cart' => [],
                    'status' => 'updated'
                ]);
            }

            // ⭐ Fallback for non‑AJAX
            return redirect()->back()
                ->with('cart_open', true)
                ->with('cart_updated', true);
        }

        // Save updated cart
        session()->put('cart', $cart);

        // ⭐ AJAX response
        if ($request->expectsJson()) {
            return response()->json([
                'cart' => $cart,
                'status' => 'updated'
            ]);
        }

        // ⭐ Fallback for non‑AJAX
        return redirect()->back()
            ->with('cart_open', true)
            ->with('cart_updated', true);
    }



    public function remove(string $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product])) {
            unset($cart[$product]);
        }

        if (empty($cart)) {
            session()->forget('cart');

            // ⭐ AJAX response
            if (request()->expectsJson()) {
                return response()->json([
                    'cart' => [],
                    'status' => 'removed'
                ]);
            }

            return redirect()->back()
                ->with('cart_open', true)
                ->with('cart_updated', true);
        }

        session()->put('cart', $cart);

        // ⭐ AJAX response
        if (request()->expectsJson()) {
            return response()->json([
                'cart' => $cart,
                'status' => 'removed'
            ]);
        }

        return redirect()->back()
            ->with('cart_open', true)
            ->with('cart_updated', true);
    }

}
