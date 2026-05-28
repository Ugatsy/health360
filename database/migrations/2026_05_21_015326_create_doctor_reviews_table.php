<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users');
            $table->foreignId('ai_response_id')->constrained('ai_responses');
            $table->enum('review_decision', ['approved', 'modified', 'rejected', 'flagged_for_human']);
            $table->text('review_notes')->nullable();
            $table->text('modified_remedies')->nullable();
            $table->enum('modified_risk_level', ['emergency', 'high', 'medium', 'low'])->nullable();
            $table->text('modified_advice')->nullable();
            $table->string('doctor_license_number', 50)->nullable();
            $table->string('doctor_license_state', 100)->nullable();
            $table->timestamp('reviewed_at')->useCurrent();
            $table->timestamps();

            $table->index('doctor_id');
            $table->index('ai_response_id');
            $table->index('review_decision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_reviews');
    }
};
