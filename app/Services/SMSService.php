<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService
{
    protected string $apiKey;
    protected string $deviceId;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('textbee.api_key');
        $this->deviceId = config('textbee.device_id');
        $this->apiUrl = config('textbee.api_url');
    }

    public function send(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::timeout(config('textbee.timeout'))
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/' . $this->deviceId . '/send-sms', [
                    'recipients' => [$phoneNumber],
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('SMS sending failed', [
                'phone' => $phoneNumber,
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('SMS exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendBulk(array $phoneNumbers, string $message): bool
    {
        try {
            $response = Http::timeout(config('textbee.timeout'))
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/' . $this->deviceId . '/send-sms', [
                    'recipients' => $phoneNumbers,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('Bulk SMS sent', [
                    'count' => count($phoneNumbers),
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('Bulk SMS failed', [
                'count' => count($phoneNumbers),
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Bulk SMS exception', [
                'count' => count($phoneNumbers),
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
