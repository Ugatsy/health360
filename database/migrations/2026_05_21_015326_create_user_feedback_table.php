<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ai_response_id')->constrained('ai_responses');
            $table->boolean('was_helpful')->nullable();
            $table->boolean('was_accurate')->nullable();
            $table->text('feedback_text')->nullable();
            $table->boolean('consulted_actual_doctor')->default(false);
            $table->boolean('doctor_diagnosis_matched_ai')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('ai_response_id');
            $table->index('was_helpful');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_feedback');
    }
};
