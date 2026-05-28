<?php

namespace App\Models;

use App\Traits\HasEmergencySms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, HasEmergencySms, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'date_of_birth',
        'biological_sex',
        'blood_type',
        'role',
        'doctor_license_number',
        'doctor_specialty'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    // ========== Boot ==========

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // ========== Relationships ==========

    public function medicalProfile()
    {
        return $this->hasOne(MedicalProfile::class);
    }

    public function symptomSessions()
    {
        return $this->hasMany(SymptomSession::class);
    }

    public function symptomEntries()
    {
        return $this->hasMany(SymptomEntry::class);
    }

    public function doctorReviews()
    {
        return $this->hasMany(DoctorReview::class, 'doctor_id');
    }

    public function emergencyAlerts()
    {
        return $this->hasMany(EmergencyAlert::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function feedback()
    {
        return $this->hasMany(UserFeedback::class);
    }

    public function aiResponsesReviewed()
    {
        return $this->hasMany(AiResponse::class, 'reviewed_by_doctor_id');
    }

    // ========== Scopes ==========

    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    public function scopePatients($query)
    {
        return $query->where('role', 'patient');
    }

    // ========== Helpers ==========

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isPatient()
    {
        return $this->role === 'patient';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getFullNameAttribute()
    {
        return $this->name ?? explode('@', $this->email)[0];
    }

    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) return null;
        return $this->date_of_birth->age;
    }
}
