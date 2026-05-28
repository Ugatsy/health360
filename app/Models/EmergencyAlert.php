<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model
{
    protected $fillable = [
        'user_id',
        'symptom_session_id',
        'trigger_keyword',
        'user_symptom_text',
        'action_taken',
        'emergency_contact_notified',
        'emergency_contact_phone',
        'resolution',
        'sms_sent_at',
        'sms_delivery_status',
        'contacts_notified_count',
        'sms_error_message',
    ];

    protected $casts = [
        'emergency_contact_notified' => 'boolean',
        'sms_sent_at' => 'datetime',
        'contacts_notified_count' => 'integer',
    ];

    // ========== Relationships ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function symptomSession()
    {
        return $this->belongsTo(SymptomSession::class);
    }

    // ========== Scopes ==========

    public function scopeNotResolved($query)
    {
        return $query->whereNull('resolution');
    }

    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolution');
    }

    public function scopeContactNotified($query)
    {
        return $query->where('emergency_contact_notified', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ========== Methods ==========

    public function resolve($resolution)
    {
        $this->update(['resolution' => $resolution]);
    }

    public function notifyEmergencyContact()
    {
        $this->update(['emergency_contact_notified' => true]);
    }

    public function getActionTakenLabelAttribute()
    {
        return match($this->action_taken) {
            'displayed_emergency_message' => 'Displayed emergency message to user',
            'sent_sms_alert' => 'Sent SMS alert',
            'called_emergency_contact' => 'Called emergency contact',
            'displayed_911' => 'Displayed 911 prompt',
            default => 'Unknown action',
        };
    }
}
