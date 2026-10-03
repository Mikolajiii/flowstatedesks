<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessStripeWebhook;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(config('stripe.stripe.secret'));


        $endpointSecret = config('stripe.stripe.webhook_secret');


        $payload    = $request->getContent();
        $sigHeader  = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        // ✅ Offload heavy work to a job
        ProcessStripeWebhook::dispatch($event);

        // Respond quickly so Stripe is happy
        return response('Webhook received', 200);
    }
}
