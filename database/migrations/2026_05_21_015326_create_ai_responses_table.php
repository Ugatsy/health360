<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('symptom_entry_id')->constrained('symptom_entries')->onDelete('cascade');
            $table->json('raw_ai_response');
            $table->json('possible_explanations')->nullable();
            $table->json('home_remedies')->nullable();
            $table->text('when_to_see_doctor')->nullable();
            $table->enum('ai_risk_level', ['emergency', 'high', 'medium', 'low'])->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('web_sources')->nullable();
            $table->foreignId('reviewed_by_doctor_id')->nullable()->constrained('users');
            $table->boolean('doctor_approved')->default(false);
            $table->text('doctor_modified_response')->nullable();
            $table->timestamp('doctor_reviewed_at')->nullable();
            $table->timestamps();

            $table->index('symptom_entry_id');
            $table->index('ai_risk_level');
            $table->index('doctor_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_responses');
    }
};
