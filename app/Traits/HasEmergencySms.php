<?php

namespace App\Traits;

use App\Models\EmergencyContact;
use App\Services\EmergencySmsService;
use Illuminate\Support\Facades\Log;

trait HasEmergencySms
{
    public function sendEmergencySms(string $message, int $symptomSessionId): array
    {
        $service = app(EmergencySmsService::class);

        $result = $service->sendToAllContacts($this->id, $message, $symptomSessionId);

        Log::info('HasEmergencySms: sendEmergencySms completed', [
            'user_id' => $this->id,
            'total_contacts' => $result['total'] ?? 0,
            'sent_count' => $result['sent'] ?? 0,
        ]);

        return $result;
    }

    public function getEmergencyContacts()
    {
        return $this->emergencyContacts;
    }

    public function hasValidEmergencyContacts(): bool
    {
        if (!$this->relationLoaded('emergencyContacts')) {
            $this->load('emergencyContacts');
        }

        return $this->emergencyContacts
            ->filter(fn(EmergencyContact $contact) => $this->isValidPhilippineNumber($contact->phone_number))
            ->isNotEmpty();
    }

    private function isValidPhilippineNumber(string $number): bool
    {
        $cleaned = preg_replace('/[^0-9]/', '', $number);

        return (strlen($cleaned) === 10 && str_starts_with($cleaned, '9'))
            || (strlen($cleaned) === 11 && str_starts_with($cleaned, '09'))
            || (strlen($cleaned) >= 11 && str_starts_with($cleaned, '63'))
            || (strlen($cleaned) === 13 && str_starts_with($cleaned, '0639'));
    }
}
