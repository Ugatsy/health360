<?php

namespace App\Services;

use App\Models\EmergencyAlert;
use App\Models\EmergencyContact;
use App\Models\SymptomSession;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmergencySmsService
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

    public function sendToContact(EmergencyContact $contact, string $message, int $symptomSessionId): array
    {
        $phone = $contact->phone_number;

        try {
            $response = Http::timeout(config('textbee.timeout'))
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/' . $this->deviceId . '/send-sms', [
                    'recipients' => [$phone],
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('EmergencySmsService: SMS sent', [
                    'contact_id' => $contact->id,
                    'phone' => $this->maskPhoneNumber($phone),
                    'session_id' => $symptomSessionId,
                ]);
                return ['success' => true];
            }

            Log::error('EmergencySmsService: API error', [
                'contact_id' => $contact->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return ['success' => false, 'error' => "HTTP {$response->status()}: {$response->body()}"];

        } catch (\Exception $e) {
            Log::error('EmergencySmsService: Exception', [
                'contact_id' => $contact->id,
                'phone' => $this->maskPhoneNumber($phone),
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendToAllContacts(int $userId, string $message, int $symptomSessionId): array
    {
        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }

        $contacts = $user->emergencyContacts;
        if ($contacts->isEmpty()) {
            return ['success' => false, 'error' => 'No emergency contacts found'];
        }

        $results = [];
        foreach ($contacts as $contact) {
            $results[] = [
                'contact_id' => $contact->id,
                'name' => $contact->name,
                'result' => $this->sendToContact($contact, $message, $symptomSessionId),
            ];
        }

        $successCount = count(array_filter($results, fn($r) => $r['result']['success'] ?? false));

        return [
            'success' => $successCount > 0,
            'total' => count($contacts),
            'sent' => $successCount,
            'results' => $results,
        ];
    }

    public function formatEmergencyMessage(
        string $userName,
        string $riskLevel,
        string $symptomText,
        string $recommendation,
    ): string {
        $template = config('sms.message_template');

        $replacements = [
            '{userName}' => $userName,
            '{riskLevel}' => strtoupper($riskLevel),
            '{symptomText}' => \Illuminate\Support\Str::limit($symptomText, 120),
            '{recommendation}' => \Illuminate\Support\Str::limit($recommendation, 100),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    public function getDeliveryStatus(int $smsLogId): ?EmergencyAlert
    {
        return EmergencyAlert::find($smsLogId);
    }

    private function maskPhoneNumber(string $number): string
    {
        if (strlen($number) >= 8) {
            return substr($number, 0, 4) . '****' . substr($number, -3);
        }
        return '****';
    }
}
