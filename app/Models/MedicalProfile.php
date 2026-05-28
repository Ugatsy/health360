<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalProfile extends Model
{
    protected $fillable = [
        'user_id',
        'has_heart_condition',
        'has_diabetes',
        'has_high_blood_pressure',
        'has_asthma',
        'has_autoimmune_disorder',
        'allergies',
        'current_medications',
        'consent_to_store_symptoms',
        'consent_to_ai_processing',
        'consent_to_share_with_doctor'
    ];

    protected $casts = [
        'has_heart_condition' => 'boolean',
        'has_diabetes' => 'boolean',
        'has_high_blood_pressure' => 'boolean',
        'has_asthma' => 'boolean',
        'has_autoimmune_disorder' => 'boolean',
        'allergies' => 'array',
        'current_medications' => 'array',
        'consent_to_store_symptoms' => 'boolean',
        'consent_to_ai_processing' => 'boolean',
        'consent_to_share_with_doctor' => 'boolean',
    ];

    // ========== Relationships ==========

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== Helpers ==========

    public function hasAllergyTo($substance)
    {
        if (!$this->allergies) return false;
        return collect($this->allergies)->contains('substance', $substance);
    }

    public function getCriticalConditionsList()
    {
        $conditions = [];
        if ($this->has_heart_condition) $conditions[] = 'Heart Condition';
        if ($this->has_diabetes) $conditions[] = 'Diabetes';
        if ($this->has_high_blood_pressure) $conditions[] = 'High Blood Pressure';
        if ($this->has_asthma) $conditions[] = 'Asthma';
        if ($this->has_autoimmune_disorder) $conditions[] = 'Autoimmune Disorder';
        return $conditions;
    }

    public function hasGivenConsent()
    {
        return $this->consent_to_store_symptoms &&
               $this->consent_to_ai_processing;
    }
}
