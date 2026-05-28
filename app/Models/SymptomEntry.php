<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SymptomEntry extends Model
{
    protected $table = 'symptom_entries';

protected $fillable = [
    'session_id',
    'user_id',
    'body_region_id',
    'symptom_text',
    'pain_type',
    'pain_intensity',
    'pain_duration',
    'additional_symptoms',
    'symptom_started_at',
    'recorded_at'
];

    protected $casts = [
        'additional_symptoms' => 'array',
        'symptom_started_at' => 'datetime',
        'recorded_at' => 'datetime'
    ];

    // Define the relationship with SymptomSession
    public function session()
    {
        return $this->belongsTo(SymptomSession::class, 'session_id');
    }

    public function bodyRegion()
    {
        return $this->belongsTo(BodyRegion::class);
    }

    public function aiResponse()
    {
        return $this->hasOne(AiResponse::class, 'symptom_entry_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check for emergency keywords in symptom text
    public function isEmergencyKeywordDetected()
    {
        $emergencyKeywords = [
            'chest pain', 'difficulty breathing', 'shortness of breath',
            'severe bleeding', 'stroke', 'heart attack', 'unconscious',
            'severe allergic reaction', 'anaphylaxis', 'seizure'
        ];

        $text = strtolower($this->symptom_text);

        foreach ($emergencyKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function getEmergencyKeywordMatched()
    {
        $emergencyKeywords = [
            'chest pain', 'difficulty breathing', 'shortness of breath',
            'severe bleeding', 'stroke', 'heart attack', 'unconscious',
            'severe allergic reaction', 'anaphylaxis', 'seizure'
        ];

        $text = strtolower($this->symptom_text);

        foreach ($emergencyKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return $keyword;
            }
        }

        return null;
    }
}
