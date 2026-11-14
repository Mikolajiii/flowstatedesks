<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function welcome()
    {
        $products = Product::where('active', 1)
            ->get()
            ->keyBy('slug');

        return view('welcome', compact('products'));
    }
}
