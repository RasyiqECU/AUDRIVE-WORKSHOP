<?php

/**
 * Konfigurasi AI Service (Python FastAPI)
 * Nilai diambil dari .env Laravel.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | URL endpoint Python AI Service
    |--------------------------------------------------------------------------
    | Contoh: http://localhost:8001   (development)
    |         http://ai-service:8001  (Docker)
    */
    'url' => env('AI_SERVICE_URL', 'http://localhost:8001'),

    /*
    |--------------------------------------------------------------------------
    | Secret key untuk autentikasi request
    | Harus sama dengan API_SECRET di .env Python
    |--------------------------------------------------------------------------
    */
    'secret' => env('AI_SERVICE_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Timeout HTTP request ke AI service (detik)
    |--------------------------------------------------------------------------
    */
    'timeout' => env('AI_SERVICE_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Retry otomatis jika gagal
    |--------------------------------------------------------------------------
    */
    'retry_times' => env('AI_SERVICE_RETRY', 2),
    'retry_sleep' => env('AI_SERVICE_RETRY_SLEEP', 1000), // milliseconds

];
