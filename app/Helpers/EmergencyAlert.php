<?php

namespace App\Helpers;

use App\Models\User;
use App\Services\SMSService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmergencyAlert
{
    public static function send(User $user, string $riskLevel, string $symptoms, string $recommendation): bool
    {
        $smsService = app(SMSService::class);

        $message = "EMERGENCY ALERT\n";
        $message .= "User: {$user->name}\n";
        $message .= "Risk Level: {$riskLevel}\n";
        $message .= "Symptoms: {$symptoms}\n";
        $message .= "Action: {$recommendation}\n";
        $message .= "Contact: {$user->emergency_phone}";

        $key = 'sms_rate_' . now()->format('Y-m-d-H-i');

        if (Cache::get($key, 0) >= 5) {
            Log::warning('SMS rate limit exceeded');
            return false;
        }

        Cache::increment($key);
        Cache::put($key, Cache::get($key), 60);

        return $smsService->send($user->emergency_phone, $message);
    }
}
