<?php

return [
    'rapidapi' => [
        'key' => env('RAPIDAPI_KEY'),
        'host' => env('RAPIDAPI_HOST'),
        'base_url' => env('RAPIDAPI_BASE_URL'),
    ],
    'http' => [
        'connect_timeout' => env('RAPIDAPI_CONNECT_TIMEOUT', 5),
        'timeout' => env('RAPIDAPI_TIMEOUT', 10),
        'download_timeout' => env('REEL_DOWNLOAD_TIMEOUT', 120),
        'retries' => env('RAPIDAPI_RETRIES', 2),
        'retry_delay_ms' => env('RAPIDAPI_RETRY_DELAY_MS', 200),
    ],
    'security' => [
        'allowed_video_hosts' => array_filter(array_map('trim', explode(',', (string) env(
            'ALLOWED_VIDEO_HOSTS',
            'cdninstagram.com,fbcdn.net'
        )))),
    ],
];
