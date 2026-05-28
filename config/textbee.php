<?php

return [
    'api_key' => env('TEXTBEE_API_KEY'),
    'device_id' => env('TEXTBEE_DEVICE_ID'),
    'api_url' => env('TEXTBEE_API_URL', 'https://api.textbee.dev/api/v1/gateway/devices'),
    'timeout' => (int) env('TEXTBEE_TIMEOUT', 30),
];
