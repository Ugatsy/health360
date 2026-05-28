<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed body regions first
        $this->call(BodyRegionsSeeder::class);

        // Create test patient
        $patient = User::create([
            'uuid' => Str::uuid(),
            'name' => 'John Patient',
            'email' => 'patient@example.com',
            'password' => bcrypt('password'),
            'role' => 'patient',
            'date_of_birth' => '1990-01-01',
            'biological_sex' => 'male',
        ]);

        // Create test doctor
        $doctor = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Dr. Sarah Smith',
            'email' => 'doctor@example.com',
            'password' => bcrypt('password'),
            'role' => 'doctor',
            'doctor_license_number' => 'DOC123456',
            'doctor_specialty' => 'General Practice',
        ]);

        // Create medical profile for patient
        $patient->medicalProfile()->create([
            'has_asthma' => true,
            'allergies' => json_encode([['substance' => 'penicillin', 'reaction' => 'rash']]),
            'consent_to_ai_processing' => true,
            'consent_to_store_symptoms' => true,
        ]);

        $this->command->info('✅ Seeded successfully!');
        $this->command->info('Patient: patient@example.com / password');
        $this->command->info('Doctor: doctor@example.com / password');
    }
}
