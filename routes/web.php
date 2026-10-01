<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Services\SendGridService;
Route::get('/', function () {
    return view('welcome');
});
Route::view('/terms', 'terms')->name('terms');
Route::view('/shipping', 'shipping')->name('shipping');
Route::view('/faq', 'FAQ')->name('faq');


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
