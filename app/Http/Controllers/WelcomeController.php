<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function welcome()
    {


        $products = config('products');

        return view('welcome', compact('products'));
    }
}
