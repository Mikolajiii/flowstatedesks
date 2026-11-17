<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class CartController extends Controller
{

    public function add(Request $request)
    {
        $productId = (int) $request->product;
        $qty = max(1, (int) $request->qty);

        $product = Product::find($productId);

        $product = Product::findOrFail($productId);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {

            $cart[$product->id]['qty'] += $qty;

        } else {

            $cart[$product->id] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'qty'   => $qty,
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return response()->json([
                'cart' => $cart,
                'status' => 'updated'
            ]);
        }

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



    public function remove(int $product)
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
