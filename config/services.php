<?php

return [

    'fastapi' => [
        'base_url' => env('FASTAPI_BASE_URL', 'http://host.docker.internal:8090'),
        'api_key' => env('FASTAPI_API_KEY', 'my-super-secret-key-123'),
        'timeout' => env('FASTAPI_TIMEOUT', 10),
        'retries' => env('FASTAPI_RETRIES', 2),
        'retry_sleep_ms' => env('FASTAPI_RETRY_SLEEP_MS', 200),
    ],

];
