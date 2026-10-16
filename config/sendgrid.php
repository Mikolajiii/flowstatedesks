<?php
// config/sendgrid.php
return [

    'api_key' => env('SENDGRID_API_KEY'),

    'templates' => [
        'WELCOME_MESSAGE' => env('SENDGRID_TEMPLATE_WELCOME_MESSAGE'),
    ],

];
