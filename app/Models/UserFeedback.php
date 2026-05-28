<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFeedback extends Model
{
    protected $fillable = [
        'user_id',
        'ai_response_id',
        'was_helpful',
        'was_accurate',
        'feedback_text',
        'consulted_actual_doctor',
        'doctor_diagnosis_matched_ai'
    ];

    protected $casts = [
        'was_helpful' => 'boolean',
        'was_accurate' => 'boolean',
        'consulted_actual_doctor' => 'boolean',
        'doctor_diagnosis_matched_ai' => 'boolean',
    ];

    // ========== Relationships ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aiResponse()
    {
        return $this->belongsTo(AiResponse::class);
    }

    // ========== Scopes ==========

    public function scopeHelpful($query)
    {
        return $query->where('was_helpful', true);
    }

    public function scopeNotHelpful($query)
    {
        return $query->where('was_helpful', false);
    }

    public function scopeAccurate($query)
    {
        return $query->where('was_accurate', true);
    }

    public function scopeConsultedDoctor($query)
    {
        return $query->where('consulted_actual_doctor', true);
    }

    // ========== Accessors ==========

    public function getAccuracyRateAttribute()
    {
        if (!$this->consulted_actual_doctor || $this->doctor_diagnosis_matched_ai === null) {
            return null;
        }
        return $this->doctor_diagnosis_matched_ai ? '✅ Matched' : '❌ Did not match';
    }

    public function getHelpfulnessLabelAttribute()
    {
        if ($this->was_helpful === null) return 'Not rated';
        return $this->was_helpful ? '👍 Helpful' : '👎 Not helpful';
    }
}
