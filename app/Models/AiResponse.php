<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiResponse extends Model
{
    protected $fillable = [
        'symptom_entry_id',
        'raw_ai_response',
        'possible_explanations',
        'home_remedies',
        'when_to_see_doctor',
        'ai_risk_level',
        'risk_factors',
        'web_sources',
        'reviewed_by_doctor_id',
        'doctor_approved',
        'doctor_modified_response',
        'doctor_reviewed_at'
    ];

    protected $casts = [
        'raw_ai_response' => 'array',
        'possible_explanations' => 'array',
        'home_remedies' => 'array',
        'risk_factors' => 'array',
        'web_sources' => 'array',
        'doctor_approved' => 'boolean',
        'doctor_reviewed_at' => 'datetime',
    ];

    // ========== Relationships ==========

    public function symptomEntry()
    {
        return $this->belongsTo(SymptomEntry::class);
    }

    public function reviewedByDoctor()
    {
        return $this->belongsTo(User::class, 'reviewed_by_doctor_id');
    }

    public function doctorReviews()
    {
        return $this->hasMany(DoctorReview::class);
    }

    public function feedback()
    {
        return $this->hasMany(UserFeedback::class);
    }

    // ========== Scopes ==========

    public function scopeEmergency($query)
    {
        return $query->where('ai_risk_level', 'emergency');
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('ai_risk_level', ['emergency', 'high']);
    }

    public function scopeLowRisk($query)
    {
        return $query->where('ai_risk_level', 'low');
    }

    public function scopePendingDoctorReview($query)
    {
        return $query->where('doctor_approved', false)
                     ->whereIn('ai_risk_level', ['high', 'emergency']);
    }

    public function scopeApprovedByDoctor($query)
    {
        return $query->where('doctor_approved', true);
    }

    // ========== Methods ==========

    public function needsDoctorReview()
    {
        return in_array($this->ai_risk_level, ['high', 'emergency']) && !$this->doctor_approved;
    }

    public function approveByDoctor($doctorId, $modifiedResponse = null)
    {
        $this->update([
            'reviewed_by_doctor_id' => $doctorId,
            'doctor_approved' => true,
            'doctor_modified_response' => $modifiedResponse,
            'doctor_reviewed_at' => now()
        ]);
    }

    public function getFinalResponseAttribute()
    {
        return $this->doctor_modified_response ?? $this->when_to_see_doctor;
    }

    public function getRiskLevelBadgeAttribute()
    {
        return match($this->ai_risk_level) {
            'emergency' => '🚨 EMERGENCY',
            'high' => '🔴 High Risk',
            'medium' => '🟡 Medium Risk',
            'low' => '🟢 Low Risk',
            default => '⚪ Unknown',
        };
    }

    public function getAverageHelpfulnessAttribute()
    {
        return $this->feedback()->whereNotNull('was_helpful')->avg('was_helpful');
    }
}
