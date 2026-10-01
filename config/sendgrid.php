<?php
return [
    'api_key' => env('SENDGRID_API_KEY'),

    'templates' => [
        // example
        'PAYMENT_SUCCESS' => env('SENDGRID_TEMPLATE_WELCOME_MESSAGE'),
    ],
];
