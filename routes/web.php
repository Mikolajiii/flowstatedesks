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



Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');



/*Route::stripeWebhooks('stripe/webhook')
    ->withoutMiddleware(['web', 'csrf']);

*/


Route::stripeWebhooks('stripe/webhook');





Route::get('/admin/logout', function () {

    session()->flush();

    return redirect('/admin/login');

})->name('admin.logout');



Route::get('/admin/login', function () {

    return view('admin.login');

})->name('admin.login');

Route::post('/admin/login', function (\Illuminate\Http\Request $request) {

    if ($request->password === env('ADMIN_PASSWORD')) {

        session([
            'admin_authenticated' => true
        ]);

        return redirect()->route('admin.stripe.dashboard');
    }

    return back()->withErrors([
        'password' => 'Invalid password'
    ]);

})->name('admin.login.submit');





Route::middleware('admin.password')->group(function () {
//stripe  admin routes
Route::get('/admin/stripe', function () {
    return view('admin.stripe.dashboard');
})->name('admin.stripe.dashboard');

Route::prefix('admin/stripe')->group(function () {
    Route::get('/orders', [StripeDashboardController::class, 'orders'])->name('admin.stripe.orders');
    Route::get('/orders/{id}', [StripeDashboardController::class, 'orderShow'])->name('admin.stripe.orders.show');

    Route::get('/transactions/sync', [StripeDashboardController::class, 'sync'])->name('admin.stripe.transactions.sync');
    Route::get('/transactions', [StripeDashboardController::class, 'transactions'])->name('admin.stripe.transactions');
    Route::get('/transactions/{id}', [StripeDashboardController::class, 'transactionShow'])->name('admin.stripe.transactions.show');
});
});
//cart routes
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');



//test sendgrid
Route::get('/sendgrid-test', [\App\Http\Controllers\SendGridTestController::class, 'test']);
