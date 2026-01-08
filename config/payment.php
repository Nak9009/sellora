<?php
return [
    'default' => env('PAYMENT_GATEWAY', 'stripe'),

    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
    ],

    'methods' => [
        'stripe' => 'Stripe',
        'paypal' => 'PayPal',
        'offline' => 'Bank Transfer',
    ],

    'currencies' => [
        'USD' => 'US Dollar',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'PHP' => 'Philippine Peso',
        'KHR' => 'Cambodian Riel',
    ],
];
