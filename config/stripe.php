<?php

return [
    'secret'  => env('STRIPE_SECRET'),
    'key'     => env('STRIPE_KEY'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE')
    ],
    'model' => env('STRIPE_MODEL')
];
