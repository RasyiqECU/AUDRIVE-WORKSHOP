<?php


return [
    'ai' => [
        'driver' => env('AI_DRIVER', 'fake'), // fastapi or fake
        'base_url' => env('AI_SERVICE_BASE_URL', 'http://localhost:8001'),
        'timeout' => env('AI_SERVICE_TIMEOUT', 3),
    ],
];



