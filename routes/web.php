<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use App\Services\SendGridService;
Route::get('/', function () {
    return view('welcome');
});
Route::view('/terms', 'terms')->name('terms');
Route::view('/shipping', 'shipping')->name('shipping');
Route::view('/faq', 'FAQ')->name('faq');
Route::get('/payment/success', function () {
    return view('success');
})->name('payment.success');

Route::get('/payment/cancel', function () {
    return view('cancel');
})->name('payment.cancel');





Route::get('/test-email', function () {
    $sendgrid = app(SendGridService::class);

    $success = $sendgrid->sendTemplate(
        'PAYMENT_SUCCESS',
        'mikolajkondakow@gmail.com',
        'Flow State Customer',
        [
            'message' => 'Payment completed. Let me know if you got this email.',
        ]
    );

    return $success ? 'Email sent!' : 'SendGrid failed.';
});


route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');


Route::stripeWebhooks('stripe/webhook');


//stripe  admin routes
//make middleware for admin routes using ADMIN_KEY=supersecret_9283hdsf in .env file later
Route::prefix('admin')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');

    Route::get('/stripe/balance', [\App\Http\Controllers\Admin\StripeController::class, 'balance'])->name('admin.stripe.balance');
    Route::get('/stripe/payouts', [\App\Http\Controllers\Admin\StripeController::class, 'payouts'])->name('admin.stripe.payouts');

    Route::get('/webhooks', [\App\Http\Controllers\Admin\WebhookController::class, 'index'])->name('admin.webhooks');
});

