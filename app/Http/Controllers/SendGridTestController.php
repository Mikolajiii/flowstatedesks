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
            'WELCOME_MESSAGE',          // your template key
            'mikolajkondakow@gmail.com',     // your email
            'Mickey',                   // name
            [
                'message' => "Payment completed. Let me know if you got this email",

            ]
        );

        Log::info('📨 SendGrid TEST: result', [
            'success' => $success,
        ]);

        return $success
            ? 'SendGrid TEST: Email sent successfully!'
            : 'SendGrid TEST: FAILED — check laravel.log';
    }
}

