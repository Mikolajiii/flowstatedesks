<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductPricingController extends Controller
{
    public function edit()
    {
        $products = Product::orderBy('id')->get();

        return view(
            'admin.products.pricing',
            compact('products')
        );
    }

    public function update(Request $request)
    {
        foreach ($request->products as $productId => $price) {

            Product::where('id', $productId)
                ->update([
                    'price' => (int) round($price * 100),
                ]);
        }

        session([
            'price_save_success' => true,
        ]);

        return redirect()
            ->route('admin.products.edit');
    }
}
