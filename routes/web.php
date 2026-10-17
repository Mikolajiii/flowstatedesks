<?php


use App\Http\Controllers\Admin\StripeDashboardController;
use App\Http\Controllers\CartController;
use Spatie\WebhookClient\Http\Controllers\WebhookController;
use App\Http\Controllers\CheckoutController;
//use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use App\Services\SendGridService;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::view('/terms', 'terms')->name('terms');
Route::view('/shipping', 'shipping')->name('shipping');
Route::view('/faq', 'FAQ')->name('faq');


Route::get('/payment/success', function () {

    // Clear the cart after successful payment
    session()->forget('cart');

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

//shopping cart routes

/* route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', function () {
    session()->forget('cart');
    return response()->json(['status' => 'cleared']);
});

*/
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/admin/stripe/transactions/sync', [StripeDashboardController::class, 'sync'])
    ->name('admin.stripe.transactions.sync');


/*Route::stripeWebhooks('stripe/webhook')
    ->withoutMiddleware(['web', 'csrf']);

*/


Route::stripeWebhooks('stripe/webhook');


//Route::webhooks('stripe/webhook', 'stripe')
//    ->withoutMiddleware(['web', 'csrf']);














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

//cart routes
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');


//test sendgrid
Route::get('/sendgrid-test', [\App\Http\Controllers\SendGridTestController::class, 'test']);
