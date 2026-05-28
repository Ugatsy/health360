<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('name')->nullable();

            // Demographics
            $table->date('date_of_birth')->nullable();
            $table->enum('biological_sex', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'unknown'])->nullable();

            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // Role management (for doctor collaboration)
            $table->enum('role', ['patient', 'doctor', 'admin'])->default('patient');
            $table->string('doctor_license_number')->nullable();
            $table->string('doctor_specialty')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index('email');
            $table->index('uuid');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
