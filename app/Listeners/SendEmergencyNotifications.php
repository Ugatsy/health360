<?php

namespace App\Listeners;

use App\Events\EmergencyDetected;
use App\Jobs\SendEmergencySmsJob;
use App\Models\EmergencyAlert;
use App\Notifications\HighRiskAlert;
use App\Services\EmergencySmsService;
use Illuminate\Support\Facades\Log;

class SendEmergencyNotifications
{
    public function handle(EmergencyDetected $event): void
    {
        $contacts = $event->user->emergencyContacts;

        if ($contacts->isEmpty()) {
            Log::warning('No emergency contacts found for user', [
                'user_id' => $event->user->id,
                'risk_level' => $event->riskLevel,
            ]);
            return;
        }

        $sessionId = $event->symptomEntry?->session_id;

        if ($sessionId) {
            $existing = EmergencyAlert::where('user_id', $event->user->id)
                ->where('symptom_session_id', $sessionId)
                ->where('action_taken', 'sent_sms_alert')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();

            if ($existing) {
                Log::info('Duplicate emergency alert suppressed for session', [
                    'user_id' => $event->user->id,
                    'session_id' => $sessionId,
                ]);
                return;
            }
        }

        $alert = EmergencyAlert::create([
            'user_id' => $event->user->id,
            'symptom_session_id' => $sessionId,
            'trigger_keyword' => 'ai_detected_' . $event->riskLevel,
            'user_symptom_text' => $event->symptomText,
            'action_taken' => 'sent_sms_alert',
            'emergency_contact_notified' => false,
        ]);

        $message = app(EmergencySmsService::class)->formatEmergencyMessage(
            userName: $event->user->name,
            riskLevel: $event->riskLevel,
            symptomText: $event->symptomText,
            recommendation: $event->recommendation,
        );

        SendEmergencySmsJob::dispatchSync(
            $event->user->id,
            $alert->id,
            $message,
        );

        try {
            $summary = sprintf(
                'URGENT: %s reported "%s" (Risk: %s). %s - Health360',
                $event->user->name,
                \Illuminate\Support\Str::limit($event->symptomText, 100),
                strtoupper($event->riskLevel),
                $event->recommendation,
            );

            foreach ($contacts as $contact) {
                $contact->notify(new HighRiskAlert(
                    entry: $event->symptomEntry,
                    riskLevel: $event->riskLevel,
                    summary: $summary,
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send in-app notification to emergency contacts', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
