<?php

namespace App\Notifications;

use App\Models\SymptomEntry;
use App\Models\EmergencyContact;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class HighRiskAlert extends Notification
{
    public function __construct(
        public SymptomEntry $entry,
        public string $riskLevel,
        public string $summary
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'contact_name' => $notifiable->name,
            'contact_phone' => $notifiable->phone_number,
            'risk_level' => $this->riskLevel,
            'symptoms' => $this->entry->symptom_text,
            'summary' => $this->summary,
        ];
    }
}
