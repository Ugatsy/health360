<?php

namespace App\Events;

use App\Models\SymptomEntry;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmergencyDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public string $riskLevel,
        public string $symptomText,
        public string $recommendation,
        public ?SymptomEntry $symptomEntry = null,
    ) {}
}
