<?php


return [
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'ai' => [
        'driver' => env('AI_DRIVER', 'fake'),
        'base_url' => env('AI_SERVICE_BASE_URL', 'http://localhost:8001'),
        'timeout' => env('AI_SERVICE_TIMEOUT', 3),
    ],
    'payment' => [
        'driver' => env('PAYMENT_DRIVER', 'fake'),
    ],
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    ],
    'xendit' => [
        'secret_key' => env('XENDIT_SECRET_KEY'),
        'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    ],
    'whatsapp' => [
        'driver' => env('WHATSAPP_DRIVER', 'log'),
        'api_url' => env('WHATSAPP_API_URL'),
        'token' => env('WHATSAPP_TOKEN'),
    ],
];

