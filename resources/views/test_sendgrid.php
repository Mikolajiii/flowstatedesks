<?php

require 'vendor/autoload.php';
use App\Services\SendGridService;

$sendgrid = app(SendGridService::class);

$success = $sendgrid->sendTemplate(
    'PAYMENT_SUCCESS',
    "mikolajkondakow@gmail.com",
    "Flow State Customer",
    [
        'message' => "Payment completed. Let me know if you got this email",
        'bcc'     => "michaelkondakow@gmail.com",
    ]
);

return $success ? "Email sent successfully." : "SendGrid failed.";





