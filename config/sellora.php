// config/sellora.php
<?php
return [
    'app_name' => env('APP_NAME', 'Sellora'),
    'app_description' => 'Classified Ads Marketplace',

    // Pagination
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],

    // Image Upload
    'images' => [
        'max_size' => 5120, // KB (5MB)
        'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif'],
        'directories' => [
            'ads' => 'ads',
            'avatars' => 'avatars',
            'categories' => 'categories',
        ],
    ],

    // Ad Settings
    'ads' => [
        'min_price' => 0,
        'max_price' => 1000000,
        'max_images_per_ad' => 10,
        'max_featured_days' => 90,
        'default_duration_days' => 30,
        'require_approval' => true,
    ],

    // Subscription
    'subscription' => [
        'default_duration_days' => 30,
        'auto_renew' => false,
    ],

    // Rate Limiting
    'rate_limit' => [
        'requests_per_minute' => 60,
        'auth_requests_per_minute' => 5,
        'upload_requests_per_minute' => 10,
    ],

    // Email Verification
    'email_verification' => [
        'enabled' => true,
        'token_expires_minutes' => 60,
    ],

    // Admin
    'admin' => [
        'require_2fa' => false,
        'session_timeout_minutes' => 60,
    ],
];
