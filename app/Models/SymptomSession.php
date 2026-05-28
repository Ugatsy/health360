<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SymptomSession extends Model
{
    protected $table = 'symptom_sessions';

    protected $fillable = [
        'user_id',
        'session_uuid',
        'started_at',
        'completed_at',
        'status',
        'device_type',
        'app_version',
        'highest_risk_level',
        'was_emergency_detected',
        'emergency_recommendation'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'was_emergency_detected' => 'boolean'
    ];

    public function symptomEntries()
    {
        return $this->hasMany(SymptomEntry::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aiResponse()
    {
        return $this->hasOneThrough(
            AiResponse::class,
            SymptomEntry::class,
            'session_id', // Foreign key on symptom_entries
            'symptom_entry_id', // Foreign key on ai_responses
            'id', // Local key on symptom_sessions
            'id' // Local key on symptom_entries
        );
    }

    public function markEmergency($message)
    {
        $this->update([
            'was_emergency_detected' => true,
            'emergency_recommendation' => $message,
            'status' => 'emergency_routed',
            'highest_risk_level' => 'emergency'
        ]);
    }
}
