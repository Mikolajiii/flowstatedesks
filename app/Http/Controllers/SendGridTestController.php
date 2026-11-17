<?php

namespace App\Http\Controllers;

use App\Services\SendGridService;
use Illuminate\Support\Facades\Log;

class SendGridTestController extends Controller
{
    public function test()
    {
        Log::info('📨 SendGrid TEST: starting');

        $sendgrid = app(SendGridService::class);

        $success = $sendgrid->sendTemplate(
            'WELCOME_MESSAGE',
            'michaelkondakow@gmail.com',   // main recipient
            'Mickey',
            [
                'message'  => "Michael you have a new flowstate desk order! Please check your dashboard for details.",
                'order_id' => 12345,
                'total'    => 123.45,
                'items'=>"hello i have items"
            ],

        );


        Log::info('📨 SendGrid TEST: result', [
            'success' => $success,
        ]);

        return $success
            ? 'SendGrid TEST: Email sent successfully!'
            : 'SendGrid TEST: FAILED — check laravel.log';
    }
}

