<?php


use App\Http\Controllers\Admin\StripeDashboardController;
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



Route::stripeWebhooks('stripe/webhook')
    ->withoutMiddleware(['web', 'csrf']);







//stripe  admin routes

Route::get('/admin/stripe', function () {
    return view('admin.stripe.dashboard');
})->name('admin.stripe.dashboard');

Route::prefix('admin/stripe')->group(function () {
    Route::get('/orders', [StripeDashboardController::class, 'orders'])->name('admin.stripe.orders');
    Route::get('/orders/{id}', [StripeDashboardController::class, 'orderShow'])->name('admin.stripe.orders.show');

    Route::get('/transactions', [StripeDashboardController::class, 'transactions'])->name('admin.stripe.transactions');
    Route::get('/transactions/{id}', [StripeDashboardController::class, 'transactionShow'])->name('admin.stripe.transactions.show');
});
