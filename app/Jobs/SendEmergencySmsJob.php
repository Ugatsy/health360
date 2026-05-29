<?php

namespace App\Jobs;

use App\Models\EmergencyAlert;
use App\Models\User;
use App\Services\SMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendEmergencySmsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public int $tries = 3;
    public int $backoff = 5;

    private const int RATE_LIMIT_WINDOW = 60;
    private const string RATE_LIMIT_CACHE_KEY = 'sms_rate_limit_user_';

    public function __construct(
        public int $userId,
        public int $emergencyAlertId,
        public string $message,
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            Log::error('SendEmergencySmsJob: User not found', ['user_id' => $this->userId]);
            return;
        }

        $alert = EmergencyAlert::find($this->emergencyAlertId);
        if (!$alert) {
            Log::error('SendEmergencySmsJob: EmergencyAlert not found', ['alert_id' => $this->emergencyAlertId]);
            return;
        }

        if ($alert->sms_sent_at) {
            Log::info('SendEmergencySmsJob: Alert already processed, skipping', [
                'alert_id' => $this->emergencyAlertId,
            ]);
            return;
        }

        $contacts = $user->emergencyContacts;

        if ($contacts->isEmpty()) {
            Log::warning('SendEmergencySmsJob: No contacts to send SMS to', [
                'user_id' => $this->userId,
                'alert_id' => $this->emergencyAlertId,
            ]);
            return;
        }

        $rateLimitPerMinute = config('sms.rate_limit_per_minute', 5);
        $cacheKey = self::RATE_LIMIT_CACHE_KEY . $this->userId;

        $sentCount = (int) Cache::get($cacheKey, 0);
        $availableSlots = $rateLimitPerMinute - $sentCount;

        if ($availableSlots <= 0) {
            Log::warning('SendEmergencySmsJob: Rate limit exhausted, releasing', [
                'user_id' => $this->userId,
                'alert_id' => $this->emergencyAlertId,
                'sent_in_window' => $sentCount,
            ]);
            $this->release(self::RATE_LIMIT_WINDOW);
            return;
        }

        $smsService = app(SMSService::class);
        $successCount = 0;
        $failCount = 0;
        $errors = [];

        foreach ($contacts as $contact) {
            if ($successCount + $failCount >= $availableSlots) {
                Log::warning('SendEmergencySmsJob: Rate limit would be exceeded, stopping', [
                    'user_id' => $this->userId,
                    'alert_id' => $this->emergencyAlertId,
                    'sent_in_window' => $sentCount,
                    'attempted' => $successCount + $failCount,
                ]);
                $errors[] = 'Rate limit reached, remaining contacts deferred';
                break;
            }

            $count = Cache::increment($cacheKey);
            if ($count === 1) {
                Cache::put($cacheKey, 1, self::RATE_LIMIT_WINDOW);
            }

            if ($smsService->send($contact->phone_number, $this->message)) {
                $successCount++;
            } else {
                $failCount++;
                $errors[] = "Contact {$contact->id}: send failed";
            }
        }

        $deliveryStatus = match (true) {
            $successCount > 0 && $failCount === 0 => 'delivered',
            $successCount === 0 && $failCount > 0 => 'failed',
            $successCount > 0 && $failCount > 0 => 'partial',
            default => 'pending',
        };

        $alert->update([
            'sms_sent_at' => now(),
            'sms_delivery_status' => $deliveryStatus,
            'contacts_notified_count' => $successCount,
            'emergency_contact_notified' => $successCount > 0,
            'sms_error_message' => $errors ? implode(' | ', $errors) : null,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendEmergencySmsJob: All retries exhausted', [
            'user_id' => $this->userId,
            'alert_id' => $this->emergencyAlertId,
            'error' => $exception->getMessage(),
        ]);

        EmergencyAlert::where('id', $this->emergencyAlertId)->update([
            'sms_delivery_status' => 'failed',
            'sms_sent_at' => now(),
            'sms_error_message' => 'All retries failed: ' . $exception->getMessage(),
        ]);
    }
}
