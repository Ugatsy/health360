<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorReview extends Model
{
    protected $fillable = [
        'doctor_id',
        'ai_response_id',
        'review_decision',
        'review_notes',
        'modified_remedies',
        'modified_risk_level',
        'modified_advice',
        'doctor_license_number',
        'doctor_license_state',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ========== Relationships ==========

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function aiResponse()
    {
        return $this->belongsTo(AiResponse::class);
    }

    // ========== Scopes ==========

    public function scopeApproved($query)
    {
        return $query->where('review_decision', 'approved');
    }

    public function scopeModified($query)
    {
        return $query->where('review_decision', 'modified');
    }

    public function scopeRejected($query)
    {
        return $query->where('review_decision', 'rejected');
    }

    public function scopeFlagged($query)
    {
        return $query->where('review_decision', 'flagged_for_human');
    }

    // ========== Accessors ==========

    public function getReviewDecisionBadgeAttribute()
    {
        return match($this->review_decision) {
            'approved' => '✅ Approved',
            'modified' => '✏️ Modified',
            'rejected' => '❌ Rejected',
            'flagged_for_human' => '⚠️ Flagged',
            default => '⚪ Unknown',
        };
    }
}
