<?php

return [
    'gateway_url' => env('TEXTBEE_GATEWAY_URL', 'http://192.168.1.100:8080/messages'),
    'from_number' => env('TEXTBEE_FROM_NUMBER', '+639945487682'),
    'timeout' => env('SMS_TIMEOUT', 30),
    'retry_attempts' => env('SMS_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('SMS_RETRY_DELAY', 5),
    'rate_limit_per_minute' => env('SMS_RATE_LIMIT', 5),

    'message_template' => env('SMS_EMERGENCY_TEMPLATE', '🚨EMERGENCY ALERT

Patient: {userName}
Risk: {riskLevel}
Symptoms: {symptomText}
Action: {recommendation}'),

    'test_message' => env('SMS_TEST_MESSAGE', 'This is a test SMS from Health360 emergency alert system. Your emergency contact settings are working correctly.'),
];
