<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('has_heart_condition')->default(false);
            $table->boolean('has_diabetes')->default(false);
            $table->boolean('has_high_blood_pressure')->default(false);
            $table->boolean('has_asthma')->default(false);
            $table->boolean('has_autoimmune_disorder')->default(false);
            $table->json('allergies')->nullable();
            $table->json('current_medications')->nullable();
            $table->boolean('consent_to_store_symptoms')->default(false);
            $table->boolean('consent_to_ai_processing')->default(false);
            $table->boolean('consent_to_share_with_doctor')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_profiles');
    }
};
