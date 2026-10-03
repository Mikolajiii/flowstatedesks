<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Stripe\Stripe;
use Stripe\Balance;
use Stripe\Payout;

class StripeController extends Controller
{
    public function balance()
    {
        Stripe::setApiKey(config('stripe.stripe.secret'));

        $balance = Balance::retrieve();

        return view('admin.stripe.balance', compact('balance'));
    }

    public function payouts()
    {
        Stripe::setApiKey(config('stripe.secret'));

        $payouts = Payout::all(['limit' => 50]);

        return view('admin.stripe.payouts', compact('payouts'));
    }
}
