<?php

return [
    'base_url' => env('ASTRO_AUTH_API_BASE_URL', env('ASTRO_API_BASE_URL', 'https://admin.astrorajumaharaj.com/api/v1')),
    'timeout' => (int) env('ASTRO_AUTH_API_TIMEOUT', env('ASTRO_API_TIMEOUT', 10)),
    'retry' => (int) env('ASTRO_AUTH_API_RETRY', env('ASTRO_API_RETRY', 2)),
    'endpoints' => [
        'request_otp' => '/login/otp/request',
        'resend_otp' => '/login/otp/resend',
        'verify_otp' => '/login/otp/verify',
    ],
    'features' => [
        'log_payloads' => (bool) env('ASTRO_AUTH_API_LOG_PAYLOADS', false),
    ],
];
