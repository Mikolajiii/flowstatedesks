<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function pricing()
    {
        $products = Product::orderBy('id')->get();

        return view('admin.products.pricing', compact('products'));
    }

    public function updatePrices(Request $request)
    {
        foreach ($request->products as $productId => $price) {

            Product::where('id', $productId)
                ->update([
                    'price' => (int) round($price * 100),
                ]);
        }

        return back()->with(
            'success',
            'Prices updated successfully.'
        );
    }
}
