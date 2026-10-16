<?php

require 'vendor/autoload.php';
use App\Services\SendGridService;

$sendgrid = app(SendGridService::class);

$success = $sendgrid->sendTemplate(
    'WELCOME_MESSAGE',
    "mikolajkondakow@gmail.com",
    "Flow State Customer",
    [
        'message' => "Payment completed. Let me know if you got this email",
        'order_id' => 12345,
        'total' => 123.45,

    ]
);

return $success ? "Email sent successfully." : "SendGrid failed.";





